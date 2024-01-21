<?php

declare(strict_types=1);

namespace FactChecker\AssessService;

interface Assessor
{
    const DEFAULT_SCORE = 0;
    const MAX_SCORE = 5;

    const DEFAULT_OPINIONS = [
        'unassessable',
        'unreliable',
        'plausible',
        'believable',
        'credible',
    ];

    /**
     * Calculate a score of a sentence.
     *
     * @param string $sentence
     * @return int
     */
    public function getScore(string $sentence): int;

    /**
     * Form an opinion about a sentence.
     *
     * @param string $sentence
     * @return string
     */
    public function getOpinion(string $sentence): string;
}
