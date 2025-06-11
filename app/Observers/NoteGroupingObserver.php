<?php

namespace App\Observers;

use App\Models\NoteGrouping;

class NoteGroupingObserver
{
    public function deleting(NoteGrouping $noteGrouping): void
    {
        $noteGrouping->aromaNotes->each->delete();
    }
}
