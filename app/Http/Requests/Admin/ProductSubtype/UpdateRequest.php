<?php

namespace App\Http\Requests\Admin\ProductSubtype;

use App\Enums\Product\ProductCategoryEnum;
use App\Enums\Product\ProductSubtypeFormatEnum;
use App\Enums\Product\ProductSubtypeTypeEnum;
use App\Models\Product;
use App\Models\ProductSubtypeFormat;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $productId = $this->input('product_id');
        $product = Product::find($productId);
        $productSubtypeFormatId = $this->input('product_subtype_format_id');
        $productSubtypeFormat = ProductSubtypeFormat::find($productSubtypeFormatId);

        if ($productSubtypeFormat) {
            switch ($productSubtypeFormat->product_subtype_format) {
                case ProductSubtypeFormatEnum::Remainder:
                    $this->merge([
                        'volume_text' => null
                    ]);
                    break;
                case ProductSubtypeFormatEnum::Set:
                    $this->merge([
                        'bottle_volume' => null
                    ]);
                    break;
                default:
                    $this->merge([
                        'bottle_volume' => null,
                        'volume_text' => null
                    ]);
                    break;
            }
        }



        $this->merge([
            'product' => $product,
            'product_subtype_format' => $productSubtypeFormat,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $product = $this->input('product');
            $productSubtypeFormat = $this->input('product_subtype_format');

            if ($product->category_type == ProductCategoryEnum::Product) {
                if (!$this->input('product_subtype_format_id')) {
                    $validator->errors()->add(
                        'product_subtype_format_id',
                        'Формат подвида обязателен'
                    );
                }
                if (!$this->input('volume')) {
                    $validator->errors()->add(
                        'volume',
                        'Объём обязателен'
                    );
                }

                if ($productSubtypeFormat) {
                    if ($productSubtypeFormat->product_subtype_format === ProductSubtypeFormatEnum::Remainder) {
                        if (!$this->input('bottle_volume')) {
                            $validator->errors()->add(
                                'bottle_volume',
                                'Объём обязателен'
                            );
                        }
                    }

                    if ($productSubtypeFormat->product_subtype_format === ProductSubtypeFormatEnum::Set) {
                        if (!$this->input('volume_text')) {
                            $validator->errors()->add(
                                'volume_text',
                                'Объём обязателен'
                            );
                        }
                    }
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:product_subtypes,id',
            'product_subtype_type' => 'required|integer|in:' . implode(',', ProductSubtypeTypeEnum::values()),
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric',
            'qty' => 'required|numeric',

            'product_subtype_format_id' => 'nullable|exists:product_subtype_formats,id',
            'volume' => 'nullable|numeric',
            'bottle_volume' => 'nullable|numeric',
            'volume_text' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'                          => 'ID подвида продукта обязателен',
            'id.exists'                            => 'Указанный подвид продукта не найден',

            'product_subtype_type.required'       => 'Тип подвида обязателен',
            'product_subtype_type.integer'        => 'Тип подвида должен быть числом',
            'product_subtype_type.in'             => 'Недопустимый тип подвида',

            'product_id.required'                 => 'ID продукта обязателен',
            'product_id.exists'                   => 'Продукт с таким ID не найден',

            'price.required'                      => 'Стоимость обязательна',
            'price.numeric'                       => 'Стоимость должна быть числовым значением',

            'qty.required'                        => 'Количество на складе обязательно',
            'qty.numeric'                         => 'Количество должно быть числовым значением',

            'product_subtype_format_id.exists'    => 'Формат подвида не найден',

            'volume.numeric'                      => 'Объём должен быть числовым значением',

            'bottle_volume.numeric'               => 'Объём флакона должен быть числовым значением',

            'volume_text.string'                  => 'Текстовый объём должен быть строкой',
        ];
    }
}
