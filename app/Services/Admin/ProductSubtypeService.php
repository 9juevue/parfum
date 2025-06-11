<?php

namespace App\Services\Admin;

use App\Models\ProductSubtype;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductSubtypeService
{
    public function store(array $data): ProductSubtype
    {
        return ProductSubtype::query()->create($data);
    }

    public function update(string $id, array $data): ProductSubtype
    {
        $productSubtype = ProductSubtype::query()->findOrFail($id);

        $productSubtype->update($data);

        return $productSubtype;
    }

    public function delete(string $id): bool
    {
        $productSubtype = ProductSubtype::query()->findOrFail($id);

        return $productSubtype->delete();
    }
}
