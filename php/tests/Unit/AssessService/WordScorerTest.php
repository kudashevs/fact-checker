<?php

namespace FactChecker\Tests\Unit\AssessService;

use FactChecker\AssessService\WordScorer;
use PHPUnit\Framework\TestCase;

class WordScorerTest extends TestCase
{
    private WordScorer $scorer;

    protected function setUp(): void
    {
        $this->scorer = new WordScorer();
    }

    /** @test */
    public function it_can_score_a_sentence_without_cats()
    {
        $sentence = 'this is a test sentence without target';

        $this->assertSame(0, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_with_one_cat()
    {
        $sentence = 'this is a test sentence with one cat';

        $this->assertSame(2, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_with_two_cats()
    {
        $sentence = 'this is a test sentence with cat and cat';

        $this->assertSame(3, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_with_three_acats()
    {
        $sentence = 'this is a test sentence with cats, cats, and cats';

        $this->assertSame(4, $this->scorer->calculateScore($sentence));
    }

    /** @test */
    public function it_can_score_a_sentence_with_three_and_more_cats()
    {
        $sentence = 'this is a test sentence with cats, cats, cats, and cats';

        $this->assertSame(4, $this->scorer->calculateScore($sentence));
    }
}
