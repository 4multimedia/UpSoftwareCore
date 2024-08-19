<?php

namespace Upsoftware\Core\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Builder;

trait UseHash {
    public function getModelAttribute(): ?string
    {
        $path = explode('\\', get_class($this));
        return array_pop($path);
    }

    public function resolveRouteBinding($value, $field = null) {
        $record = $this->where($this->primaryKey, self::hashToId($value, $this->model))->first();
        if (!$record) {
            throw new \Exception('Record not found');
        }
        return $record;
    }

    public function scopeByHash(Builder $query, string $hash): Builder
    {
        return $query->where($this->getTable().'.'.$this->getKeyName(), self::hashToId($hash, $this->model));
    }

    public function getHash($id) {
        $salt = $this->getSalt();
        $hashids = new Hashids($salt, config('hashids.connections.main.length', 32));
        return $hashids->encode($id);
    }

    public function getHashAttribute() {
        return $this->getHash($this->id);
    }

    public static function byHash($hash): ?self
    {
        return self::query()->byHash($hash)->first();
    }

    public function shouldHashPersist(): bool
    {
        return property_exists($this, 'shouldHashPersist')
            ? $this->shouldHashPersist
            : false;
    }

    public static function hashToId(string $hash, string $model): ?int
    {
        $salt = (new static)->getSalt();
        $hashids = new Hashids($salt, config('hashids.connections.main.length', 32));
        $decoded = $hashids->decode($hash);
        return $decoded[0] ?? null;
    }

    protected function getSalt(): string
    {
        return config('app.key') . $this->getModelAttribute();
    }
}
