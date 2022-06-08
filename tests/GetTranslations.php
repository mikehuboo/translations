<?php

declare(strict_types=1);

namespace Tests\Traits;

use Huboo\Translations\Services\Cache\RedisCacheService;
use Huboo\Translations\TranslationCache;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;

trait GetTranslations
{
    protected function setConfigCache()
    {
        $path = TranslationCache::KEY_PREFIX . config('language.loader.url');

        Redis::set($path . '/config.json', File::get(base_path('tests/Feature/Data/config.json')));
        Redis::set($path . '/php/en.json', File::get(base_path('tests/Feature/Data/en.json')));
        Redis::set($path . '/php/es.json', File::get(base_path('tests/Feature/Data/es.json')));
        Redis::set($path . '/php/de.json', File::get(base_path('tests/Feature/Data/de.json')));
    }

    /**
     * @return TranslationCache
     */
    protected function getMockedTranslationService(): TranslationCache
    {
        $mockClient = $this->mock(Client::class, function ($mock) {
            return $mock
                ->shouldReceive('get')
                ->times(4)
                ->andReturns(
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Feature/Data/config.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Feature/Data/en.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Feature/Data/es.json'))
                    ),
                    new Response(
                        \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        [],
                        File::get(base_path('tests/Feature/Data/de.json'))
                    )
                );
        });

        $cacheService = new RedisCacheService();
        $loaderConfig = config('language.loader');

        return new TranslationCache($mockClient, $loaderConfig, $cacheService);
    }
}
