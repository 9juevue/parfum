<?php

namespace App\Traits\Models;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMedia
{
    /**
     * @throws FileCannotBeAdded
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function attachMediaFiles(array $files, string $collectionName = 'default'): void
    {
        if (empty($files)) {
            $this->clearMediaCollection($collectionName);
            return;
        }

        $mediaItems = $this->getMedia($collectionName);
        $urlToMediaMap = [];

        foreach ($mediaItems as $media) {
            $urlToMediaMap[$media->getFullUrl()] = $media;
        }

        $incomingUrlToSortMap = [];
        foreach ($files as $file) {
            if (!isset($file['url'])) {
                continue;
            }
            $incomingUrlToSortMap[$file['url']] = empty($file['sort']) ? 0 : (int)$file['sort'];
        }

        $keptUrls = [];

        foreach ($incomingUrlToSortMap as $fullUrl => $sortOrder) {
            $item = collect($files)->where('url', $fullUrl)->first();

            if (array_key_exists($fullUrl, $urlToMediaMap)) {
                $mediaItem = $urlToMediaMap[$fullUrl];
                $mediaItem->order_column = $sortOrder;
                $mediaItem->setCustomProperty('is_favorite', (bool) ($item['is_favorite'] ?? false));
                $mediaItem->save();

                $keptUrls[] = $fullUrl;
                unset($urlToMediaMap[$fullUrl]);
                continue;
            }

            $pathOnly = parse_url($fullUrl, PHP_URL_PATH);
            $localPath = public_path($pathOnly);

            if (!file_exists($localPath)) {
                throw new RuntimeException(
                    "Файл не найден по локальному пути: {$localPath}"
                );
            }



            $newMedia = $this
                ->addMedia($localPath)
                ->usingName(pathinfo($localPath, PATHINFO_FILENAME))
                ->withCustomProperties([
                    'is_favorite' => (bool) ($item['is_favorite'] ?? false),
                ])
                ->toMediaCollection($collectionName);

            $newMedia->order_column = $sortOrder;
            $newMedia->save();

            $keptUrls[] = $fullUrl;
        }

        if (! empty($urlToMediaMap)) {
            collect($urlToMediaMap)
                ->each(fn(Media $media) => $media->delete());
        }
    }
}
