<?php

namespace Upsoftware\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Upsoftware\Core\Models\MediaItem;

trait UseMedia
{
    public function media(): MorphToMany {
        return $this->morphToMany(MediaItem::class, 'model', 'model_has_medias', 'model_id', 'media_item_id')
            ->withTimestamps();
    }

    public function assignMedia(...$medias) {
        $model = $this->getModel();

        if ($model->exists) {
            $media_id = collect($medias)->map(function($media) {
                return $media instanceof MediaItem ? $media->id : $media;
            })->toArray();
            $this->media()->attach($media_id);
        }

        return $this;
    }
}
