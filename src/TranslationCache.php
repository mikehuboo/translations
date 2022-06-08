<?php

namespace Huboo\Translations;

use Huboo\Translations\Exceptions\CannotRebuildCacheException;
use Huboo\Translations\Services\Cache\RedisCacheService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TranslationCache
{
    /**
     * @var string
     */
    const KEY_PREFIX = 'translation.';
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $loaderConfig;
    /**
     * @var RedisCacheService
     */
    private $cache;

    /**
     * @param Client $client
     * @param array $loaderConfig
     * @param RedisCacheService $redisCacheService
     */
    public function __construct(Client $client, array $loaderConfig, RedisCacheService $redisCacheService)
    {
        $this->client = $client;
        $this->loaderConfig = $loaderConfig;
        $this->cache = $redisCacheService;
    }

    /**
     * @throws Exception
     *
     * @return array|null
     */
    public function getConfig(): ?array
    {
        $key = $this->getConfigPath();

        return $this->cache->get(self::KEY_PREFIX . $key);
    }

    /**
     * @param string $locale
     *
     * @throws Exception
     *
     * @return void
     */
    public function rebuild(string $locale): void
    {
        $path = $this->getLanguagePath($locale);
        try {
            $contents = $this->getContents($path);
            $this->cache->set(self::KEY_PREFIX . $path, $contents);
        } catch (Exception $exception) {
            throw new CannotRebuildCacheException("Cannot rebuild $locale cache");
        }
    }

    /**
     * @throws Exception
     */
    public function rebuildAllCaches()
    {
        $this->rebuildConfig();

        $locales = $this->getConfig()['available_locales'];

        collect($locales)->each(function ($locale) {
            $this->rebuild($locale);
        });
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function rebuildConfig(): void
    {
        $path = $this->getConfigPath();
        try {
            $contents = $this->getContents($path);
            if ($contents) {
                $this->cache->set(self::KEY_PREFIX . $path, $contents);
            }
        } catch (Exception $exception) {
            throw new CannotRebuildCacheException('Cannot rebuild config cache');
        }
    }

    /**
     * @param string $locale
     *
     * @throws Exception
     *
     * @return array|null
     */
    public function getLanguage(string $locale): ?array
    {
        $key = $this->getLanguagePath($locale);

        return $this->cache->get(self::KEY_PREFIX . $key);
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    private function getLanguagePath(string $locale): string
    {
        return $this->loaderConfig['url'] . "/php/$locale.json";
    }

    /**
     * @return string
     */
    private function getConfigPath(): string
    {
        return $this->loaderConfig['url'] . '/config.json';
    }

    /**
     * @param string $path
     *
     * @throws Exception
     *
     * @return array|null
     */
    private function getContents(string $path): ?array
    {
        try {
            $response = $this->client->get($path);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            throw new Exception('Cannot load file');
        }
    }
}
