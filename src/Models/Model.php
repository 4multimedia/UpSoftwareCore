<?php

namespace Upsoftware\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel {

    protected function getCreatedAtAttribute() {
        return $this->attributes["created_at"];
    }
        protected function getUpdatedAtAttribute() {
        return $this->attributes["updated_at"];
    }
}
