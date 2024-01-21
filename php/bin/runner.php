<?php

declare(strict_types=1);

use FactChecker\FactChecker;

require_once __DIR__ . '/../vendor/autoload.php';
$fetcher = new \FactChecker\FetchService\DefaultFetcher();
$assessor = \FactChecker\AssessService\DefaultAssessor::create();
$factChecker = new FactChecker($fetcher, $assessor);

echo $factChecker->randomFact();
