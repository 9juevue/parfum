<?php

namespace App\Http\Requests\Admin\AromaNote;

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
            'id' => 'required|exists:aroma_notes,id',
            'title_ru' => 'required|string',
            'title_en' => 'required|string',
            'color' => 'required|string',
            'note_grouping_id' => 'required|exists:note_groupings,id',
            'short_description' => 'required|string',
            'seo_description' => 'nullable|string',

            'files' => 'required|array|max:1',
            'files.*.url' => 'required|string',
            'files.*.sort' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title_ru.required' => 'Название на русском страны обязательно',
            'title_ru.string' => 'Название на русском должно быть строкой',

            'title_en.required' => 'Название на английском страны обязательно',
            'title_en.string' => 'Название на английском должно быть строкой',

            'note_grouping_id.required' => 'Выберите группировку нот',
            'note_grouping_id.exists' => 'Выбранная группировка не существует',

            'color.required' => 'Цвет аккорда обязателен',

            'short_description.required' => 'Краткое описание обязательно',

            'seo_description.string' => 'SEO-описание должно быть строкой.',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',
        ];
    }
}
