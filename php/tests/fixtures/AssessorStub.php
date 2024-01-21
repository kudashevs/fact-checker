<?php

declare(strict_types=1);

namespace FactChecker\Tests\fixtures;

use FactChecker\AssessService\Assessor;

class AssessorStub implements Assessor
{
    public function getScore(string $sentence): int
    {
        return 4;
    }

    public function getOpinion(string $sentence): string
    {
        return 'believable';
    }
}
