<?php
declare(strict_types=1);

namespace ZendCycle\Cli;

use Composer\Script\Event;
//TODO
class Generator
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();

        // do stuff
    }
}