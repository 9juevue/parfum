<?php

namespace App\Http\Requests\Admin\Perfumer;

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
            'id' => 'required|exists:perfumers',
            'title' => 'required|string|max:25',
            'slug' => 'nullable|string|regex:/^[a-z0-9-]+$/|unique:perfumers,slug,' . $this->input('id'),
            'short_description' => 'required|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'country_id' => 'required|exists:countries,id',

            'files' => 'required|array|max:1',
            'files.*.url' => 'required|string',
            'files.*.sort' => 'required|integer',

            'synonyms' => 'nullable|array',
            'synonyms.*.title' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название страны обязательно',
            'title.string' => 'Название должно быть строкой',
            'title.max' => 'Название не может превышать 25 символов',

            'slug.string' => 'Url должен быть строкой.',
            'slug.regex' => 'Url может содержать только латинские буквы, цифры и символ "-".',
            'slug.unique' => 'Такой url уже используется',

            'short_description.required' => 'Краткое описание обязательно',

            'seo_title.string' => 'SEO-заголовок должен быть строкой.',
            'seo_title.max' => 'SEO-заголовок не должен превышать 255 символов.',

            'seo_description.string' => 'SEO-описание должно быть строкой.',

            'country_id.required' => 'Страна обязательна.',
            'country_id.exists' => 'Выбранная страна не существует.',

            'files.required' => 'Изображение обязательное',
            'files.max' => 'Можно загрузить не более 1 изображения',

            'synonyms.*.title.required' => 'Поле должно быть заполнено',
            'synonyms.*.title.max' => 'Синоним не может превышать 255 символов'
        ];
    }
}
