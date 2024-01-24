package factchecker.AssessService;

public interface Scorer {
    /**
     * Score a sentence.
     */
    Integer calculateScore(String sentence);
}
