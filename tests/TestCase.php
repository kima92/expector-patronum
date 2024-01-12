<?php

namespace Kima92\ExpectorPatronum\Tests;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Orchestra\Testbench\TestCase as Orchestra;
use Kima92\ExpectorPatronum\ExpectorPatronumServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ExpectorPatronumServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/2024_01_08_140425_create_expector_patronum_tables.php';
        $migration->up();

        if (method_exists($kernel = app(ConsoleKernel::class), 'rerouteSymfonyCommandEvents')) {
            $kernel->rerouteSymfonyCommandEvents();
        }
    }
}
