<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/01/2024
 * Time: 13:07
 */

namespace Kima92\ExpectorPatronum\Listeners;

use Kima92\ExpectorPatronum\ExpectorPatronum;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Str;

class HandleArtisanListener
{
    public function subscribe($events)
    {
        if (!config("expector-patronum.isActive")) {
            return;
        }

        $events->listen(
            CommandStarting::class,
            [HandleArtisanListener::class, 'handleCommandStarting']
        );

        $events->listen(
            CommandFinished::class,
            [HandleArtisanListener::class, 'handleCommandFinished']
        );
    }

    public function handleCommandStarting(CommandStarting $event)
    {
        app(ExpectorPatronum::class)->generateArtisanTask($event->command);
    }

    public function handleCommandFinished(CommandFinished $event)
    {
        app(ExpectorPatronum::class)->completeArtisanTask($event->command);
    }
}
