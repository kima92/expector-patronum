<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 09/01/2024
 * Time: 21:11
 */

namespace Kima92\ExpectorPatronum\Enums;

enum ExpectationStatus: int
{
    case Pending    = 1;
    case Success    = 2;
    case Failed     = 3;
    case SomeFailed = 4;
}
