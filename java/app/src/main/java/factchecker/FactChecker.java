package factchecker;

import factchecker.AssessService.Assessor;
import factchecker.FetchService.Fetcher;
import factchecker.LoggerService.Logger;
import factchecker.LoggerService.NullLogger;
import factchecker.NotifierService.Notifier;
import factchecker.NotifierService.NullNotifier;
import org.json.JSONException;
import org.json.JSONObject;

import javax.security.auth.login.LoginException;
import java.io.IOException;

public class FactChecker {
    public static final String API_URL = "https://catfact.ninja/fact";

    private Logger logger;

    private Notifier notifier;

    private Fetcher fetcher;

    private Assessor assessor;

    public FactChecker(Fetcher fetcher, Assessor assessor) {
        initLogger();
        initNotifier();

        initFetcher(fetcher);
        initAssessor(assessor);
    }

    private void initLogger() {
        this.logger = new NullLogger();
    }

    private void initNotifier() {
        this.notifier = new NullNotifier();
    }

    private void initFetcher(Fetcher fetcher) {
        this.fetcher = fetcher;
    }

    private void initAssessor(Assessor assessor) {
        this.assessor = assessor;
    }

    public void setLogger(Logger logger) {
        this.logger = logger;
    }

    public void setNotifier(Notifier notifier) {
        this.notifier = notifier;
    }

    public String randomFact() {
        String fact = "";

        try {
            fact = fetchFact();
        } catch (CannotFetchFact e) {
            return String.format("Cannot retrieve a fact due to an error: %s.", e.getMessage());
        }

        String assessment = assessFact(fact);

        return String.format("%s %s", fact, assessment);
    }

    protected String fetchFact() throws CannotFetchFact {
        String rawFact = "";
        String fact = "";

        try {
            rawFact = this.fetcher.fetch(API_URL);
        } catch (IOException | InterruptedException e) {
            // log special cases: - NoHttpResponse; - ConnectTimeoutException; - etc
            this.logger.error(e.getMessage());
            throw new CannotFetchFact(e.getMessage(), e);
        }

        try {
            JSONObject parsedFact = new JSONObject(rawFact);

            if (!parsedFact.has("fact")) {
                throw new LoginException(String.format("the fact field doesn't exist. The original JSON is: %s.", rawFact));
            }

            fact = parsedFact.getString("fact");
        } catch (JSONException e) {
            // log special cases: - JSON parse exception
            this.logger.fatal(e.getMessage());
            throw new CannotFetchFact(e.getMessage(), e);
        } catch (LoginException e) {
            // log special cases: - JSON is unexpected
            this.logger.fatal(e.getMessage());
            // notify special cases: - notify Discord, - notify Mail
            this.notifier.notify("email", e.getMessage());
            this.notifier.notify("email", e.getMessage());
            this.notifier.notify("slack", e.getMessage());
            throw new CannotFetchFact(e.getMessage(), e);
        }

        return fact;
    }

    protected String assessFact(String fact) {
        return generateAssessment(fact);
    }

    protected String generateAssessment(String fact) {
        String opinion = this.assessor.getOpinion(fact);
        int score = this.assessor.getScore(fact);

        return String.format(
            "It seems to be %s. Our score is %s point%s.",
            opinion,
            score,
            (score != 1) ? "s" : ""
        );
    }
}
