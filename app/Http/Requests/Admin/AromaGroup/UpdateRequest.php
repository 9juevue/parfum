<?php

namespace App\Http\Requests\Admin\AromaGroup;

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
            'id' => 'required|exists:aroma_groups,id',
            'title' => 'required|string|max:25',
            'short_description' => 'required|string',
            'seo_title' => 'nullable|string|max:255',
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

            'color.required' => 'Цвет группы ароматов обязателен',

            'short_description.required' => 'Краткое описание обязательно',

            'seo_title.string' => 'SEO-заголовок должен быть строкой.',
            'seo_title.max' => 'SEO-заголовок не должен превышать 255 символов.',

            'seo_description.string' => 'SEO-описание должно быть строкой.',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',
        ];
    }
}
