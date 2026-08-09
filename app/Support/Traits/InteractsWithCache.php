<?php

namespace App\Support\Traits;

use App\Services\Support\CacheService;

trait InteractsWithCache
{
    protected function cache(): CacheService
    {
        return app(CacheService::class);
    }

    protected function remember(string $profile, string $key, \Closure $callback, array $tags = []): mixed
    {
        return $this->cache()->remember($profile, $key, $callback, $tags ?: $this->cacheTags());
    }

    protected function flushCache(): void
    {
        $this->cache()->flush($this->cacheTags());
    }

    protected function cacheTags(): array
    {
        return property_exists($this, 'cacheTags') ? $this->cacheTags : [];
    }
}
