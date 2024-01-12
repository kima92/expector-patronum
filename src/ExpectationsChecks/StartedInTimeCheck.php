<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 10/01/2024
 * Time: 17:33
 */

namespace Kima92\ExpectorPatronum\ExpectationsChecks;

use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Patronum;

class StartedInTimeCheck
{

    const RULE_NAME = 'started_at';

    public function check(Expectation $expectation): void
    {
        $task = $expectation->task;

        $rules = collect($expectation->checks_results)->keyBy('type')->all();
        if (!$rule = $rules[self::RULE_NAME] ?? null) {
            return;
        }

        $isSuccess = $task->started_at->between($expectation->expected_start_date->subMinutes(5), $expectation->expected_start_date->addMinutes(5));

        $rules[self::RULE_NAME]['status'] = ($isSuccess ? ExpectationStatus::Success : ExpectationStatus::Failed)->name;

        $expectation->checks_results = array_values($rules);

        (new Patronum())->updateStatusByCheckRules($expectation);

        $expectation->save();
    }
}
