<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 12/01/2024
 * Time: 22:32
 */

namespace Kima92\ExpectorPatronum\Tests;

use Carbon\CarbonImmutable;
use Kima92\ExpectorPatronum\ExpectationsChecks\StartedInTimeCheck;
use Kima92\ExpectorPatronum\Expector;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Group;

class ExpectorTest extends TestCase
{

    public function testGeneratesNextExpectations()
    {
        /** @var Group $group */
        $group = Group::query()->create(['name' => 'bla', 'color' => 'green']);
        $expector = new Expector();

        $data = [
            [
                'plan' => $expector->generatePlan('nevermind1', '0 2,8,9 * * *', $group, [['type' => StartedInTimeCheck::RULE_NAME]]),
                'dates' => [
                    CarbonImmutable::parse('2024-01-01 02:00:00'),
                    CarbonImmutable::parse('2024-01-01 08:00:00'),
                    CarbonImmutable::parse('2024-01-01 09:00:00'),
                ]
            ],
            [
                'plan' => $expector->generatePlan('nevermind2', '3 4-6,16-18 * * *',   $group, [['type' => StartedInTimeCheck::RULE_NAME]]),
                'dates' => [
                    CarbonImmutable::parse('2024-01-01 04:03:00'),
                    CarbonImmutable::parse('2024-01-01 05:03:00'),
                    CarbonImmutable::parse('2024-01-01 06:03:00'),
                    CarbonImmutable::parse('2024-01-01 16:03:00'),
                    CarbonImmutable::parse('2024-01-01 17:03:00'),
                    CarbonImmutable::parse('2024-01-01 18:03:00'),
                ]
            ],
        ];

        $expector->generateNextExpectations(CarbonImmutable::create(2024), CarbonImmutable::create(2024, day: 2));

        foreach ($data as $planData) {
            /** @var ExpectationPlan $plan */
            $plan = $planData['plan'];
            foreach ($plan->expectations->sortBy('expected_start_date') as $index => $expectation) {
                $this->assertArrayHasKey($index, $planData['dates'], 'Expected {$index} to be {$expectation->expected_start_date}');
                $this->assertEquals($planData['dates'][$index] ?? CarbonImmutable::now(), $expectation->expected_start_date);
            }
            $this->assertCount(count($planData['dates']), $plan->expectations);
        }
    }
}
