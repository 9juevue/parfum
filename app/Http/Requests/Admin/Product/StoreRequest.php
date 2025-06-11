<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\Product\ProductCategoryEnum;
use App\Enums\Product\ProductGenderEnum;
use App\Enums\Product\ProductSubtypeCategoryEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $categoryType = $this->input('category_type');

        if ($this->has('category_type')) {
            $default = match ($categoryType) {
                ProductCategoryEnum::Product->value => $this->input('subtype_category_type'),
                ProductCategoryEnum::ParfumBox->value => ProductSubtypeCategoryEnum::ParfumBox->value,
                ProductCategoryEnum::GiftSet->value => ProductSubtypeCategoryEnum::GiftSet->value,
                default => null,
            };

            if ($default) {
                $this->merge([
                    'subtype_category_type' => $default,
                ]);
            }

        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:78',
            'slug' => 'nullable|string|regex:/^[a-z0-9-]+$/|unique:products,slug',
            'synonyms' => 'nullable|array',
            'synonyms.*.title' => 'required|string|max:255',
            'sku' => 'required|string|max:25',
            'tag' => 'nullable|string|max:25',
            'description' => 'required|string',
            'category_type' => 'required|integer|in:' . implode(',', ProductCategoryEnum::values()),
            'subtype_category_type' =>
                'required_if:category_type,' . ProductCategoryEnum::Product->value
                . '|integer|in:' . implode(',', ProductSubtypeCategoryEnum::values()),

            'brand_id' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . ',' . ProductCategoryEnum::GiftSet->value,

            'perfumers' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:perfumers,id',
            'year' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1900|max:' . date('Y'),
            'gender_type' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|in:' . implode(',', ProductGenderEnum::values()),
            'video' => 'nullable|string',

            'aroma_groups' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:aroma_groups,id',
            'aroma_chords' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:aroma_chords,id',
            'base_notes' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:aroma_notes,id',
            'middle_notes' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:aroma_notes,id',
            'top_notes' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|array|exists:aroma_notes,id',

            'longevity' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1|max:10',
            'sillage' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1|max:10',
            'season' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1|max:10',
            'time_of_day' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1|max:10',
            'gender' => 'required_if:category_type,' . ProductCategoryEnum::Product->value . '|integer|min:1|max:10',

            'files' => 'required|array|min:1',
            'files.*.url' => 'required|string',
            'files.*.sort' => 'required|integer',
            'files.*.is_favorite' => 'required|boolean',

            'parfum_box_subtypes' => 'required_if:category_type,' . ProductCategoryEnum::ParfumBox->value . '|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название обязательно',
            'title.string' => 'Название должно быть строкой',
            'title.max' => 'Название не может превышать 78 символов',

            'slug.string' => 'Слаг должен быть строкой',
            'slug.regex' => 'Слаг может содержать только латинские буквы, цифры и дефис',

            'synonyms.array' => 'Синонимы должны быть массивом',
            'synonyms.*.title.required' => 'Синоним обязательно должен быть заполнен',
            'synonyms.*.title.string' => 'Синоним должен быть строкой',
            'synonyms.*.title.max' => 'Синоним не может превышать 255 символов',

            'sku.required' => 'Артикул обязателен',
            'sku.string' => 'Артикул должен быть строкой',
            'sku.max' => 'Артикул не может быть длиннее 25 символов',

            'tag.required' => 'Тег обязателен',
            'tag.string' => 'Тег должен быть строкой',
            'tag.max' => 'Тег не может быть длиннее 25 символов',

            'description.required' => 'Описание товара обязательно',
            'description.string' => 'Описание должно быть строкой',

            'category_type.required' => 'Необходимо указать тип категории',
            'category_type.integer' => 'Тип категории должен быть числовым',
            'category_type.in' => 'Недопустимый тип категории',

            'brand_id.required_if' => 'Бренд обязателен для этого типа товара',
            'brand_id.integer' => 'ID бренда должен быть числом',
            'brand_id.exists' => 'Выбранный бренд не найден',

            'perfumers.required_if' => 'Необходимо выбрать хотя бы одного парфюмера',
            'perfumers.array' => 'Список парфюмеров должен быть массивом',
            'perfumers.exists' => 'Один или несколько выбранных парфюмеров не найдены',

            'year.required_if' => 'Укажите год создания для этого типа товара',
            'year.integer' => 'Год создания должен быть числом',
            'year.min' => 'Год создания не может быть раньше 1900',
            'year.max' => 'Год создания не может быть позже ' . date('Y'),

            'gender_type.required_if' => 'Укажите гендер для этого типа товара',
            'gender_type.integer' => 'Тип гендера должен быть числом',
            'gender_type.in' => 'Недопустимый тип гендера',

            'video.string' => 'Видео должно быть строкой (iframe или ID)',

            'aroma_groups.required_if' => 'Необходимо указать группы аромата',
            'aroma_groups.array' => 'Группы аромата должны быть массивом',
            'aroma_groups.exists' => 'Одна или несколько групп аромата не найдены',

            'aroma_chords.required_if' => 'Необходимо указать аккорды аромата',
            'aroma_chords.array' => 'Аккорды аромата должны быть массивом',
            'aroma_chords.exists' => 'Один или несколько выбранных аккордов не найдены',

            'base_notes.required_if' => 'Необходимо указать базовые ноты',
            'base_notes.array' => 'Базовые ноты должны быть массивом',
            'base_notes.exists' => 'Одна или несколько выбранных базовых нот не найдены',

            'middle_notes.required_if' => 'Необходимо указать средние ноты',
            'middle_notes.array' => 'Средние ноты должны быть массивом',
            'middle_notes.exists' => 'Одна или несколько выбранных средних нот не найдены',

            'top_notes.required_if' => 'Необходимо указать верхние ноты',
            'top_notes.array' => 'Верхние ноты должны быть массивом',
            'top_notes.exists' => 'Одна или несколько выбранных верхних нот не найдены',

            'sillage.required_if' => 'Укажите стойкость аромата',
            'sillage.integer' => 'Стойкость должна быть числом',
            'sillage.min' => 'Минимальное значение стойкости — 1',
            'sillage.max' => 'Максимальное значение стойкости — 10',

            'season.required_if' => 'Укажите сезон для аромата',
            'season.integer' => 'Сезон должен быть числом',
            'season.min' => 'Минимальное значение сезона — 1',
            'season.max' => 'Максимальное значение сезона — 10',

            'time_of_day.required_if' => 'Укажите время дня для аромата',
            'time_of_day.integer' => 'Время дня должно быть числом',
            'time_of_day.min' => 'Минимальное значение времени дня — 1',
            'time_of_day.max' => 'Максимальное значение времени дня — 10',

            'gender.required_if' => 'Укажите гендер для аромата',
            'gender.integer' => 'Гендер должен быть числом',
            'gender.min' => 'Минимальное значение гендера — 1',
            'gender.max' => 'Максимальное значение гендера — 10',

            'files.required' => 'Необходимо загрузить хотя бы один файл',
            'files.array' => 'Файлы должны быть массивом',
            'files.min' => 'Хотя бы один файл обязателен',

            'files.*.url.required' => 'URL изображения обязателен',
            'files.*.url.string' => 'URL изображения должен быть строкой',

            'files.*.sort.required' => 'Порядок сортировки обязателен',
            'files.*.sort.integer' => 'Порядок сортировки должен быть числом',

            'files.*.is_favorite.required' => 'Укажите, является ли изображение фаворитом',
            'files.*.is_favorite.boolean' => 'Поле «избранное» должно быть булевым',
        ];
    }
}
