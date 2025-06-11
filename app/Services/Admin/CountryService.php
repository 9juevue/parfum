<?php

namespace App\Services\Admin;


use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

class CountryService
{
    /**
     * @throws Throwable
     */
    public function store(array $data): Country
    {
        return DB::transaction(function () use ($data) {
           $country = Country::query()->create([
               'title' => $data['title'],
           ]);

            $country->attachMediaFiles($data['files'], 'image');

           return $country;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $id, array $data): Country
    {
        return DB::transaction(function () use ($id, $data) {
            $country = Country::query()->findOrFail($id);

            $country->update([
                'title' => $data['title'],
            ]);

            $country->attachMediaFiles($data['files'], 'image');

            return $country;
        });
    }

    public function delete(string $id): bool
    {
        $country = Country::query()->findOrFail($id);

        return $country->delete();
    }
}
