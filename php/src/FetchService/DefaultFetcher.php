<?php

declare(strict_types=1);

namespace FactChecker\FetchService;

use FactChecker\Exceptions\RequestError;

class DefaultFetcher implements Fetcher
{
    public function fetch(string $url): string
    {
        $content = file_get_contents($url);

        if ($content === false) {
            throw new RequestError(
                sprintf('%s was not able to retrieve data from %s.', self::class, $url)
            );
        }

        return $content;
    }
}
