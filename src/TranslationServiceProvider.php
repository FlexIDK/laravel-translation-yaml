<?php

namespace One23\LaravelTranslationYaml;

use \Illuminate\Translation\TranslationServiceProvider as ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{

    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            $ref = new \ReflectionClass(ServiceProvider::class);

            $path = [];
            $file = $ref->getFileName();
            if ($file) {
                $dir = dirname($file);
                $path[] = $dir . "/lang";
            }
            $path[] = $app['path.lang'];

            return new FileLoader($app['files'], $path);
        });
    }

}
