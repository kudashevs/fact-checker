<?php

declare(strict_types=1);

namespace FactChecker\AssessService;

class LengthScorer implements Scorer
{
    public function calculateScore(string $sentence): int
    {
        $length = $this->findLength($sentence);

        if ($length > 100) {
            return 2;
        }

        if ($length > 1) {
            return 1;
        }

        return 0;
    }

    private function findLength(string $sentence): int
    {
        return mb_strlen($sentence);
    }
}
