<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 15:21
 */

namespace Kima92\ExpectorPatronum;

use Carbon\CarbonInterface;
use Cron\CronExpression;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectationsChecks\EndedInTimeCheck;
use Kima92\ExpectorPatronum\Models\Group;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use RuntimeException;

class Expector
{

    public function generatePlan(string $name, string $schedule, Group $group, array $rules): ExpectationPlan
    {
        new CronExpression($schedule);

        $plan = new ExpectationPlan();
        $plan->name = $name;
        $plan->schedule = $schedule;
        $plan->group()->associate($group);
        $plan->rules = $rules;
        $plan->save();

        return $plan;
    }

    public function generateNextExpectations(CarbonInterface $startTime, CarbonInterface $endTime): array
    {
        $expectations = [];
        foreach ($this->getExpectationsBetweenDates($startTime, $endTime) as $planData) {
            /** @var Carbon $date */
            foreach ($planData['dates'] as $date) {
                /** @var ExpectationPlan $plan */
                $plan = $planData['plan'];
                $expectation = new Expectation();
                $expectation->expectationPlan()->associate($plan);
                $expectation->expected_start_date = $date;
                $expectation->expected_end_date = $date->toImmutable()->addMinutes(
                    collect($plan->rules)->firstWhere('type', EndedInTimeCheck::RULE_NAME)['in'] ?? 210
                );
                $expectation->status = ExpectationStatus::Pending;
                $expectation->checks_results = collect($expectation->expectationPlan->rules)
                    ->map(fn (array $rule) => array_merge($rule, ['status' => ExpectationStatus::Pending->name]))
                    ->all();

                $expectation->save();

                $expectations[] = $expectation;
            }
        }

        return $expectations;
    }


    public function getExpectationsBetweenDates(CarbonInterface $startTime, CarbonInterface $endTime): Collection
    {
        return ExpectationPlan::query()->get()
            ->map(function (ExpectationPlan $ep) use ($startTime, $endTime) {
                $dates = $this->getExpectationsByPlanBetweenDates(new CronExpression($ep->schedule), $startTime, $endTime);

                return $dates ? [
                    'plan' => $ep,
                    'dates' => $dates,
                ] : null;
            })->filter();
    }

    /**
     * @param CronExpression  $cron
     * @param CarbonInterface $startTime
     * @param CarbonInterface $endTime
     *
     * @return array<array-key,Carbon>
     * @throws \Exception
     */
    private function getExpectationsByPlanBetweenDates(CronExpression $cron, CarbonInterface $startTime, CarbonInterface $endTime): array
    {
        $matches = [];

        while ($startTime->lessThan($endTime)) {
            try {
                $result = new Carbon($cron->getNextRunDate($startTime));
            } catch (RuntimeException $e) {
                break;
            }

            $startTime = clone $result;

            $matches[] = $result;
        }

        // Last one probably passed endTime
        array_pop($matches);

        return $matches;
    }
}
