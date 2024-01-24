package factchecker;

import static org.junit.jupiter.api.Assertions.assertSame;
import static org.junit.jupiter.api.Assertions.assertTrue;
import static org.mockito.Mockito.*;

import factchecker.AssessService.Assessor;
import factchecker.AssessService.DefaultAssessor;
import factchecker.FetchService.DefaultFetcher;
import factchecker.FetchService.Fetcher;
import factchecker.LoggerService.Logger;
import factchecker.NotifierService.Notifier;
import factchecker.NotifierService.NullNotifier;
import factchecker.fixtures.AssessorStub;
import factchecker.fixtures.FetcherStub;
import org.junit.jupiter.api.Test;
import org.mockito.ArgumentCaptor;
import org.mockito.Mockito;

import java.io.IOException;
import java.util.List;

class FactCheckerTest {
    @Test
    void it_can_fetch_a_fact() {
        Fetcher fetcher = new DefaultFetcher();
        Assessor assessor = DefaultAssessor.create();

        FactChecker factChecker = new FactChecker(fetcher, assessor);
        String fact = factChecker.randomFact();

        assertTrue(true);
    }

    /**
     * This is a usage example of a hardcoded Fetcher stub.
     */
    @Test
    void it_can_process_an_expected_json_from_a_hardcoded_stub() {
        Fetcher fetcherStub = createFetcherStub();

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("fact"));
    }

    /**
     * This is a usage example of a Fetcher stub.
     */
    @Test
    void it_can_process_an_expected_json_with_a_stub() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{\"fact\":\"cat\"}");

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("cat"));
    }

    /**
     * This is a usage example of a Fetcher mock with parameters validation.
     * Because the Fetcher object is an awkward object, we should mock it.
     *
     * @see Mock or Stub? slide in the presentation.
     */
    @Test
    void it_can_process_an_expected_json_with_a_mock() throws IOException, InterruptedException {
        Fetcher fetcherMock = Mockito.mock(Fetcher.class);
        when(fetcherMock.fetch(FactChecker.API_URL)).thenReturn("{\"fact\":\"cat\"}");

        FactChecker checker = new FactChecker(fetcherMock, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("cat"));
        verify(fetcherMock).fetch(FactChecker.API_URL);
    }

    /**
     * This is general test for checking the SUT behavior when Fetcher fails
     * with an unspecified reason, such as Network, Protocol, other issues.
     */
    @Test
    void it_can_handle_a_request_error_with_unspecified_reason() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenThrow(new IOException("Request error"));

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("Request error"));
    }

    /**
     * This is general test for checking the Logger behavior when Fetcher fails
     * with an unspecified reason, such as Network, Protocol, other issues.
     */
    @Test
    void it_can_log_a_request_error_with_unspecified_reason() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenThrow(new IOException("Request error"));
        Logger loggerMock = Mockito.mock(Logger.class);

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setLogger(loggerMock);
        checker.randomFact();

        verify(loggerMock).error(contains("Request error"));
    }

    @Test
    void it_can_handle_an_empty_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("");

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("JSONObject text must begin"));
    }

    @Test
    void it_can_log_an_empty_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("");
        Logger loggerMock = Mockito.mock(Logger.class);

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setLogger(loggerMock);
        checker.randomFact();

        verify(loggerMock).fatal(contains("JSONObject text must begin"));
    }

    @Test
    void it_can_handle_an_invalid_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{");

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("JSONObject text"));
    }

    @Test
    void it_can_log_an_invalid_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{");
        Logger loggerMock = Mockito.mock(Logger.class);

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setLogger(loggerMock);
        checker.randomFact();

        verify(loggerMock).fatal(contains("JSONObject text"));
    }

    @Test
    void it_can_handle_an_unexpected_json_format() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{\"wrong\":\"test\"}");

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        String fact = checker.randomFact();

        assertTrue(fact.contains("fact field"));
    }

    @Test
    void it_can_log_an_unexpected_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{\"test\":\"unexpected\"}");
        Logger loggerMock = Mockito.mock(Logger.class);

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setLogger(loggerMock);
        checker.randomFact();

        /*
         * We can be even more precise and check that the original JSON was logged.
         */
        verify(loggerMock).fatal(contains("{\"test\":\"unexpected\"}"));
    }

    /**
     * This test is highly tightly coupled and, therefore, it is very brittle.
     */
    @Test
    void it_can_notify_an_unexpected_json() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{\"test\":\"unexpected\"}");
        Notifier notifierSpy = Mockito.spy(new NullNotifier());

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setNotifier(notifierSpy);
        checker.randomFact();

        verify(notifierSpy, atLeastOnce()).notify(contains("email"), anyString());
        verify(notifierSpy, atLeastOnce()).notify(contains("email"), anyString());
        verify(notifierSpy, atLeastOnce()).notify(contains("slack"), contains("{\"test\":\"unexpected\"}"));
    }

    /**
     * This test is highly tightly coupled and, therefore, it is very brittle.
     */
    @Test
    void it_can_notify_an_unexpected_json_with_additional_times_check() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(any())).thenReturn("{\"test\":\"unexpected\"}");
        Notifier notifierSpy = Mockito.spy(new NullNotifier());

        FactChecker checker = new FactChecker(fetcherStub, createAssessorStub());
        checker.setNotifier(notifierSpy);
        checker.randomFact();

        /*
         * We can be even more precise and check the number of calls if this does make sense.
         */
        ArgumentCaptor<String> serviceCaptor = ArgumentCaptor.forClass(String.class);
        ArgumentCaptor<String> messageCaptor = ArgumentCaptor.forClass(String.class);
        verify(notifierSpy, times(3)).notify(serviceCaptor.capture(), messageCaptor.capture());

        List<String> registeredServices = serviceCaptor.getAllValues();
        List<String> registeredMessages = messageCaptor.getAllValues();

        assertSame("email", registeredServices.get(0));
        assertTrue(registeredMessages.get(0).contains("fact field"));
        assertSame("email", registeredServices.get(1));
        assertTrue(registeredMessages.get(1).contains("fact field"));
        assertSame("slack", registeredServices.get(2));
        assertTrue(registeredMessages.get(2).contains("{\"test\":\"unexpected\"}"));
    }

    /**
     * This is a usage example of a hardcoded Assessor stub.
     */
    @Test
    void it_can_assess_a_fact_from_a_hardcoded_stub() {
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
         */
        Fetcher fetcherStub = createFetcherStub();
        Assessor assessorStub = createAssessorStub();

        FactChecker checker = new FactChecker(fetcherStub, assessorStub);
        String fact = checker.randomFact();

        assertTrue(fact.contains("believable"));
        assertTrue(fact.contains("4 points"));
    }

    /**
     * This is a usage example of an Assessor stub.
     */
    @Test
    void it_can_assess_a_fact_with_a_stub() {
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
         */
        Fetcher fetcherStub = createFetcherStub();
        Assessor assessorStub = Mockito.mock(Assessor.class);
        when(assessorStub.getScore(anyString())).thenReturn(3);
        when(assessorStub.getOpinion(anyString())).thenReturn("unbelievable but true");

        FactChecker checker = new FactChecker(fetcherStub, assessorStub);
        String fact = checker.randomFact();

        assertTrue(fact.contains("unbelievable but true"));
        assertTrue(fact.contains("3 points"));
    }

    /**
     * This is a usage example of an Assessor mock. Don't do that!
     *
     * You can think of it from two different perspectives:
     * - we don't use mocks for queries. So, we don't mock it.
     * - the Assessor is a dependency with the deterministic behavior without side effects. So, don't mock it!
     *
     * You can stub it, if you want, but mocking is exceeding and even consider dangerous in this case.
     */
    @Test
    void it_can_assess_a_fact_with_a_mock() {
        String defaultFetcherStubFact = "A simple fact without any target word.";
        Assessor assessorMock = Mockito.mock(Assessor.class);
        when(assessorMock.getScore(defaultFetcherStubFact)).thenReturn(3);
        when(assessorMock.getOpinion(defaultFetcherStubFact)).thenReturn("interesting");

        FactChecker checker = new FactChecker(createFetcherStub(), assessorMock);
        String fact = checker.randomFact();

        assertTrue(fact.contains("interesting"));
        verify(assessorMock).getOpinion(defaultFetcherStubFact);
        assertTrue(fact.contains("3 points"));
        verify(assessorMock).getScore(defaultFetcherStubFact);
    }

    /**
     * This is a usage example of processing a known fact using a real Assessor implementation.
     *
     * We stub Fetcher with some real world example and check the result generated by the SUT.
     * Because the behavior of the Assessor is deterministic, we can use the real implementation.
     *
     * @see Classicist vs Mockist slide in the presentation.
     */
    @Test
    void it_can_assess_a_fact_with_a_real_implementation() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(FactChecker.API_URL)).thenReturn("{\"fact\":\"Jaguars are the only big cats that don't roar.\",\"length\":46}");
        Assessor assessor = DefaultAssessor.create();

        FactChecker checker = new FactChecker(fetcherStub, assessor);
        String fact = checker.randomFact();

        assertTrue(fact.contains("plausible"));
        assertTrue(fact.contains("3 points"));
    }

    /**
     * This is a usage example of processing an empty fact string with an Assessor stub.
     * This test **does not** make a lot of sense, because we already did it previously.
     *
     * @see FactCheckerTest#it_can_assess_a_fact_with_a_stub()
     */
    void it_can_process_an_empty_fact_with_a_stub() {
        /*
         * The Fetcher's behavior is beyond the scope of our interest, but we have to provide it.
         * In this test we substitute all of the internal calculations with the predefined data.
         */
        Fetcher fetcherStub = createFetcherStub();
        Assessor assessorStub = Mockito.mock(Assessor.class);
        when(assessorStub.getScore(anyString())).thenReturn(0);
        when(assessorStub.getOpinion(anyString())).thenReturn("empty fact");

        FactChecker checker = new FactChecker(fetcherStub, assessorStub);
        String fact = checker.randomFact();

        assertTrue(fact.contains("empty fact"));
        assertTrue(fact.contains("0 points"));
    }

    /**
     * This is a usage example of processing an empty fact using a real Assessor implementation.
     * This test **does** make a lot of sense, because processing the empty string is an edge case.
     *
     * @see Classicist vs Mockist slide in the presentation.
     */
    @Test
    void it_can_process_an_empty_fact_with_a_real_implementation() throws IOException, InterruptedException {
        Fetcher fetcherStub = Mockito.mock(Fetcher.class);
        when(fetcherStub.fetch(anyString())).thenReturn("{\"fact\":\"\",\"length\":0}");
        Assessor assessor = DefaultAssessor.create();

        FactChecker checker = new FactChecker(fetcherStub, assessor);
        String fact = checker.randomFact();

        assertTrue(fact.contains("unassessable"));
        assertTrue(fact.contains("0 points"));
    }

    private Fetcher createFetcherStub() {
        /*
         * A hardcoded Fetcher stub.
         */
        return new FetcherStub();
    }

    private Assessor createAssessorStub() {
        /*
         * A hardcoded Assessor stub.
         */
        return new AssessorStub();
    }
}
