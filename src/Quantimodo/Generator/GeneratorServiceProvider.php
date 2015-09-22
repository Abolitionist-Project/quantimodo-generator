<?php

namespace Quantimodo\Generator;

use Illuminate\Support\ServiceProvider;
use Quantimodo\Generator\Commands\APIGeneratorCommand;
use Quantimodo\Generator\Commands\PublisherCommand;
use Quantimodo\Generator\Commands\ScaffoldAPIGeneratorCommand;
use Quantimodo\Generator\Commands\ScaffoldGeneratorCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../../config/generator.php';

        $this->publishes([
            $configPath => config_path('generator.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('quantimodo.generator.publish', function ($app) {
            return new PublisherCommand();
        });

        $this->app->singleton('quantimodo.generator.api', function ($app) {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('quantimodo.generator.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('quantimodo.generator.scaffold_api', function ($app) {
            return new ScaffoldAPIGeneratorCommand();
        });

        $this->commands([
            'quantimodo.generator.publish',
            'quantimodo.generator.api',
            'quantimodo.generator.scaffold',
            'quantimodo.generator.scaffold_api',
        ]);
    }
}
