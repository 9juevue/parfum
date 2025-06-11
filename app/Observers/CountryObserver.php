<?php

namespace App\Observers;

use App\Models\Country;

class CountryObserver
{
    public function deleting(Country $country): void
    {
        $country->brands->each->delete();
        $country->perfumers->each->delete();
    }
}
