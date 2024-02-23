<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 22/02/2024
 * Time: 19:01
 */

namespace Kima92\ExpectorPatronum;

use Illuminate\Contracts\Database\Query\Builder;

class Utils
{
    public static function queryToSql(Builder $query): string
    {
        if (method_exists($query, 'toRawSql')) {
            return $query->toRawSql();
        }

        $queryWithBindings = $query["query"];

        foreach ($query["bindings"] as $binding) {
            $queryWithBindings = preg_replace('/\?/', "'{$binding}'", $queryWithBindings, 1);
        }

        $time = $query["time"] ?? null;

        if ($time) {
            $time = " [T:{$time}]";
        }

        return str_replace('`', '', $queryWithBindings) . $time;
    }
}
