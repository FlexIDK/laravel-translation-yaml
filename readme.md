# YAML support into Laravel Translation

Add Yaml file support for Laravel TranslationServiceProvider

## Install

Via Composer

``` bash
$ composer require one23/laravel-translation-yaml
```

Replace default `TranslationServiceProvider` to `\One23\LaravelTranslationYaml\TranslationServiceProvider` in `config/app.php`

```php
...
'providers' => ServiceProvider::defaultProviders()
    ->replace([
        \Illuminate\Translation\TranslationServiceProvider::class => One23\LaravelTranslationYaml\TranslationServiceProvider::class
    ])
...
```

## Security

If you discover any security related issues, please email eugene@krivoruchko.info instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
