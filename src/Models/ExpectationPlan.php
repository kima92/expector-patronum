<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kima92\ExpectorPatronum\Models\ExpectationPlan
 *
 * @property int $id
 * @property string $name
 * @property string $schedule
 * @property int $group_id
 * @property array $rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Kima92\ExpectorPatronum\Models\Expectation> $expectations
 * @property-read int|null $expectations_count
 * @property-read \Kima92\ExpectorPatronum\Models\Group $group
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpectationPlan query()
 * @mixin \Eloquent
 */
class ExpectationPlan extends Model
{
    protected $table = 'ep_expectation_plans';
    protected static $unguarded = true;
    protected $casts = [
        'rules' => 'array',
    ];

    // Add relationships if needed, e.g., belongsTo Group, etc.

    // You might also include methods to interpret the schedule and rules

    public function expectations()
    {
        return $this->hasMany(Expectation::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
