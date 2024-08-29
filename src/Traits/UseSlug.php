<?php

namespace Upsoftware\Core\Traits;

use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

trait UseSlug
{
    use HasTranslatableSlug;
    
    public static function bootUseSlug(): void {
        static::booted(function ($model) {

            $slugField = property_exists($model, 'slugField') ? $model->slugField : 'slug';

            if (property_exists($model, 'translatable') && is_array($model->translatable)) {
                $model->addTranslatableAttribute($slugField);
            }
        });
    }

    public function initializeUseSlug(): void
    {
        $slugField = property_exists($this, 'slugField') ? $this->slugField : 'slug';

        if (property_exists($this, 'translatable') && is_array($this->translatable)) {
            if (!in_array($slugField, $this->translatable)) {
                $this->translatable[] = $slugField;
            }
        }
    }

    public function getSlugOptions() : SlugOptions
    {
        $slugSource = property_exists($this, 'slugSource') ? $this->slugSource : 'name';
        $slugField = property_exists($this, 'slugField') ? $this->slugField : 'slug';

        return SlugOptions::create()
            ->generateSlugsFrom($slugSource)
            ->saveSlugsTo($slugField);
    }
}
