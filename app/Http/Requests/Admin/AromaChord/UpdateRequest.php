<?php

namespace App\Http\Requests\Admin\AromaChord;

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
            'id' => 'required|exists:aroma_chords,id',
            'title' => 'required|string|max:25',
            'short_description' => 'required|string',
            'seo_description' => 'nullable|string',

            'color' => 'required|string',

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

            'color.required' => 'Цвет аккорда обязателен',

            'short_description.required' => 'Краткое описание обязательно',

            'seo_description.string' => 'SEO-описание должно быть строкой.',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',
        ];
    }
}
