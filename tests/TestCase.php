<?php

namespace Kima92\ExpectorPatronum\Tests;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Kima92\ExpectorPatronum\Commands\CheckNotStartedExpectationsCommand;
use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectationsChecks\StartedInTimeCheck;
use Kima92\ExpectorPatronum\Expector;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Group;
use Orchestra\Testbench\TestCase as Orchestra;
use Kima92\ExpectorPatronum\ExpectorPatronumServiceProvider;

class TestCase extends Orchestra
{
    use WithConsoleEvents;

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
    }
}
