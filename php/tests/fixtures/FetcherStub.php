<?php

declare(strict_types=1);

namespace FactChecker\Tests\fixtures;

use FactChecker\FetchService\Fetcher;

class FetcherStub implements Fetcher
{
    public function fetch(string $url): string
    {
        /*
         * The JSON schema is equal to the cat facts public API.
         * For more information @see https://catfact.ninja/fact
         */
        return '{"fact":"A simple fact without any target word.","length":38}';
    }
}
