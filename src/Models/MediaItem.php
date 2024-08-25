<?php

namespace Upsoftware\Core\Models;

use Illuminate\Database\Eloquent\Model;
use \Upsoftware\Core\Contracts\MediaItem as MediaItemContract;

class MediaItem extends Model implements MediaItemContract
{
    protected $guarded = [];

    public $casts = [
        'file_info' => 'array'
    ];

    public function assignDirectory(MediaDirectory $directory): static
    {
        $this->update(['directory_id' => $directory->id]);
        return $this;
    }
}
