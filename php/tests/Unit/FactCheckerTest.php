<?php

namespace FactChecker\Tests\Unit;

use FactChecker\AssessService\Assessor;
use FactChecker\AssessService\DefaultAssessor;
use FactChecker\Exceptions\RequestError;
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
     * This is a usage example of a Fetcher mock with parameters validation.
     * Because the Fetcher object is an awkward object, we should mock it.
     *
     * @see Mock or Stub? slide in the presentation.
     *
     * @test
     */
    public function it_can_process_an_expected_json_with_a_mock()
    {
        $fetcherMock = $this->createMock(Fetcher::class);
        $fetcherMock->expects($this->once())
            ->method('fetch')
            ->with(FactChecker::API_URL)
            ->willReturn('{"fact":"cat"}');

        $checker = new FactChecker($fetcherMock, $this->createAssessorStub());
        $fact = $checker->randomFact();

        $this->assertStringContainsString('cat', $fact);
    }

    /**
     * This is general test for checking the SUT behavior when Fetcher fails
     * with an unspecified reason, such as Network, Protocol, other issues.
     *
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

    /**
     * This is general test for checking the Logger behavior when Fetcher fails
     * with an unspecified reason, such as Network, Protocol, other issues.
     *
     * @test
     */
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
            ->method('alert');

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
            ->method('alert');

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
        /*
         * We can be even more precise and check that the original JSON was logged.
         */
        $loggerMock->expects($this->once())
            ->method('alert')
            ->with($this->stringContains('{"test":"unexpected"}'));

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
     * This test is highly tightly coupled and, therefore, it is very brittle.
     *
     * @test
     * @doesNotPerformAssertions
     */
    public function it_can_notify_an_unexpected_json_with_additional_times_check()
    {
        $fetcherStub = $this->createStub(Fetcher::class);
        $fetcherStub->method('fetch')
            ->willReturn('{"test":"unexpected"}');
        $notifierSpy = \Mockery::spy(Notifier::class);

        $checker = new FactChecker($fetcherStub, $this->createAssessorStub());
        $checker->setNotifier($notifierSpy);
        $checker->randomFact();

        /*
         * We can be even more precise and check the number of calls if this does make sense.
         */
        $notifierSpy->shouldHaveReceived('notify')->atLeast()->times(3);
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
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
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
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
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
     * This is a usage example of processing a known fact using a real Assessor implementation.
     *
     * We stub Fetcher with some real world example and check the result generated by the SUT.
     * Because the behavior of the Assessor is deterministic, we can use the real implementation.
     *
     * @see Classicist vs Mockist slide in the presentation.
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
     * This is a usage example of processing an empty fact string with an Assessor stub.
     * This test **does not** make a lot of sense, because we already did it previously.
     *
     * @see FactCheckerTest::it_can_assess_a_fact_with_a_stub()
     *
     * @test
     */
    public function it_can_process_an_empty_fact_with_a_stub()
    {
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
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
     * This is a usage example of processing an empty fact using a real Assessor implementation.
     * This test **does** make a lot of sense, because processing the empty string is an edge case.
     *
     * @see Classicist vs Mockist slide in the presentation.
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
        /*
         * A hardcoded Fetcher stub.
         */
        return new FetcherStub();
    }

    private function createAssessorStub()
    {
        /*
         * A hardcoded Assessor stub.
         */
        return new AssessorStub();
    }
}

