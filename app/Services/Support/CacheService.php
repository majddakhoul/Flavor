<?php

namespace App\Services\Support;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CacheService
{
    public function remember(string $profile, string $key, Closure $callback, array $tags = []): mixed
    {
        if (! $this->enabled()) {
            return $callback();
        }

        try {
            return $this->store($tags)->remember($this->key($key), $this->ttl($profile), $callback);
        } catch (Throwable $exception) {
            Log::warning('Cache read failed, serving fresh data.', ['key' => $key, 'reason' => $exception->getMessage()]);

            return $callback();
        }
    }

    public function forget(string $key, array $tags = []): void
    {
        try {
            $this->store($tags)->forget($this->key($key));
        } catch (Throwable $exception) {
            Log::warning('Cache forget failed.', ['key' => $key, 'reason' => $exception->getMessage()]);
        }
    }

    public function flush(array $tags): void
    {
        if ($tags === []) {
            return;
        }

        try {
            if ($this->supportsTags()) {
                Cache::tags($tags)->flush();
            }
        } catch (Throwable $exception) {
            Log::warning('Cache flush failed.', ['tags' => $tags, 'reason' => $exception->getMessage()]);
        }
    }

    public function enabled(): bool
    {
        return (bool) config('flavor.cache.enabled');
    }

    public function key(string $key): string
    {
        return sprintf('%s:%s:%s', config('flavor.cache.prefix'), app()->getLocale(), $key);
    }

    protected function ttl(string $profile): int
    {
        return (int) (config('flavor.cache.profiles.' . $profile, 5) * 60);
    }

    protected function store(array $tags): Repository
    {
        return $tags !== [] && $this->supportsTags() ? Cache::tags($tags) : Cache::store();
    }

    protected function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
