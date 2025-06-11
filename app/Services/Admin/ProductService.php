<?php

namespace App\Services\Admin;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::query()->create($data);

            $product->attachMediaFiles($data['files'], 'images');

            if (!empty($data['synonyms'])) {
                $product->synonyms()->createMany($data['synonyms']);
            }

            $this->syncRelations($product, $data);

            return $product;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = Product::query()->findOrFail($id);

            $product->update($data);

            $product->attachMediaFiles($data['files'], 'images');

            $product->synonyms()->delete();
            if (!empty($data['synonyms'])) {
                $product->synonyms()->createMany($data['synonyms']);
            }

            $this->syncRelations($product, $data);

            return $product;
        });
    }

    public function delete(string $id): bool
    {
        $product = Product::query()->findOrFail($id);

        return $product->delete();
    }

    private function syncRelations(Product $product, array $data): void
    {
        $relations = [
            'perfumers'           => 'perfumers',
            'aroma_groups'        => 'aromaGroups',
            'aroma_chords'        => 'aromaChords',
            'base_notes'          => 'baseNotes',
            'middle_notes'        => 'middleNotes',
            'top_notes'           => 'topNotes',
            'similar_products'    => 'similarProducts',
            'parfum_box_subtypes' => 'parfumBoxSubtypes',
        ];

        foreach ($relations as $field => $method) {
            if (! empty($data[$field]) && is_array($data[$field])) {
                $product->{$method}()->sync($data[$field]);
            }
        }
    }
}
