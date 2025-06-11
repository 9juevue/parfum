<?php

namespace App\Http\Requests\Admin\NoteGrouping;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название на русском страны обязательно',
            'title.string' => 'Название на русском должно быть строкой',
        ];
    }
}
