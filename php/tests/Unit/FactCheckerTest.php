<?php

namespace FactChecker\Tests\Unit;

use FactChecker\AssessService\Assessor;
use FactChecker\AssessService\DefaultAssessor;
use FactChecker\Exceptions\RequestError;
use FactChecker\Exceptions\RequestTimeout;
use FactChecker\FactChecker;
use FactChecker\FetchService\Fetcher;
use FactChecker\LoggerService\Logger;
use FactChecker\NotifyService\Notifier;
use FactChecker\Tests\fixtures\AssessorStub;
use FactChecker\Tests\fixtures\FetcherStub;
use PHPUnit\Framework\TestCase;

class FactCheckerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /**
     * This is a usage example of a hardcoded Fetcher stub.
     *
     * @test
     */
    public function it_can_process_an_expected_json_from_a_hardcoded_stub()
    {
        $fetcherStub = $this->createFetcherStub();

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('fact', $fact);
    }

    /**
     * This is a usage example of a Fetcher stub.
     *
     * @test
     */
    public function it_can_process_an_expected_json_with_a_stub()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"fact":"cat"}');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('cat', $fact);
    }

    /**
     * This is a usage example of a Fetcher mock.
     * We can mock it, because it is an awkward object.
     *
     * @test
     */
    public function it_can_process_an_expected_json_with_a_mock()
    {
        $fetcherMock = $this->createMock(Fetcher::class);
        $fetcherMock->expects($this->once())
            ->method('fetch')
            ->willReturn('{"fact":"cat"}');

        $checker = new FactChecker($fetcherMock, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('cat', $fact);
    }

    /**
     * @test
     */
    public function it_can_handle_a_request_error_with_unspecified_reason()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willThrowException(new RequestError('Request error'));

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('Request error', $fact);
    }

    /** @test */
    public function it_can_log_a_request_error_with_unspecified_reason()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willThrowException(new RequestError('Request error'));
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('error');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setLogger($loggerMock);
        $checker->randomFact();
    }

    /**
     * @test
     */
    public function it_can_handle_a_request_error_due_to_a_timeout()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willThrowException(new RequestTimeout('Request timeout'));

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('Request timeout', $fact);
    }

    /** @test */
    public function it_can_log_a_request_error_due_to_a_timeout()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willThrowException(new RequestTimeout('Request timeout'));
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('error');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setLogger($loggerMock);
        $checker->randomFact();
    }

    /** @test */
    public function it_can_handle_an_empty_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('error', $fact);
    }

    /** @test */
    public function it_can_log_an_empty_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('');
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('warning');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setLogger($loggerMock);
        $checker->randomFact();
    }

    /** @test */
    public function it_can_handle_an_invalid_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('error', $fact);
    }

    /** @test */
    public function it_can_log_an_invalid_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{');
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('warning');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setLogger($loggerMock);
        $checker->randomFact();
    }

    /** @test */
    public function it_can_handle_an_unexpected_json_format()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn(json_encode(['wrong' => 'test']));

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('fact field', $fact);
    }


    /** @test */
    public function it_can_log_an_unexpected_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"test":"unexpected"}');
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('warning');

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setLogger($loggerMock);
        $checker->randomFact();
    }

    /**
     * This test is highly tightly coupled and, therefore, it is very brittle.
     *
     * @test
     * @doesNotPerformAssertions
     */
    public function it_can_notify_an_unexpected_json()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"test":"unexpected"}');
        $notifierSpy = \Mockery::spy(Notifier::class);

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setNotifier($notifierSpy);
        $checker->randomFact();

        $notifierSpy->shouldHaveReceived(
            'notify',
            ['email', \Mockery::any(), \Mockery::hasValue('CTO')]
        )->once();
        $notifierSpy->shouldHaveReceived(
            'notify',
            ['email', \Mockery::any(), \Mockery::hasValue('programmers')]
        )->once();
        $notifierSpy->shouldHaveReceived(
            'notify',
            ['slack', \Mockery::any(), \Mockery::hasValue('programmers')]
        )->once();
    }


    /**
     * This is a usage example of a hardcoded Assessor stub.
     *
     * @test
     */
    public function it_can_assess_a_fact_from_a_hardcoded_stub()
    {
        /**
         * The Fetcher stub won't affect the result, but we have to provide it.
         * We substitute all the internal calculations with the predefined data.
         */
        $fetcherStub = $this->createFetcherStub();
        $assessorStub = $this->createAssessorStub();

        $checker = new FactChecker($fetcherStub, $assessorStub);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('believable', $fact);
        $this->assertStringContainsString('4 points', $fact);
    }

    /**
     * This is a usage example of an Assessor stub.
     *
     * @test
     */
    public function it_can_assess_a_fact_with_a_stub()
    {
        /**
         * The Fetcher stub won't affect the result, but we have to provide it.
         * We substitute all the internal calculations with the predefined data.
         */
        $fetcherStub = $this->createFetcherStub();
        $assessorStub = $this->createStub(Assessor::class);
        $assessorStub->method('getScore')
            ->willReturn(3);
        $assessorStub->method('getOpinion')
            ->willReturn('unbelievable but true');

        $checker = new FactChecker($fetcherStub, $assessorStub);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('unbelievable but true', $fact);
        $this->assertStringContainsString('3 points', $fact);
    }

    /**
     * This is a usage example of an Assessor mock. Don't do that!
     *
     * You can think of it from two different perspectives:
     * - we don't use mocks for queries. So, we don't mock it.
     * - the Assessor is a dependency with the deterministic behavior without side effects. So, don't mock it!
     *
     * You can stub it, if you want, but mocking is exceeding and even consider dangerous in this case.
     *
     * @test
     */
    public function it_can_assess_a_fact_with_a_mock()
    {
        $assessorMock = $this->createMock(Assessor::class);
        $assessorMock->expects($this->once())
            ->method('getScore')
            ->willReturn(3);
        $assessorMock->expects($this->once())
            ->method('getOpinion')
            ->willReturn('interesting');

        $checker = new FactChecker($this->createFetcherStub(), $assessorMock);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('interesting', $fact);
        $this->assertStringContainsString('3 points', $fact);
    }

    /**
     * This is a usage example of a real Assessor implementation.
     * We stub Fetcher with some real data and check the result.
     *
     * @test
     */
    public function it_can_assess_a_fact_with_a_real_implementation()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"fact":"Jaguars are the only big cats that don\'t roar.","length":46}');
        $assessor = DefaultAssessor::create();

        $checker = new FactChecker($fetcherStub, $assessor);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('plausible', $fact);
        $this->assertStringContainsString('3 points', $fact);
    }

    /**
     * This is a usage example of processing an empty fact with an Assessor stub.
     * This test does not make a lot of sense, because we already did it.
     * @see FactCheckerTest::it_can_assess_a_fact_with_a_stub()
     *
     * @test
     */
    public function it_can_process_an_empty_fact_with_a_stub()
    {
        /**
         * The Fetcher stub won't affect the result, but we have to provide it.
         * We substitute all the internal calculations with the predefined data.
         */
        $fetcherStub = $this->createFetcherStub();
        $assessorStub = $this->createStub(Assessor::class);
        $assessorStub->method('getScore')
            ->willReturn(0);
        $assessorStub->method('getOpinion')
            ->willReturn('empty fact');

        $checker = new FactChecker($fetcherStub, $assessorStub);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('empty fact', $fact);
        $this->assertStringContainsString('0 points', $fact);
    }

    /**
     * This is a usage example of processing an empty fact with a real Assessor implementation.
     * This test does make a lot of sense, because processing the empty string is an edge case.
     *
     * @test
     */
    public function it_can_process_an_empty_fact_with_a_real_implementation()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"fact":"","length":0}');
        $assessor = DefaultAssessor::create();

        $checker = new FactChecker($fetcherStub, $assessor);
        $fact = $checker->randomFact();

        $this->assertStringContainsString('unassessable', $fact);
        $this->assertStringContainsString('0 points', $fact);
    }

    /**
     * @return FetcherStub
     */
    private function createFetcherStub()
    {
        /**
         * A hardcoded Fetcher stub.
         */
        return new FetcherStub();
    }

    private function createAssessorStub()
    {
        /**
         * A hardcoded Assessor stub.
         */
        return new AssessorStub();
    }
}

