package factchecker.AssessService;

public class LengthScorer implements Scorer {
    @Override
    public Integer calculateScore(String sentence) {
        int length = findLength(sentence);

        if (length > 100) {
            return 2;
        }

        if (length > 1) {
            return 1;
        }

        return 0;
    }

    private Integer findLength(String sentence) {
        return sentence.length();
    }
}
