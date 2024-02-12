<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 13:42
 */

namespace Kima92\ExpectorPatronum\Repositories;

use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\Models\Expectation;

class EloquentExpectationRepository
{
    public function eachPendingNotStarted(callable $each, int $minutesBefore = 10): void
    {
        Expectation::query()
            ->where('expected_start_date', '<=', now()->subMinutes($minutesBefore))
            ->where('status', ExpectationStatus::Pending)
            ->whereNull('task_id')
            ->eachById(fn(Expectation $expectation, $i) => call_user_func_array($each, [$expectation, $i, $this]));
    }

    public function failExpectation(Expectation $expectation): void
    {
        $expectation->status = ExpectationStatus::Failed;
        $expectation->save();
    }
}
