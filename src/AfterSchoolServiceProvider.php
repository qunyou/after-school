<?php

namespace Onepoint\AfterSchool;

use Illuminate\Support\ServiceProvider;

class AfterSchoolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'after-school');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'after-school');
        $this->mergeConfigFrom(__DIR__.'/config/after-school.php', 'after-school');
    }
}
