<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return Employee::query()
            ->orderBy('id', 'asc')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Employee
    {
        return Employee::query()->findOrFail($id);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $employee = $this->findOrFail($id);
        $employee->update($data);
        return $employee;
    }

    public function delete(int $id): void
    {
        $employee = $this->findOrFail($id);
        $employee->delete();
    }
}
