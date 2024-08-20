<?php

namespace Upsoftware\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Upsoftware\Core\Models\MediaItem;

trait UseMedia
{
    public static function bootUseMedia()
    {
        static::creating(function ($model) {
            $maxPosition = static::where('model_id', $model->model_id)
                ->where('model_type', $model->model_type)
                ->where('collection_name', $model->collection_name)
                ->max('position');

            $model->position = $maxPosition + 1;
        });
    }

    public function media(): MorphToMany {
        return $this->morphToMany(MediaItem::class, 'model', 'model_has_medias', 'model_id', 'media_item_id')
            ->withTimestamps();
    }

    public function assignMedia(...$medias)
    {
        $model = $this->getModel();

        if ($model->exists) {
            if (is_array($medias[0])) {
                $medias = $medias[0];
            }

            $mediaItems = collect($medias)->map(function ($media) {
                return $media instanceof MediaItem ? $media : MediaItem::find($media);
            });

            $mediaItems->each(function ($item) use ($model) {
                $maxPosition = $this->media()
                    ->wherePivot('collection_name', $item->collection_name)
                    ->max('position');

                try {
                    $this->media()->attach($item->id, [
                        'position' => $maxPosition + 1,
                        'is_main' => false,
                        'status' => true,
                        'collection_name' => $collection ?? 'default',
                    ]);
                } catch (\Exception $e) {
                    return [
                        'status' => 'error',
                        'error' => 'File exists in collection',
                        'message' => $e->getMessage(),
                    ];
                }
            });
        }

        return $this;
    }
}
