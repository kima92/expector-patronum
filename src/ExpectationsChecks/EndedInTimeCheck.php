<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 10/01/2024
 * Time: 17:33
 */

namespace Kima92\ExpectorPatronum\ExpectationsChecks;

use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectorPatronum;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Patronum;

class EndedInTimeCheck
{

    public function __construct(private Patronum $patronum) { }

    const RULE_NAME = 'ended_in';
    public function check(Expectation $expectation): void
    {
        $rules = collect($expectation->checks_results)->keyBy('type');
        if (!$rule = $rules->get(self::RULE_NAME)) {
            return;
        }

        $task = $expectation->task;
        $isSuccess = $task->ended_at->lessThan($expectation->expected_start_date->addMinutes($rule['in']));

        $rules[self::RULE_NAME]['status'] = ($isSuccess ? ExpectationStatus::Success : ExpectationStatus::Failed)->name;

        $expectation->checks_results = $rules->values();

        $this->patronum->updateStatusByCheckRules($expectation);

        $expectation->save();
    }
}
