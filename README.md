Installation:

1. in `app/Http/Kernel.php`

1.1 add  `Hubbo/Translations/Http/Middleware/CheckLanguage::class`
to `$middleware` array

and

1.2 add `'check-language' => CheckLanguage::class,` to `$routeMiddleware` array

2. in `config/app.php`

2.1 add `Huboo\I18nLoader\Providers\I18nLoaderServiceProvider::class,` to `providers`

3. publish webhook config

3.1 run `php artisan vendor:publish --provider=""Huboo\I18nLoader\Providers\I18nLoaderServiceProvider"`