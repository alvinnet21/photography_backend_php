<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TimeSlot;
use App\Enums\BookStatus;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(BookStatus::class)],
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
