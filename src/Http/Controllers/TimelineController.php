<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 09/01/2024
 * Time: 16:29
 */

namespace Kima92\ExpectorPatronum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Kima92\ExpectorPatronum\Enums\ExpectationStatus;
use Kima92\ExpectorPatronum\ExpectorPatronum;
use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Models\Group;
use Kima92\ExpectorPatronum\Models\Task;

class TimelineController extends Controller
{
    public function index(ExpectorPatronum $ep)
    {
        $ep->checkAuthenticated();

        return view('expector-patronum::index', [
            "start"      => now()->subMonths(6),
            "end"        => now()->addDay(),
            "startFocus" => now()->subDay(),
            "endFocus"   => now()->addDay(),
        ]);
    }

    public function getItemsBetweenDates(Request $request, ExpectorPatronum $ep)
    {
        $ep->checkAuthenticated();
        $groups = Group::query()->pluck("name","id")->mapWithKeys(fn ($name, $id) => [
            $id . "_expected" => $name . " - Expected",
            $id . "_reality" => $name . " - Reality",
        ]);

        $items = Task::query()
            ->select(["id", "expectation_plan_id", "uuid", "started_at as start", "ended_at as end"])
            ->with("expectationPlan")
            ->whereBetween("started_at", [$request->get("start"), $request->get("end")])
            ->get()
            ->map(function (Task $task) {
                $task = $task->fill([
                    "content" => $task->expectationPlan->name . " - " . $task->uuid,
                    "title"   => $task->expectationPlan->name . " - " . $task->uuid,
                    "group"   => $task->expectationPlan->group_id . "_reality"
                ])->unsetRelation("expectationPlan")->toArray();

                $task["id"] = "task_" . $task["id"];
                $task["plan_id"] = $task["expectation_plan_id"];
                unset($task["expectation_plan_id"]);

                return $task;
            });

        $expectedItems = Expectation::query()
            ->select(["id", "status", "expectation_plan_id", "expected_start_date as start", DB::raw("IFNULL(expected_end_date, DATE_ADD(expected_start_date, INTERVAL 3 HOUR)) as end"),])
            ->whereBetween("expected_start_date", [$request->get("start"), $request->get("end")])
            ->with("expectationPlan")
            ->get()
            ->map(function(Expectation $ex) {
                $ex = $ex->fill([
                    "content"    => $ex->expectationPlan->name,
                    "title"      => $ex->expectationPlan->name,
                    "group"      => $ex->expectationPlan->group_id . "_expected",
                    "className"  => match ($ex->status) {
                        ExpectationStatus::Pending    => null,
                        ExpectationStatus::Success    => "bg-green-300  border-green-700",
                        ExpectationStatus::Failed     => "bg-red-300    border-red-700",
                        ExpectationStatus::SomeFailed => "bg-orange-300 border-orange-700",
                        default => null,
                    },
                ])->unsetRelation("expectationPlan")->toArray();

                $ex["id"] = "expected_" . $ex["id"];
                $ex["plan_id"] = $ex["expectation_plan_id"];

                unset($ex["expectation_plan_id"]);

                return $ex;
            });

        $groups = $groups->map(fn($name, $id) => ["id" => $id, "content" => $name])->values();

        return [
            "expected" => $expectedItems,
            "reality" => $items,
            "groups" => $groups,
        ];
    }
}
