<?php

declare(strict_types=1);

namespace FactChecker\AssessService;

class DefaultAssessor implements Assessor
{
    private array $decisive_scorers = [];

    private array $other_scorers = [];

    protected function __construct()
    {
    }

    public static function create(): self
    {
        $assessor = new self();
        $assessor->setDecisiveScorer(new WordScorer());
        $assessor->setNormalScorer(new LengthScorer());

        return $assessor;
    }

    public function setDecisiveScorer(Scorer $scorer): void
    {
        /*
         * The decisive scorer is unique therefore we overwrite the array.
         */
        $this->decisive_scorers = [$scorer];
    }

    public function setNormalScorer(Scorer $scorer): void
    {
        $this->other_scorers[] = $scorer;
    }

    public function getScore(string $sentence): int
    {
        return $this->calculateEntireScore($sentence);
    }

    private function calculateEntireScore(string $sentence): int
    {
        $scorers = $this->combineScorers();

        return $this->calculateScore($sentence, $scorers);
    }

    private function combineScorers(): array
    {
        return array_merge($this->decisive_scorers, $this->other_scorers);
    }

    private function calculateScore(string $sentence, array $scorers): int
    {
        $score = self::DEFAULT_SCORE;
        foreach ($scorers as $scorer) {
            $score += $scorer->calculateScore($sentence);
        }

        return ($score > self::MAX_SCORE) ? self::MAX_SCORE : $score;
    }

    public function getOpinion(string $sentence): string
    {
        return $this->calculateOpinion($sentence);
    }

    private function calculateOpinion(string $sentence): string
    {
        $significantScore = $this->calculateDecisiveScore($sentence);
        $insignificantScore = $this->calculateInsignificantScore($sentence);

        return $this->retrieveOpinion($significantScore, $insignificantScore);
    }

    private function calculateDecisiveScore(string $sentence): int
    {
        return $this->calculateScore($sentence, $this->decisive_scorers);
    }

    private function calculateInsignificantScore(string $sentence): int
    {
        return $this->calculateScore($sentence, $this->other_scorers);
    }

    private function retrieveOpinion(int $significantScore, int $insignificantScore): string
    {
        $significanceThreshold = 2;

        if ($significantScore >= $significanceThreshold) {
            return $this->retrieveOpinionFromSignificantScore($significantScore);
        }

        return $this->retrieveOpinionFromInsignificantScore($significantScore + $insignificantScore);
    }

    private function retrieveOpinionFromSignificantScore(int $score): string
    {
        $numberOfOptions = count(self::DEFAULT_OPINIONS);

        if ($score <= 0) {
            return self::DEFAULT_OPINIONS[0];
        }

        if ($score >= $numberOfOptions) {
            return self::DEFAULT_OPINIONS[$numberOfOptions];
        }

        return self::DEFAULT_OPINIONS[$score];
    }

    private function retrieveOpinionFromInsignificantScore(int $score): string
    {
        if ($score >= 1) {
            return self::DEFAULT_OPINIONS[1];
        }

        return self::DEFAULT_OPINIONS[0];
    }
}
