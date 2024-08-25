<?php

namespace Upsoftware\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Upsoftware\Core\Contracts\MediaDirectory as MediaDirectoryContract;
use Upsoftware\Core\Traits\UseCategory;

class MediaDirectory extends Model implements MediaDirectoryContract
{
    use UseCategory;
    protected $guarded = [];

    public function parent(): BelongsTo {
        return $this->belongsTo(MediaDirectory::class, 'parent_id');
    }

    public function children(): HasMany {
        return $this->hasMany(MediaDirectory::class, 'parent_id');
    }
}
