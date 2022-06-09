<?php

declare(strict_types=1);

namespace Tests\Feature\I18nLoader;

use Huboo\I18nLoader\Exceptions\CannotRebuildCacheException;
use Huboo\I18nLoader\Services\Cache\RedisCacheService;
use Huboo\I18nLoader\I18nLoaderCache;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Redis as RedisCache;
use Orchestra\Testbench\TestCase;

class I18nLoaderCacheTest extends TestCase
{
    /** @test */
    public function canGetConfig()
    {
        RedisCache::set(I18nLoaderCache::KEY_PREFIX . 'https://example.com/config.json', $set = json_encode(['foo' => 'bar']));

        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('get')
            ->willReturn(
                new Response(
                    $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                    $headers = [],
                    '{"results":true}'
                )
            );

        $cache = new I18nLoaderCache($mockClient, ['loader' => 'Default', 'url' => 'https://example.com'], new RedisCacheService());
        $result = $cache->getConfig();
        $this->assertEquals(json_decode($set, true), $result);
    }

    /** @test
     */
    public function canRebuildConfig()
    {
        RedisCache::del(I18nLoaderCache::KEY_PREFIX . 'https://example.com/config.json');

        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('get')
            ->willReturn(
                new Response(
                    $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                    $headers = [],
                    '{"results":true}'
                )
            );

        $cache = new I18nLoaderCache($mockClient, ['loader' => 'Default', 'url' => 'https://example.com'], new RedisCacheService());

        $this->assertEquals(null, $cache->getConfig());

        $cache->rebuildConfig();

        $this->assertEquals(['results' => true], $cache->getConfig());
    }

    /** @test */
    public function canGetLanguage()
    {
        $locale = 'en';

        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('get')
            ->willReturn(
                new Response(
                    $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                    $headers = [],
                    '{"results":true}'
                )
            );

        RedisCache::set(I18nLoaderCache::KEY_PREFIX . "https://example.com/php/$locale.json", $set = json_encode(['foo' => 'bar']));

        $cache = new I18nLoaderCache($mockClient, ['loader' => 'Default', 'url' => 'https://example.com'], new RedisCacheService());

        $this->assertEquals(json_decode($set, true), $cache->getLanguage($locale));
    }

    /** @test */
    public function canRebuildLanguage()
    {
        $locale = 'en';
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('get')
            ->willReturn(
                new Response(
                    $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                    $headers = [],
                    '{"results":true}'
                )
            );


        RedisCache::del(I18nLoaderCache::KEY_PREFIX . "https://example.com/php/$locale.json");

        $cache = new I18nLoaderCache($mockClient, ['loader' => 'Default', 'url' => 'https://example.com'], new RedisCacheService());

        $this->assertEquals(null, $cache->getLanguage($locale));

        $cache->rebuild($locale);

        $this->assertEquals(['results' => true], $cache->getLanguage($locale));
    }

    /** @test */
    public function rebuildCacheFailsIfCannotConnectToRemote()
    {


        RedisCache::set(I18nLoaderCache::KEY_PREFIX . 'https://example.com/config.json', $set = json_encode(['available_locales' => ['en']]));

        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('get')
            ->willThrowException(new Exception());

        $cache = new I18nLoaderCache($mockClient, ['loader' => 'Default', 'url' => 'https://example.com'], new RedisCacheService());

        $this->assertEquals(json_decode($set, true), $cache->getConfig());

        $this->expectException(CannotRebuildCacheException::class);

        $cache->rebuildConfig();

        $this->assertEquals(json_decode($set, true), $cache->getConfig());
    }
}



