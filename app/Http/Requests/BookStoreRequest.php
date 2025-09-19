<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TimeSlot;
use App\Enums\BookStatus;

class BookStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id,deleted_at,NULL'],
            'date' => ['required', 'integer', 'min:0'],
            'time_slot' => ['required', new Enum(TimeSlot::class)],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
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
