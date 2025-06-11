<?php

namespace App\Services\Admin;

use App\Models\AromaNote;
use Illuminate\Support\Facades\DB;
use Throwable;

class AromaNoteService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): AromaNote
    {
        return DB::transaction(function () use ($data) {
            $aromaNote = AromaNote::query()->create($data);

            $aromaNote->attachMediaFiles($data['files'], 'image');

            return $aromaNote;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): AromaNote
    {
        return DB::transaction(function () use ($id, $data) {
            $aromaNote = AromaNote::query()->findOrFail($id);

            $aromaNote->update($data);

            $aromaNote->attachMediaFiles($data['files'], 'image');

            return $aromaNote;
        });
    }

    public function delete(string $id): bool
    {
        $aromaNote = AromaNote::query()->findOrFail($id);

        return $aromaNote->delete();
    }
}
