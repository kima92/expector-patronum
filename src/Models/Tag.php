<?php

namespace Kima92\ExpectorPatronum\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kima92\ExpectorPatronum\Models\Tag
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Kima92\ExpectorPatronum\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @mixin \Eloquent
 */
class Tag extends Model
{
    protected $table = 'ep_tags';

    protected static $unguarded = true;

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
}
