<?php

declare(strict_types=1);

namespace FactChecker\AssessService;

interface Scorer
{
    /**
     * Score a sentence.
     *
     * @param string $sentence
     * @return int
     */
    public function calculateScore(string $sentence): int;
}
