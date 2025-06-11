<?php

namespace App\Services\Admin;

use App\Models\NoteGrouping;
use Illuminate\Support\Facades\DB;
use Throwable;

class NoteGroupingService
{
    public function store(array $data): NoteGrouping
    {
        return NoteGrouping::query()->create($data);
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): NoteGrouping
    {
        $noteGrouping = NoteGrouping::query()->findOrFail($id);

        $noteGrouping->update($data);

        return $noteGrouping;

    }

    public function delete(string $id): bool
    {
        $noteGrouping = NoteGrouping::query()->findOrFail($id);

        return $noteGrouping->delete();
    }
}
