<?php

namespace Huboo\I18nLoader\Tests\Feature\Loader;

use Huboo\I18nLoader\Services\Cache\RedisCacheService;
use Huboo\I18nLoader\Loader\HybridLoader;
use Huboo\I18nLoader\I18nLoaderCache;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use Huboo\I18nLoader\Tests\GetTranslations;

class HybridLoaderTest extends TestCase
{
    use GetTranslations;

    const EXAMPLE_URL = 'https://www.example.com';

    /** @test */
    public function canReturnIfTranslationsAreEmpty()
    {

        $client = $this->createMock(Client::class);
        $cacheService = new RedisCacheService();
        $cache = new I18nLoaderCache($client, ['url' => self::EXAMPLE_URL], $cacheService);
        $hybridLoader = (new HybridLoader(new Filesystem(), '', $cache));

        $hybridLoader->load('en', '*', '*');

        $this->assertTrue(true); // confirming that no exceptions or type errors are thrown
    }

    /** @test */
    public function canReturnTranslationsForVendor()
    {

        $client = $this->createMock(Client::class);
        $cacheService = new RedisCacheService();
        $cache = new I18nLoaderCache($client, ['url' => self::EXAMPLE_URL], $cacheService);
        $hybridLoader = (new HybridLoader(new Filesystem(), '', $cache));

        $hybridLoader->load('en', 'messages', 'vat-validator');

        $this->assertTrue(true); // confirming that no exceptions or type errors are thrown
    }
}
