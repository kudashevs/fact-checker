package factchecker;

import static org.junit.jupiter.api.Assertions.assertFalse;
import static org.junit.jupiter.api.Assertions.assertTrue;

import factchecker.AssessService.Assessor;
import factchecker.AssessService.DefaultAssessor;
import factchecker.FetchService.DefaultFetcher;
import factchecker.FetchService.Fetcher;
import factchecker.fixtures.FetcherStub;
import org.junit.jupiter.api.Test;

class FactCheckerTest {
    @Test
    void it_can_fetch_a_fact() {
        Fetcher fetcher = new DefaultFetcher();
        Assessor assessor = DefaultAssessor.create();

        FactChecker factChecker = new FactChecker(fetcher, assessor);
        String fact = factChecker.randomFact();

        /*
         * Using an assertion to verify that a result contains something is tempting, but we cannot check for
         * the exact value, because the output is random. So, we can check the significant characteristics only.
         */
        assertTrue(fact.length() > 0);
        assertFalse(fact.contains("error"));
    }

    @Test
    void it_can_assess_a_fact() {
        Fetcher fetcher = new FetcherStub();
        Assessor assessor = DefaultAssessor.create();

        FactChecker checker = new FactChecker(fetcher, assessor);
        String fact = checker.randomFact();

        /*
         * Because we are using a Fetcher stub, this test can rely on a more predictable behavior.
         * On the other hand, we couple our test to the stub's implementation (may become brittle).
         */
        assertTrue(fact.length() > 0);
        assertTrue(fact.contains("unreliable"));
    }
}
