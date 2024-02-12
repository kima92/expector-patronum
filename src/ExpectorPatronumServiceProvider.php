<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 15:11
 */

namespace Kima92\ExpectorPatronum;

use Cron\CronExpression;
use Illuminate\Support\Facades\Validator;
use Kima92\ExpectorPatronum\Commands\CheckNotStartedExpectationsCommand;
use Kima92\ExpectorPatronum\Commands\GenerateNextExpectationsCommand;
use Kima92\ExpectorPatronum\Listeners\HandleArtisanListener;
use Kima92\ExpectorPatronum\Repositories\EloquentExpectationRepository;
use Kima92\ExpectorPatronum\Repositories\EloquentTaskRepository;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class ExpectorPatronumServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Event::subscribe(HandleArtisanListener::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateNextExpectationsCommand::class,
                CheckNotStartedExpectationsCommand::class
            ]);
        }

        $this->app->resolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(CheckNotStartedExpectationsCommand::class)->everyFiveMinutes()->onOneServer();
            $schedule->command(GenerateNextExpectationsCommand::class)->dailyAt('18:00')->onOneServer();
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'expector-patronum');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish a config file
        $configPath = __DIR__.'/../config/expector-patronum.php';
        $this->publishes([
            $configPath => config_path('expector-patronum.php'),
        ], 'config');

        Validator::extend('cron', function ($attribute, $value, $parameters, $validator) {
            return CronExpression::isValidExpression($value);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ExpectorPatronum::class, function ($app) {
            return new ExpectorPatronum($app, new EloquentTaskRepository(), new EloquentExpectationRepository(), app(LoggerInterface::class));
        });

        $configPath = __DIR__.'/../config/expector-patronum.php';
        $this->mergeConfigFrom($configPath, 'expector-patronum');
    }
}
