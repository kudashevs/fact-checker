package factchecker;

public class CannotFetchFact extends RuntimeException {
    public CannotFetchFact(String errorMessage, Throwable err) {
        super(errorMessage, err);
    }
}
