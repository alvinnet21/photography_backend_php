<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\TimeSlot;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class BookService
{
    public function isAvailable(int $employeeId, int $date, string $timeSlot, ?int $exceptId = null): bool
    {
        $slot = is_string($timeSlot) ? $timeSlot : (string) $timeSlot;
        $conflictSlots = [];
        if ($slot === TimeSlot::FULL_DAY->value) {
            // Full day requires no bookings at all that day
            $conflictSlots = [TimeSlot::MORNING->value, TimeSlot::AFTERNOON->value, TimeSlot::FULL_DAY->value];
        } elseif ($slot === TimeSlot::MORNING->value) {
            $conflictSlots = [TimeSlot::MORNING->value, TimeSlot::FULL_DAY->value];
        } else { // AFTERNOON
            $conflictSlots = [TimeSlot::AFTERNOON->value, TimeSlot::FULL_DAY->value];
        }

        $query = Book::query()
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->whereIn('time_slot', $conflictSlots)
            ->whereIn('status', [
                BookStatus::PENDING->value,
                BookStatus::ACCEPTED->value,
                BookStatus::FINISHED->value,
            ]);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return !$query->exists();
    }

    public function availabilityRange(int $employeeId, string $startDate, int $days = 180, ?int $exceptId = null): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = (clone $start)->addDays($days - 1);
        $startTs = $start->getTimestamp();
        $endTs = $end->getTimestamp();

        $query = Book::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$startTs, $endTs])
            ->whereIn('status', [
                BookStatus::PENDING->value,
                BookStatus::ACCEPTED->value,
                BookStatus::FINISHED->value,
            ]);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        $rows = $query
            ->selectRaw('date as d, time_slot, COUNT(*) as c')
            ->groupBy('d', 'time_slot')
            ->get();

        $byDate = [];
        foreach ($rows as $r) {
            $slotKey = is_string($r->time_slot)
                ? $r->time_slot
                : ($r->time_slot->value ?? (string) $r->time_slot);
            $byDate[(int) $r->d][$slotKey] = (int) $r->c;
        }

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = (clone $start)->addDays($i);
            $dateKey = $day->getTimestamp();
            $has = $byDate[$dateKey] ?? [];

            $hasFull = isset($has[TimeSlot::FULL_DAY->value]);
            $hasMorning = isset($has[TimeSlot::MORNING->value]);
            $hasAfternoon = isset($has[TimeSlot::AFTERNOON->value]);

            $available = [];
            if (!$hasFull && !$hasMorning) {
                $available[] = TimeSlot::MORNING->value;
            }
            if (!$hasFull && !$hasAfternoon) {
                $available[] = TimeSlot::AFTERNOON->value;
            }
            if (!$hasFull && !$hasMorning && !$hasAfternoon) {
                $available[] = TimeSlot::FULL_DAY->value;
            }

            $out[] = [
                'date' => $day->getTimestamp(),
                'available_slots' => $available,
            ];
        }

        return $out;
    }

    public function list(int $perPage = 20, ?int $dateFrom = null, ?int $dateTo = null): LengthAwarePaginator
    {
        return Book::query()
            ->with('employee')
            ->when($dateFrom !== null && $dateTo !== null, function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            })
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Book
    {
        return Book::query()->with('employee')->findOrFail($id);
    }

    public function create(array $data): Book
    {
        // Always set status to PENDING on create
        $status = BookStatus::PENDING->value;
        $normalizedDate = Carbon::createFromTimestamp((int) $data['date'])->startOfDay()->getTimestamp();
        $this->assertAvailability(
            employeeId: (int) $data['employee_id'],
            date: $normalizedDate,
            timeSlot: $data['time_slot']
        );

        $book = new Book();
        $book->employee_id = $data['employee_id'];
        $book->date = $normalizedDate;
        $book->time_slot = $data['time_slot'];
        $book->status = $status;
        $book->customer_name = $data['customer_name'] ?? null;
        $book->customer_email = $data['customer_email'] ?? null;
        $book->customer_phone = $data['customer_phone'] ?? null;
        $book->save();
        return $book->fresh(['employee']);
    }

    public function update(int $id, array $data): Book
    {
        $book = $this->findOrFail($id);

        if (!array_key_exists('status', $data)) {
            throw new \InvalidArgumentException('status is required');
        }

        $current = $book->status?->value ?? $book->status;
        $next = is_string($data['status']) ? $data['status'] : ($data['status']->value ?? (string) $data['status']);

        $allowed = [];
        if ($current === BookStatus::PENDING->value) {
            $allowed = [BookStatus::ACCEPTED->value, BookStatus::REJECTED->value];
        } elseif ($current === BookStatus::ACCEPTED->value) {
            $allowed = [BookStatus::FINISHED->value];
        }

        if (!in_array($next, $allowed, true)) {
            throw new \LogicException('Transisi status tidak valid.');
        }

        $book->status = $next;
        $book->save();
        return $book->fresh(['employee']);
    }

    public function delete(int $id): void
    {
        $book = $this->findOrFail($id);
        $book->delete();
    }

    private function assertAvailability(int $employeeId, int $date, string $timeSlot, ?int $exceptId = null): void
    {
        if (!$this->isAvailable($employeeId, $date, $timeSlot, $exceptId)) {
            // Slot conflict for this employee
            throw new \LogicException('Tanggal dan time slot sudah terpakai untuk employee ini.');
        }
    }
}
