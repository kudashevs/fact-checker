package factchecker.AssessService;

public interface Assessor {
    public static final Integer DEFAULT_SCORE = 0;
    public static final Integer MAX_SCORE = 5;

    public static final String[] DEFAULT_OPINIONS = {
        "unassessable",
        "unreliable",
        "plausible",
        "believable",
        "credible",
    };

    /**
     * Calculate a score for a sentence.
     */
    abstract Integer getScore(String sentence);

    /**
     * Form an opinion about a sentence.
     */
    abstract String getOpinion(String sentence);
}
