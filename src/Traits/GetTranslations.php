<?php

declare(strict_types=1);

namespace Huboo\I18nLoader\Traits;

use Huboo\I18nLoader\I18nLoaderCache;
use Huboo\I18nLoader\Services\Cache\RedisCacheService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;

trait GetTranslations
{
    protected function setConfigCache()
    {
        $path = I18nLoaderCache::KEY_PREFIX . config('language.loader.url');

        Redis::set($path . '/config.json', File::get(base_path('tests/Data/config.json')));
        Redis::set($path . '/php/en.json', File::get(base_path('tests/Data/en.json')));
        Redis::set($path . '/php/es.json', File::get(base_path('tests/Data/es.json')));
        Redis::set($path . '/php/de.json', File::get(base_path('tests/Data/de.json')));
    }

    /**
     * @return I18nLoaderCache
     */
    protected function getMockedTranslationService(): I18nLoaderCache
    {
        $mockClient = $this->mock(Client::class, function ($mock) {
            return $mock
                ->shouldReceive('get')
                ->times(4)
                ->andReturns(
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Data/config.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Data/en.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Data/es.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Data/de.json'))
                    )
                );
        });

        $cacheService = new RedisCacheService();
        $loaderConfig = config('language.loader');

        return new I18nLoaderCache($mockClient, $loaderConfig, $cacheService);
    }
}
