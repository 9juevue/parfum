<?php

namespace App\Services\Admin;

use App\Models\Perfumer;
use Illuminate\Support\Facades\DB;
use Throwable;

class PerfumerService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): Perfumer
    {
        return DB::transaction(function () use ($data) {
            $perfumer = Perfumer::query()->create($data);

            $perfumer->attachMediaFiles($data['files'], 'image');

            if (!empty($data['synonyms'])) {
                $perfumer->synonyms()->createMany($data['synonyms']);
            }

            return $perfumer;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): Perfumer
    {
        return DB::transaction(function () use ($id, $data) {
            $perfumer = Perfumer::query()->findOrFail($id);

            $perfumer->update($data);

            $perfumer->synonyms()->delete();

            if (!empty($data['synonyms'])) {
                $perfumer->synonyms()->createMany($data['synonyms']);
            }

            $perfumer->attachMediaFiles($data['files'], 'image');

            return $perfumer;
        });
    }

    public function delete(string $id): bool
    {
        $perfumer = Perfumer::query()->findOrFail($id);

        return $perfumer->delete();
    }
}
