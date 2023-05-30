<?php

namespace NadzorServera\Skijasi\Module\Commerce\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use NadzorServera\Skijasi\Module\Commerce\SkijasiCommerceModule;
use NadzorServera\Skijasi\Module\Commerce\Commands\SkijasiCommerceSetup;
use NadzorServera\Skijasi\Module\Commerce\Commands\SkijasiCommerceTestSetup;
use NadzorServera\Skijasi\Module\Commerce\Commands\SkijasiDeleteExpiredOrder;
use NadzorServera\Skijasi\Module\Commerce\Facades\SkijasiCommerceModule as FacadesSkijasiCommerceModule;

class SkijasiCommerceModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $kernel = $this->app->make(Kernel::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('SkijasiCommerceModule', FacadesSkijasiCommerceModule::class);

        $this->app->singleton('skijasi-commerce-module', function () {
            return new SkijasiCommerceModule();
        });

        $this->loadMigrationsFrom(__DIR__.'/../Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'skijasi_commerce');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'skijasi_commerce');

        $this->publishes([
            __DIR__.'/../Seeder' => database_path('seeders/Skijasi/Commerce'),
            __DIR__.'/../Config/skijasi-commerce.php' => config_path('skijasi-commerce.php'),
        ], 'SkijasiCommerce');

        $this->publishes([
            __DIR__.'/../Swagger' => app_path('Http/Swagger/swagger_models'),
        ], 'SkijasiCommerceSwagger');

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule
                ->command('skijasi-commerce:delete-expired-order')
                ->cron(env('CRON_EXPIRED_ORDER') ?? '*/5 * * * *')
                ->runInBackground();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommands();
        $this->app->register(SkijasiCommerceModuleEventServiceProvider::class);
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(SkijasiCommerceSetup::class);
        $this->commands(SkijasiCommerceTestSetup::class);
        $this->commands(SkijasiDeleteExpiredOrder::class);
    }
}
