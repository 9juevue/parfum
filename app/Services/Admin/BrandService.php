<?php

namespace App\Services\Admin;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Throwable;

class BrandService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): Brand
    {
        return DB::transaction(function () use ($data) {
            $brand = Brand::query()->create($data);

            $brand->attachMediaFiles($data['files'], 'image');

            if (!empty($data['synonyms'])) {
                $brand->synonyms()->createMany($data['synonyms']);
            }

            return $brand;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): Brand
    {
        return DB::transaction(function () use ($id, $data) {
            $brand = Brand::query()->findOrFail($id);

            $brand->update($data);

            $brand->synonyms()->delete();
            if (!empty($data['synonyms'])) {
                $brand->synonyms()->createMany($data['synonyms']);
            }

            $brand->attachMediaFiles($data['files'], 'image');

            return $brand;
        });
    }

    public function delete(string $id): bool
    {
        $brand = Brand::query()->findOrFail($id);

        return $brand->delete();
    }
}
