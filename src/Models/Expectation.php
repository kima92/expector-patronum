<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kima92\ExpectorPatronum\Enums\ExpectationStatus;

/**
 * Kima92\ExpectorPatronum\Models\Expectation
 *
 * @property int $id
 * @property int $expectation_plan_id
 * @property int|null $task_id
 * @property ExpectationStatus $status
 * @property \Carbon\CarbonImmutable $expected_start_date
 * @property \Carbon\CarbonImmutable|null $expected_end_date
 * @property array|null $checks_results
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Kima92\ExpectorPatronum\Models\ExpectationPlan $expectationPlan
 * @property-read \Kima92\ExpectorPatronum\Models\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder|Expectation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expectation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expectation query()
 * @mixin \Eloquent
 */
class Expectation extends Model
{

    protected $table = 'ep_expectations';
    protected static $unguarded = true;

    protected $casts = [
        'checks_results'      => 'array',
        'status'              => ExpectationStatus::class,
        'expected_start_date' => 'immutable_datetime',
        'expected_end_date'   => 'immutable_datetime',
    ];

    public function expectationPlan(): BelongsTo
    {
        return $this->belongsTo(ExpectationPlan::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
