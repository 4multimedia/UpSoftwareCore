<?php

namespace Upsoftware\Core\Traits;

trait UseCategory
{
    private function prepareArrayToRouteAttributes($ancestors): array
    {
        $result = [];
        foreach ($ancestors as $index => $ancestor) {
            $result[] = $ancestor;
        }
        return $result;
    }

    public function getRouteAttribute(): string
    {
        return implode("/", $this->routeSlug);
    }

    public function getRouteSlugAttribute(): array
    {
        return $this->prepareArrayToRouteAttributes(array_merge($this->ancestors->pluck('slug')->toArray(), [$this->slug]));
    }

    public function getBreadcrumbsAttribute(): array
    {
        $ancestors = $this->ancestors->map(function ($ancestor) {
            return [
                'slug' => $ancestor->slug,
                'route' => $ancestor->route,
                'name' => $ancestor->name
            ];
        })->toArray();

        $ancestors[] = [
            'slug' => $this->slug,
            'route' => $this->route,
            'name' => $this->name
        ];

        return $this->prepareArrayToRouteAttributes($ancestors);
    }

    public function getDepthsAttribute(): array
    {
        return array_merge($this->ancestors->pluck('hash')->toArray(), [$this->hash]);
    }
}
