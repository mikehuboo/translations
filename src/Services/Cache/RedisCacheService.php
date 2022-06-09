<?php

declare(strict_types=1);

namespace Huboo\I18nLoader\Services\Cache;

use Exception;
use Illuminate\Support\Facades\Redis as RedisCache;

class RedisCacheService
{
    /**
     * @param string $key
     *
     * @throws Exception
     *
     * @return array|null
     */
    public function get(string $key): ?array
    {
        $contents = RedisCache::get($key);

        if (!$contents) {
            return null;
        }

        return json_decode($contents, true);
    }

    /**
     * @param string $key
     * @param array|null $contents
     * @param null $expiresIn
     *
     * @return void
     */
    public function set(string $key, ?array $contents, $expiresIn = null): void
    {
        RedisCache::set($key, json_encode($contents));
        if (isset($expiresIn)) {
            RedisCache::expire($key, $expiresIn);
        }
    }

    /**
     * @param string $key
     *
     * @throws Exception
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return !empty($this->get($key));
    }
}
