<?php

declare(strict_types=1);

namespace FactChecker;

use FactChecker\AssessService\Assessor;
use FactChecker\Exceptions\CannotFetchFact;
use FactChecker\FetchService\Fetcher;
use FactChecker\LoggerService\Logger;
use FactChecker\LoggerService\NullLogger;
use FactChecker\NotifyService\Notifier;
use FactChecker\NotifyService\NullNotifier;
use RuntimeException;

class FactChecker
{
    const API_URL = 'https://catfact.ninja/fact';

    private Logger $logger;

    private Notifier $notifier;

    private Fetcher $fetcher;

    private Assessor $assessor;

    public function __construct(Fetcher $fetcher, Assessor $assessor)
    {
        $this->initLogger();
        $this->initNotifier();

        $this->initFetcher($fetcher);
        $this->initAssessor($assessor);
    }

    private function initLogger(): void
    {
        $this->logger = new NullLogger();
    }

    private function initNotifier(): void
    {
        $this->notifier = new NullNotifier();
    }

    private function initFetcher(Fetcher $fetcher): void
    {
        $this->fetcher = $fetcher;
    }

    private function initAssessor(Assessor $assessor): void
    {
        $this->assessor = $assessor;
    }

    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    public function setNotifier(Notifier $notifier): void
    {
        $this->notifier = $notifier;
    }

    public function randomFact(): string
    {
        try {
            $fact = $this->fetchFact();
        } catch (CannotFetchFact $e) {
            return sprintf('Cannot retrieve a fact because of: %s.', $e->getMessage());
        }

        $assessment = $this->assessFact($fact);

        return sprintf(
            '%s %s',
            $fact,
            $assessment,
        );
    }

    /**
     * Fetch a fact.
     *
     * @return string
     *
     * @throws CannotFetchFact
     */
    protected function fetchFact(): string
    {
        try {
            $rawFact = $this->fetcher->fetch(self::API_URL);
        } catch (RuntimeException $e) {
            // log special cases: - RequestError; - RequestTimeout
            $this->logger->error($e->getMessage());
            throw new CannotFetchFact($e->getMessage());
        }

        try {
            $parsedFact = json_decode($rawFact, false, 2, JSON_THROW_ON_ERROR);
            $fact = $parsedFact->fact ?? throw new \LogicException('the fact field doesn\'t exist.');
        } catch (\JsonException $e) {
            // log special cases: - JSON parse exception
            $this->logger->warning($e->getMessage());
            throw new CannotFetchFact($e->getMessage());
        } catch (\LogicException $e) {
            // log special cases: - JSON is unexpected
            $this->logger->warning($e->getMessage());
            // notify special cases: - notify Discord, - notify Mail
            $this->notifier->notify('email', $e->getMessage(), ['to' => 'CTO']);
            $this->notifier->notify('email', $e->getMessage(), ['to' => 'programmers']);
            $this->notifier->notify('slack', $e->getMessage(), ['to' => 'programmers']);
            throw new CannotFetchFact($e->getMessage());
        }

        return $fact;
    }

    protected function assessFact(string $fact): string
    {
        return $this->generateAssessment($fact);
    }

    protected function generateAssessment(string $fact): string
    {
        $assessment = $this->assessor->getOpinion($fact);
        $score = $this->assessor->getScore($fact);

        return sprintf(
            'It seems to be %s. Our score is %s point%s.',
            $assessment,
            $score,
            ($score !== 1) ? 's' : '',
        );
    }
}
