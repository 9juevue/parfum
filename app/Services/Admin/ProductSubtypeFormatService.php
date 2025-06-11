<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Models\ProductSubtypeFormat;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductSubtypeFormatService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): ProductSubtypeFormat
    {
        return DB::transaction(function () use ($data) {
            $productSubtypeFormat = ProductSubtypeFormat::query()->create($data);

            $productSubtypeFormat->attachMediaFiles($data['files'], 'image');

            return $productSubtypeFormat;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): ProductSubtypeFormat
    {
        return DB::transaction(function () use ($id, $data) {
            $productSubtypeFormat = ProductSubtypeFormat::query()->findOrFail($id);

            $productSubtypeFormat->update($data);

            $productSubtypeFormat->attachMediaFiles($data['files'], 'image');

            return $productSubtypeFormat;
        });
    }

    public function delete(string $id): bool
    {
        $productSubtypeFormat = ProductSubtypeFormat::query()->findOrFail($id);

        return $productSubtypeFormat->delete();
    }
}
