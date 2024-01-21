<?php

declare(strict_types=1);

namespace FactChecker\NotifyService;

interface Notifier
{
    /**
     * Send a notification message through a notification service.
     *
     * @param string $service
     * @param string $message
     * @param array $details
     */
    public function notify(string $service, string $message, array $details): void;
}
