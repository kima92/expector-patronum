<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 12/01/2024
 * Time: 22:32
 */

namespace Kima92\ExpectorPatronum\Tests;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Kima92\ExpectorPatronum\Commands\CheckNotStartedExpectationsCommand;
use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectationsChecks\StartedInTimeCheck;
use Kima92\ExpectorPatronum\Expector;
use Kima92\ExpectorPatronum\Models\Group;

class PatronumTest extends TestCase
{

    public function testArtisanExpectationStartedInTime()
    {
        /** @var Group $group */
        $group = Group::query()->create(['name' => 'bla', 'color' => 'green']);
        $expector = new Expector();

        $plan = $expector->generatePlan('setTimeCommand', '0 16,18,20 * * *', $group, [['type' => StartedInTimeCheck::RULE_NAME]]);
        $expector->generateNextExpectations(CarbonImmutable::create(2024), CarbonImmutable::create(2024, day: 2));

        Artisan::command('setTimeCommand {time}', function (string $time) {
            Carbon::setTestNow(CarbonImmutable::parse($time));
        });

        $expectationInTime = $plan->expectations->get(0);
        $expectationDelayed = $plan->expectations->get(1);
        $expectationNotStarted = $plan->expectations->get(2);

        // Act InTime
        Carbon::setTestNow(CarbonImmutable::parse('2024-01-01 16:00:05'));
        Artisan::call('setTimeCommand \'2024-01-01 17:03:00\'');

        // Assert InTime
        $expectationInTime->refresh();
        $this->assertEquals(ExpectationStatus::Success, $expectationInTime->status);

        // Act Delayed
        Carbon::setTestNow(CarbonImmutable::parse('2024-01-01 18:06:00'));
        Artisan::call('setTimeCommand \'2024-01-01 18:30:00\'');

        // Assert delayed
        $expectationDelayed->refresh();
        $this->assertEquals(ExpectationStatus::Failed, $expectationDelayed->status);

        // Act Last not started
        Carbon::setTestNow(CarbonImmutable::parse('2024-01-01 22:20:00'));
        Artisan::call((new CheckNotStartedExpectationsCommand())->getName());

        // Assert Last not started
        $expectationNotStarted->refresh();
        $this->assertEquals(ExpectationStatus::Failed, $expectationNotStarted->status);
    }
}
