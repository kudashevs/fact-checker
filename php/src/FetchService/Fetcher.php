<?php

declare(strict_types=1);

namespace FactChecker\FetchService;

interface Fetcher
{
    /**
     * @param string $url
     * @return string A JSON string
     *
     * @throws \RuntimeException
     */
    public function fetch(string $url): string;
}
