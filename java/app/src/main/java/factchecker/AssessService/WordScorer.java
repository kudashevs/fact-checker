package factchecker.AssessService;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class WordScorer implements Scorer {
    public static final Integer MAX_SCORE = 4;

    public Integer calculateScore(String sentence) {
        Integer score = scoreWords(sentence);

        if (score >= MAX_SCORE) {
            return MAX_SCORE;
        }

        return score;
    }

    private Integer scoreWords(String sentence) {
        Pattern pattern = Pattern.compile("cats?");
        Matcher matcher = pattern.matcher(sentence);

        int count = Math.toIntExact(matcher.results().count());

        return (count == 0) ? 0 : count + 1;
    }
}
