<?php

declare(strict_types=1);

namespace FactChecker\NotifyService;

class NullNotifier implements Notifier
{
    public function notify(string $service, string $message, array $details): void
    {
    }
}
