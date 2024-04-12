<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public function get(string $key)
    {
        return Cache::get($key);
    }

    public function set(string $key, $value, int $ttl): bool
    {
        return Cache::set($key, $value, $ttl);
    }
}
