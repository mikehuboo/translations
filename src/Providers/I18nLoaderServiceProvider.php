<?php

declare(strict_types=1);

namespace Huboo\I18nLoader\Providers;

use Huboo\I18nLoader\Services\Cache\RedisCacheService;
use Huboo\I18nLoader\Loader\HybridLoader;
use GuzzleHttp\Client;
use Huboo\I18nLoader\I18nLoaderCache;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;

class I18nLoaderServiceProvider extends BaseTranslationServiceProvider
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
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/webhook-client.php' => config_path('webhook-client.php'),
        ]);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        include realpath(__DIR__ . '/../routes.php');
    }

    protected function registerLoader()
    {
        parent::registerLoader();

        $this->app->bind(I18nLoaderCache::class, function () {
            return new I18nLoaderCache($this->client, $this->loaderConfig, $this->cacheService);
        });

        $this->app->bind('translation.cache', I18nLoaderCache::class);

        $this->app->singleton('translation.loader', function ($app) {
            return new HybridLoader($app['files'], $app['path.lang'], $app[I18nLoaderCache::class]);
        });
    }
}
