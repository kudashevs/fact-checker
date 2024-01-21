<?php

namespace FactChecker\Tests\Unit\AssessService;

use FactChecker\AssessService\DefaultAssessor;
use PHPUnit\Framework\TestCase;

class DefaultAssessorTest extends TestCase
{
    private DefaultAssessor $assessor;

    protected function setUp(): void
    {
        $this->assessor = $this->createDefaultAssessor();
    }

    /** @test */
    public function it_can_assess_an_empty_sentence()
    {
        $emptySentence = '';

        $this->assertSame(0, $this->assessor->getScore($emptySentence));
    }

    /**
     * @test
     * @dataProvider provideSentencesWithScore
     */
    public function it_can_get_a_score_for_a_sentence(string $sentence, int $expected)
    {
        $this->assertSame($expected, $this->assessor->getScore($sentence));
    }

    public function provideSentencesWithScore(): array
    {
        return [
            'a sentence without cats' => [
                'this is a short sentence without target',
                1,
            ],
            'a short sentence with one cat' => [
                'this is a short sentence with cat',
                3,
            ],
            'a short sentence with two cats' => [
                'this is a short sentence with cat and cat',
                4,
            ],
            'a short sentence with three cats' => [
                'this is a short sentence with cat, cat, and cat',
                5,
            ],
            'a long sentence with one cat' => [
                'this is a long sentence with cat ' . str_repeat('t', 100),
                4,
            ],
            'a long sentence with two cats' => [
                'this is a long sentence with cat and cat ' . str_repeat('t', 100),
                5,
            ],
            'a long sentence with three cats' => [
                'this is a long sentence with cat, cat, and cat ' . str_repeat('t', 100),
                5,
            ],
        ];
    }

    /** @test */
    public function it_can_get_an_opinion_for_an_empty_sentence()
    {
        $this->assertSame('unassessable', $this->assessor->getOpinion(''));
    }

    /**
     * @test
     * @dataProvider provideSentencesWithOpinion
     */
    public function it_can_get_an_opinion_for_a_sentence(string $sentence, string $opinion)
    {
        $this->assertSame($opinion, $this->assessor->getOpinion($sentence));
    }

    public function provideSentencesWithOpinion(): array
    {
        return [
            'a sentence without cats' => [
                'this is a short sentence without target',
                'unreliable',
            ],
            'short sentence with one cat' => [
                'this is a short sentence with cat',
                'plausible',
            ],
            'short sentence with two cats' => [
                'this is a short sentence with cat and cat',
                'believable',
            ],
            'short sentence with three cats' => [
                'this is a short sentence with cat, cat, and cat',
                'credible',
            ],
            'long sentence with one cat' => [
                'this is a long sentence with cat ' . str_repeat('t', 100),
                'plausible',
            ],
            'long sentence with two cats' => [
                'this is a long sentence with cat and cat ' . str_repeat('t', 100),
                'believable',
            ],
            'long sentence with three cats' => [
                'this is a long sentence with cat, cat, and cat ' . str_repeat('t', 100),
                'credible',
            ],
        ];
    }

    private function createDefaultAssessor(): DefaultAssessor
    {
        return DefaultAssessor::create();
    }
}
