<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\EmployeePosition;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:employees,email,' . $id],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'unique:employees,phone,' . $id],
            'position' => ['sometimes', 'required', new Enum(EmployeePosition::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email already registered',
            'required' => ':attribute is required',
        ];
    }
}
