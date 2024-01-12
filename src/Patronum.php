<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 10/01/2024
 * Time: 16:43
 */

namespace Kima92\ExpectorPatronum;

use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectationsChecks\EndedInTimeCheck;
use Kima92\ExpectorPatronum\ExpectationsChecks\StartedInTimeCheck;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Models\Task;

class Patronum
{

    public function checkStarted(Task $task): void
    {
        $expectation = Expectation::query()->where('expectation_plan_id', $task->expectation_plan_id)
            ->whereBetween('expected_start_date', [$task->started_at->subMinutes(5), $task->started_at->addMinutes(5)])
            ->where('status', ExpectationStatus::Pending)
            ->whereNull('task_id')
            ->latest()
            ->first() ?? rescue(fn () => Expectation::query()->where('expectation_plan_id', $task->expectation_plan_id)
                ->whereBetween('expected_start_date', [$task->started_at->subHour(), $task->started_at->addHour()])
                ->where('status', ExpectationStatus::Pending)
                ->whereNull('task_id')
                ->latest()
                ->sole()
            );

        if (!$expectation) {
            return;
        }

        $expectation->task()->associate($task);
        $expectation->save();

        (new StartedInTimeCheck())->check($expectation);
    }

    public function checkEnded(Task $task): void
    {
        (new EndedInTimeCheck())->check($task->expectation);
    }

    public function markNotStartedAsFailed(): void
    {
        Expectation::query()
            ->where('expected_start_date', '<=', now()->subMinutes(10))
            ->where('status', ExpectationStatus::Pending)
            ->whereNull('task_id')
            ->eachById(fn(Expectation $expectation) => $expectation->fill(['status' => ExpectationStatus::Failed])->save());
    }

    public function updateStatusByCheckRules(Expectation $expectation): void
    {
        $stats = collect($expectation->checks_results)->groupBy('status')->map(fn($items) => $items->count());

        $expectation->status = match ($stats[ExpectationStatus::Failed->name] ?? 0) {
            0                                   => ExpectationStatus::Success,
            count($expectation->checks_results) => ExpectationStatus::Failed,
            default                             => ExpectationStatus::SomeFailed,
        };
    }

}
