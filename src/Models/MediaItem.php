<?php

namespace Upsoftware\Core\Models;

use Illuminate\Database\Eloquent\Model;
use \Upsoftware\Core\Contracts\MediaItem as MediaItemContract;

class MediaItem extends Model implements MediaItemContract
{
    protected $guarded = [];
}
