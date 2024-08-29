<?php

namespace Upsoftware\Core\Traits;

use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

trait UseSlug
{
    use HasTranslatableSlug;
    
    public static function bootUseSlug(): void {
        static::saving(function ($model) {
            if (!$model->slug) {
                $model->generateSlug();
            }
        });

        static::booted(function ($model) {
            if (property_exists($model, 'translatable') && is_array($model->translatable)) {
                if (!in_array('slug', $model->translatable)) {
                    $model->translatable[] = 'slug';
                }
            }
        });
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
