<?php

declare(strict_types=1);

namespace Huboo\Translations\Providers;

use Huboo\Translations\Services\Cache\RedisCacheService;
use Huboo\Translations\Loader\HybridLoader;
use GuzzleHttp\Client;
use Huboo\Translations\TranslationCache;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{
    /**
     * @var Repository|Application|mixed
     */
    private $loaderConfig;

    /**
     * @var Client
     */
    private $client;
    /**
     * @var RedisCacheService
     */
    private $cacheService;

    /**
     * Create a new service provider instance.
     *
     * @param Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->loaderConfig = config('language.loader');
        $this->client = new Client();
        $this->cacheService = new RedisCacheService();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        include dirname(__FILE__) . '../routes.php';
    }

    protected function registerLoader()
    {
        parent::registerLoader();

        $this->app->bind(TranslationCache::class, function () {
            return new TranslationCache($this->client, $this->loaderConfig, $this->cacheService);
        });

        $this->app->bind('translation.cache', TranslationCache::class);

        $this->app->singleton('translation.loader', function ($app) {
            return new HybridLoader($app['files'], $app['path.lang'], $app[TranslationCache::class]);
        });
    }
}
