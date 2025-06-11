<?php

namespace App\Services\Admin;

use App\Models\AromaChord;
use Illuminate\Support\Facades\DB;
use Throwable;

class AromaChordService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): AromaChord
    {
        return DB::transaction(function () use ($data) {
            $aromaChord = AromaChord::query()->create($data);

            $aromaChord->attachMediaFiles($data['files'], 'image');

            return $aromaChord;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): AromaChord
    {
        return DB::transaction(function () use ($id, $data) {
            $aromaChord = AromaChord::query()->findOrFail($id);

            $aromaChord->update($data);

            $aromaChord->attachMediaFiles($data['files'], 'image');

            return $aromaChord;
        });
    }

    public function delete(string $id): bool
    {
        $aromaChord = AromaChord::query()->findOrFail($id);

        return $aromaChord->delete();
    }
}
