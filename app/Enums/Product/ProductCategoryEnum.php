<?php

namespace App\Enums\Product;

use Illuminate\Support\Str;

enum ProductCategoryEnum: int
{
    case Product = 1;
    case ParfumBox = 2;
    case GiftSet = 3;

    public static function names(): array
    {
        return array_map('strtolower', array_column(self::cases(), 'name'));
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Product => 'Продукт',
            self::ParfumBox => 'Parfum Box',
            self::GiftSet => 'Подарочный набор',
        };
    }

    public static function map(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function labels(): array
    {
        $values = self::values();
        $labels = array_map(
            fn(ProductCategoryEnum $case) => $case->label(),
            self::cases()
        );

        return array_combine($values, $labels);
    }

    public static function collection(): array
    {
        $array = array();

        foreach (self::labels() as $value => $title) {
            $array[] = [
                'title' => $title,
                'value' => $value,
            ];
        }

        return $array;
    }

    public static function optionsByKey(): array
    {
        $object = [];

        foreach (self::cases() as $case) {
            $key = Str::camel(mb_strtolower($case->name));

            $object[$key] = [
                'title' => $case->label(),
                'value' => $case->value,
            ];
        }

        return $object;
    }
}
