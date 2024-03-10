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

        $queryWithBindings = $query->toSql();

        foreach ($query->getBindings() as $binding) {
            $queryWithBindings = preg_replace('/\?/', "'{$binding}'", $queryWithBindings, 1);
        }

        return str_replace('`', '', $queryWithBindings);
    }
}
