<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\EmployeePosition;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:employees,phone'],
            'position' => ['required', new Enum(EmployeePosition::class)],
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
