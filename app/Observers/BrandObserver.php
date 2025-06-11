<?php

namespace App\Observers;

use App\Enums\Page\PageTemplateEnum;
use App\Models\Brand;

class BrandObserver
{
    public function created(Brand $brand): void
    {
        $brand->page()->create([
            'slug' => $brand->slug,
            'template' => PageTemplateEnum::BRAND->value,
        ]);
    }

    public function updated(Brand $brand): void
    {
        $brand->page->update([
            'slug' => $brand->slug,
        ]);
    }

    public function deleting(Brand $brand): void
    {
        $brand->products->each->delete();
        $brand->page()->delete();
    }
}
