<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Services\EmployeeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends ApiController
{
    public function __construct(private readonly EmployeeService $service)
    {
    }

    public function index()
    {
        $list = $this->service->list(20);
        return $this->paginated($list);
    }

    public function show(int $id)
    {
        try {
            $employee = $this->service->findOrFail($id);
            return $this->success($employee);
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        }
    }

    public function store(EmployeeStoreRequest $request)
    {
        $employee = $this->service->create($request->validated());
        return $this->success($employee, null, 201);
    }

    public function update(int $id, EmployeeUpdateRequest $request)
    {
        try {
            $employee = $this->service->update($id, $request->validated());
            return $this->success($employee);
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->noContent();
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        }
    }
}
