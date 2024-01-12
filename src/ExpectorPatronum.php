<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 13:06
 */

namespace Kima92\ExpectorPatronum;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Str;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Task;
use Kima92\ExpectorPatronum\Repositories\EloquentTaskRepository;
use Illuminate\Support\Facades\Cache;

class ExpectorPatronum
{

    /** @var callable|null */
    protected static mixed $expectationUuidResolver = null;
    /** @var callable */
    protected static mixed $authWith;


    public function __construct(private Application $app, private EloquentTaskRepository $repo, private Patronum $patronum)
    {
        static::$authWith = fn (Request $request) => !$this->app->environment("production") && $request->user();
    }

    public function generateTask(ExpectationPlan $plan, string $uuid, ?Carbon $startedAt = null, ?Carbon $endedAt = null): Task
    {
        return rescue(function () use ($plan, $uuid, $startedAt, $endedAt) {
            $task = $this->repo->generateTask($plan, $uuid, $startedAt, $endedAt);
            $this->patronum->checkStarted($task);

            return $task;
        });

    }

    public function completeTask(string $uuid, ?Carbon $endedAt = null): Task
    {
        return rescue(function () use ($uuid, $endedAt) {
            $task = $this->repo->completeTask($uuid, $endedAt);
            $this->patronum->checkEnded($task);
            return $task;
        });
    }

    public function generateArtisanTask(string $command): ?Task
    {
        // Check if the command is in the expected list
        try {
            $likeWhat = (new ExpectationPlan())->getConnection()->getDriverName() == "mysql" ? 'CONCAT(name, \'%\')' : 'name || \'%\'';
            $expectPlan = Cache::remember(
                "ExpectorPatronum:command-to-task:{$command}", now()->addHour(),
                fn() => ExpectationPlan::query()->whereRaw("? like {$likeWhat}", [$command])->sole()
            );
        } catch (\Exception $e) {
            return null;
        }

        // Store the UUID in the cache
        $uuid = call_user_func(self::$expectationUuidResolver ?? fn() => Str::uuid()->toString());
        Cache::driver('array')->put('ExpectorPatronum:command:' . $command, $uuid);

        return rescue(fn() => $this->generateTask($expectPlan, $uuid));
    }

    public function completeArtisanTask(string $command): void
    {
        if (!$uuid = Cache::driver('array')->get('ExpectorPatronum:command:' . $command)) {
            return;
        }

        rescue(fn() => $this->completeTask($uuid));
    }

    public function setExpectationUuidResolver(callable $expectationUuidResolver): self
    {
        self::$expectationUuidResolver = $expectationUuidResolver;

        return $this;
    }

    public function authWith(callable $callable): self
    {
        static::$authWith = $callable;

        return $this;
    }

    public function checkAuthenticated(): void
    {
        if (! call_user_func_array(static::$authWith, [RequestFacade::instance()])) {
            throw new AuthenticationException();
        }
    }
}
