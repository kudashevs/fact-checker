<?php

declare(strict_types=1);

namespace FactChecker\Tests\fixtures;

use FactChecker\NotifyService\Notifier;

class NotifierDummy implements Notifier
{
    public function notify(string $service, string $message, array $details): void
    {
        // it can throw an Exception
    }
}
