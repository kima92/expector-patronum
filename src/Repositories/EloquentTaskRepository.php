<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 13:42
 */

namespace Kima92\ExpectorPatronum\Repositories;

use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Task;
use Carbon\Carbon;

class EloquentTaskRepository
{
    public function generateTask(ExpectationPlan $plan, string $uuid, ?Carbon $startedAt = null, ?Carbon $endedAt = null): Task
    {
        // Create a new task record if it's in the expected list
        $task = new Task();
        $task->expectationPlan()->associate($plan);
        $task->uuid       = $uuid;
        $task->started_at = $startedAt ?? now();
        $task->ended_at   = $endedAt;
        $task->save();

        return $task;
    }

    public function completeTask(string $uuid, ?Carbon $endedAt = null): Task
    {
        // Find the task by command name and update its completion status
        /** @var Task $task */
        $task = Task::query()->where('uuid', $uuid)->sole();
        $task->ended_at = $endedAt ?? now();
        $task->save();

        return $task;
    }
}
