<?php

namespace FactChecker\Tests\Unit\AssessService;

use FactChecker\AssessService\LengthScorer;
use PHPUnit\Framework\TestCase;

class LengthScorerTest extends TestCase
{
    private LengthScorer $scorer;

    protected function setUp(): void
    {
        $this->scorer = new LengthScorer();
    }

    /** @test */
    public function it_can_score_an_empty_string()
    {
        $sentence = '';

        $this->assertSame(0, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_from_1_to_100()
    {
        $sentence = str_repeat('t', 100);

        $this->assertSame(1, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_greater_than_100()
    {
        $sentence = str_repeat('t', 101);

        $this->assertSame(2, $this->scorer->calculateScore($sentence));
    }
}
