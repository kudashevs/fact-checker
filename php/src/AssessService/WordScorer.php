<?php

declare(strict_types=1);

namespace FactChecker\AssessService;

class WordScorer implements Scorer
{
    const MAX_SCORE = 4;

    public function calculateScore(string $sentence): int
    {
        $score = $this->scoreWords($sentence);

        if ($score >= self::MAX_SCORE) {
            return self::MAX_SCORE;
        }

        return $score;
    }

    private function scoreWords(string $sentence): int
    {
        $count = preg_match_all('/\bcat(s)?\b/iSu', $sentence);

        return ($count === 0) ? 0 : $count + 1;
    }
}

