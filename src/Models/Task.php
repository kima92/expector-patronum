<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kima92\ExpectorPatronum\Models\Task
 *
 * @property int $id
 * @property int $expectation_plan_id
 * @property string $uuid
 * @property \Carbon\CarbonImmutable $started_at
 * @property \Carbon\CarbonImmutable|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Kima92\ExpectorPatronum\Models\Expectation|null $expectation
 * @property-read \Kima92\ExpectorPatronum\Models\ExpectationPlan $expectationPlan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Kima92\ExpectorPatronum\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @mixin \Eloquent
 */
class Task extends Model
{
    protected $table = 'ep_tasks';
    protected static $unguarded = true;
    protected $casts = [
        'started_at' => 'immutable_datetime',
        'ended_at'   => 'immutable_datetime',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function expectationPlan()
    {
        return $this->belongsTo(ExpectationPlan::class);
    }

    public function expectation()
    {
        return $this->hasOne(Expectation::class);
    }
}
