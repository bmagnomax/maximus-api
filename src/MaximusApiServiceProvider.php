<?php

namespace MaximusApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Route;

class MaximusApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/maximus.php', 'maximus'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/maximus.php' => config_path('maximus.php'),
        ], 'config');

        // Obter os modelos configurados no arquivo maximus.php
        $models = config('maximus.models');

        foreach ($models as $modelName => $modelClass) {
            $modelNameLower = strtolower($modelName); // Obtém o nome do modelo em letras minúsculas

            Route::prefix("api/$modelNameLower")->group(function () use ($modelName, $modelClass) {
                Route::resource('', 'App\Http\Controllers\\' . $modelName . 'Controller')
                    ->except(['create', 'edit']);

                Route::get('search', 'App\Http\Controllers\\' . ucfirst($modelName) . 'Controller@search');
                Route::get('with', 'App\Http\Controllers\\' .  ucfirst($modelName). 'Controller@withRelations');
                Route::get('searchwith', 'App\Http\Controllers\\' .  ucfirst($modelName). 'Controller@searchWith');
            });
        }
    }
}

