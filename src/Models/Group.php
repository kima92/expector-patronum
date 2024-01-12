<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kima92\ExpectorPatronum\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Kima92\ExpectorPatronum\Models\ExpectationPlan> $expectationPlans
 * @property-read int|null $expectation_plans_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @mixin \Eloquent
 */
class Group extends Model
{
    protected $table = 'ep_groups';

    protected static $unguarded = true;

    public function expectationPlans()
    {
        return $this->hasMany(ExpectationPlan::class);
    }
}
