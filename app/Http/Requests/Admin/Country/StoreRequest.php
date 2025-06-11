<?php

namespace App\Http\Requests\Admin\Country;

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
            'title' => 'required|string|max:25',

            'files' => 'required|array|max:1',
            'files.*.url' => 'required|string',
            'files.*.sort' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название страны обязательно',
            'title.string' => 'Название должно быть строкой',
            'title.max' => 'Название не может превышать 25 символов',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',
        ];
    }
}
