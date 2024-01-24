<?php

namespace FactChecker\Tests\Integration;

use FactChecker\AssessService\DefaultAssessor;
use FactChecker\FactChecker;
use FactChecker\FetchService\DefaultFetcher;
use FactChecker\Tests\fixtures\AssessorStub;
use FactChecker\Tests\fixtures\FetcherStub;
use PHPUnit\Framework\TestCase;

class FactCheckerTest extends TestCase
{
    /** @test */
    public function it_can_fetch_a_fact()
    {
        $fetcher = new DefaultFetcher();
        $assessor = new AssessorStub();

        $checker = new FactChecker($fetcher, $assessor);
        $fact = $checker->randomFact();

        /*
         * Using an assertion to verify that a result contains something is tempting, but we cannot check for
         * the exact value, because the output is random. So, we can check the significant characteristics only.
         */
        $this->assertNotEmpty($fact);
    }

    /** @test */
    public function it_can_assess_a_fact()
    {
        $fetcherStub = new FetcherStub();
        $assessor = DefaultAssessor::create();

        $checker = new FactChecker($fetcherStub, $assessor);
        $fact = $checker->randomFact();

        /*
         * Because we are using a Fetcher stub, this test can rely on a more predictable behavior.
         * On the other hand, we couple our test to the stub's implementation (may become brittle).
         */
        $this->assertNotEmpty($fact);
        $this->assertStringContainsString('unreliable', $fact);
    }
}

