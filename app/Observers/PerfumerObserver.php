<?php

namespace App\Observers;

use App\Enums\Page\PageTemplateEnum;
use App\Models\Perfumer;

class PerfumerObserver
{
    public function created(Perfumer $perfumer): void
    {
        $perfumer->page()->create([
            'slug' => $perfumer->slug,
            'template' => PageTemplateEnum::PERFUMER->value,
        ]);
    }

    public function updated(Perfumer $perfumer): void
    {
        $perfumer->page->update([
            'slug' => $perfumer->slug,
        ]);
    }

    public function deleting(Perfumer $perfumer): void
    {
        $perfumer->page()->delete();
    }
}
