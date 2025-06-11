<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BreadcrumbService
{
    public function make(Model $model): array
    {
        $root = [
            'label' => 'Каталог',
            'url' => route('admin.products.index'),
        ];

        return match (true) {
            $model instanceof Product => collect()->push($root)->push($this->makeForProduct($model))->toArray(),
            default => array()
        };
    }

    protected function makeForProduct(Product $product): array
    {
        return [
            'label' => $product->title,
            'url' => route('admin.products.edit', $product->id),
        ];
    }
}
