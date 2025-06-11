<?php

namespace App\Enums\Product;

use Illuminate\Support\Str;

enum ProductSubtypeTypeEnum: int
{
    case Perfume = 1;
    case EauDeParfum = 2;
    case EauDeToilette = 3;
    case Cologne = 4;

    case ShowerGel = 5;
    case Cream = 6;
    case Deodorant = 7;
    case BodyLotion = 8;

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
            self::Perfume => 'Духи',
            self::EauDeParfum => 'Парфюмерная вода',
            self::EauDeToilette => 'Туалетная вода',
            self::Cologne => 'Одеколон',

            self::ShowerGel => 'Гель для душа',
            self::Cream => 'Крем',
            self::Deodorant => 'Дезодорант',
            self::BodyLotion => 'Лосьон для тела'
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
            fn(ProductSubtypeTypeEnum $case) => $case->label(),
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
