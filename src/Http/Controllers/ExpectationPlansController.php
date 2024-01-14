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
use Kima92\ExpectorPatronum\ExpectationsChecks\StartedInTimeCheck;
use Kima92\ExpectorPatronum\Expector;
use Kima92\ExpectorPatronum\ExpectorPatronum;
use Kima92\ExpectorPatronum\Http\Resources\ExpectationPlanResource;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class ExpectationPlansController extends Controller
{
    public function index(ExpectorPatronum $ep)
    {
        $ep->checkAuthenticated();

        return ExpectationPlanResource::collection(ExpectationPlan::with('group')->get());
    }

    public function store(Request $request, ExpectorPatronum $ep, Expector $e)
    {
        $ep->checkAuthenticated();

        $validator = validator($request->json()->all(), [
            'name'     => 'required',
            'schedule' => 'cron|required',
            'group_id' => 'integer|required',
        ], [
            'schedule' => 'Schedule expression is not valid'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => collect($validator->getMessageBag())->first()[0],
            ], Response::HTTP_BAD_REQUEST);
        }

        if (! $group = Group::query()->find($request->json('group_id'))) {
            return response()->json([
                'message' => 'group not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $expectationPlan = $e->generatePlan(
            $request->json('name'),
            $request->json('schedule'),
            $group,
            [['type' => StartedInTimeCheck::RULE_NAME]]
        );

        return new ExpectationPlanResource($expectationPlan);
    }
}
