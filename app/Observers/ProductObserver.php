<?php

namespace App\Observers;

use App\Enums\Page\PageTemplateEnum;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        $product->page()->create([
            'slug' => $product->slug,
            'template' => PageTemplateEnum::PRODUCT->value,
        ]);
    }


    public function updated(Product $product): void
    {
        $product->page->update([
            'slug' => $product->slug,
        ]);
    }

    public function deleting(Product $product): void
    {
        $product->page()->delete();
    }
}
