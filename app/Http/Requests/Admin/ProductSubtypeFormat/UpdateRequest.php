<?php

namespace App\Http\Requests\Admin\ProductSubtypeFormat;

use App\Enums\Product\ProductSubtypeFormatEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:product_subtype_formats,id',
            'product_subtype_format' => 'required|integer|in:' . implode(',', ProductSubtypeFormatEnum::values()) . '|unique:product_subtype_formats,product_subtype_format,' . $this->input('id'),
            'description' => 'required|string',
            'hint' => 'required|string',

            'files' => 'required|array|max:1',
            'files.*.url' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_subtype_format.required' => 'Название обязательно',
            'product_subtype_format.in' => 'Выберите существующий вариант',
            'product_subtype_format.unique' => 'Такой вариант уже используется',

            'description.required' => 'Описание обязательно',
            'hint.required' => 'Текст о типе обязателен',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',
        ];
    }
}
