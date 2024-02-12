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
use Kima92\ExpectorPatronum\Repositories\EloquentExpectationRepository;

class Patronum
{

    public function __construct(readonly private ExpectorPatronum $ep) { }

    public function checkStarted(Task $task): void
    {
        $this->ep->getLogger()->info("[ExpectorPatronum\Patronum][checkStarted] Got Task {$task->uuid}");
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
            $q = Expectation::query()->where('expectation_plan_id', $task->expectation_plan_id)
                ->whereBetween('expected_start_date', [$task->started_at->subHour(), $task->started_at->addHour()])
                ->where('status', ExpectationStatus::Pending)
                ->whereNull('task_id')
                ->latest()
                ->take(1)->toRawSql();
            $this->ep->getLogger()->warning("[ExpectorPatronum\Patronum][checkStarted] Expectation not found ({$q})");

            return;
        }

        $this->ep->getLogger()->debug("[ExpectorPatronum\Patronum][checkStarted] Linking Task {$task->uuid} to Expectation {$expectation->id}");

        $expectation->task()->associate($task);
        $expectation->save();

        (new StartedInTimeCheck($this))->check($expectation);
    }

    public function checkEnded(Task $task): void
    {
        (new EndedInTimeCheck($this))->check($task->expectation);
    }

    public function markNotStartedAsFailed(): void
    {
        $this->ep->expectationRepo
            ->eachPendingNotStarted(function(Expectation $expectation, int $i, EloquentExpectationRepository $repo) {
                $this->ep->getLogger()->info("[ExpectorPatronum\Patronum][markNotStartedAsFailed] Failing Expectation {$expectation->id}");

                $repo->failExpectation($expectation);
            });
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
