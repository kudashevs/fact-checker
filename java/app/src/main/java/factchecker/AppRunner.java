package factchecker;

import factchecker.AssessService.Assessor;
import factchecker.AssessService.DefaultAssessor;
import factchecker.FetchService.DefaultFetcher;
import factchecker.FetchService.Fetcher;

public class AppRunner {
    public static void main(String[] args) {
        Fetcher fetcher = new DefaultFetcher();
        Assessor assessor = DefaultAssessor.create();

        FactChecker factChecker = new FactChecker(fetcher, assessor);
        String fact = factChecker.fetchFact();

        System.out.println(fact);
    }
}
