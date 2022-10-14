<?php

namespace Sirfaenor\Leasytable;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Sirfaenor\Leasytable\Http\Livewire\CopyColumnWidget;
use Sirfaenor\Leasytable\Http\Livewire\DeleteColumnWidget;
use Sirfaenor\Leasytable\Http\Livewire\SwitchColumnWidget;
use Sirfaenor\Leasytable\Http\Livewire\StandardColumnWidget;
use Sirfaenor\Leasytable\Http\Livewire\Table;

class LeasytableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'leasytable');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'leasytable');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // routes
        //$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            /*  $this->publishes([
             ], 'deployment'); */


            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/leasytable'),
            ], 'leasytable-views');

            // Publishing the config.
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('leasytable.php'),
            ], 'leasytable-config');

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/leasytable'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/leasytable'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'leasytable');

        // register columns
        Livewire::component('leasytable::delete_column_widget', DeleteColumnWidget::class);
        Livewire::component('leasytable::switch_column_widget', SwitchColumnWidget::class);
        Livewire::component('leasytable::copy_column_widget', CopyColumnWidget::class);
        Livewire::component('leasytable::standard_column_widget', StandardColumnWidget::class);
        Livewire::component('leasytable::table', Table::class);
    }
}
