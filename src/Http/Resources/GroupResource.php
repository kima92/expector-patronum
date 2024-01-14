<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 13/01/2024
 * Time: 16:56
 */

namespace Kima92\ExpectorPatronum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Group;

class GroupResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Group|GroupResource $this */

        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'color' => $this->color,
        ];
    }
}
