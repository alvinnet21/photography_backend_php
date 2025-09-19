<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id,deleted_at,NULL'],
        ];
    }

    public function messages(): array
    {
        return [
            'exists' => 'Data not found',
            'required' => ':attribute is required',
        ];
    }
}
