<?php

namespace App\Services\Admin;

use App\Models\AromaGroup;
use Illuminate\Support\Facades\DB;
use Throwable;

class AromaGroupService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): AromaGroup
    {
        return DB::transaction(function () use ($data) {
            $aromaGroup = AromaGroup::query()->create($data);

            $aromaGroup->attachMediaFiles($data['files'], 'image');

            return $aromaGroup;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): AromaGroup
    {
        return DB::transaction(function () use ($id, $data) {
            $aromaGroup = AromaGroup::query()->findOrFail($id);

            $aromaGroup->update($data);

            $aromaGroup->attachMediaFiles($data['files'], 'image');

            return $aromaGroup;
        });
    }

    public function delete(string $id): bool
    {
        $aromaGroup = AromaGroup::query()->findOrFail($id);

        return $aromaGroup->delete();
    }
}
