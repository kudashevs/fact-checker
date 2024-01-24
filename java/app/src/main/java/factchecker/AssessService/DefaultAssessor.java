package factchecker.AssessService;

import java.util.ArrayList;
import java.util.Arrays;

public class DefaultAssessor implements Assessor {
    private ArrayList<Scorer> decisiveScorers = new ArrayList<>();

    private ArrayList<Scorer> otherScorers = new ArrayList<>();

    public DefaultAssessor() {
    }

    public static DefaultAssessor create() {
        DefaultAssessor assessor = new DefaultAssessor();
        assessor.setDecisiveScorer(new WordScorer());
        assessor.setNormalScorer(new LengthScorer());

        return assessor;
    }

    public void setDecisiveScorer(Scorer scorer) {
        /*
         * The decisive scorer is unique therefore we overwrite the array.
         */
        decisiveScorers = new ArrayList<Scorer>(Arrays.asList(scorer));
    }

    public void setNormalScorer(Scorer scorer) {
        otherScorers.add(scorer);
    }

    @Override
    public Integer getScore(String sentence) {
        return calculateEntireScore(sentence);
    }

    private Integer calculateEntireScore(String sentence) {
        ArrayList<Scorer> registeredScorers = combineScorers();

        return calculateScore(sentence, registeredScorers);
    }

    private ArrayList<Scorer> combineScorers() {
        ArrayList<Scorer> registeredScorers = new ArrayList<>();

        registeredScorers.addAll(decisiveScorers);
        registeredScorers.addAll(otherScorers);

        return registeredScorers;
    }

    private Integer calculateScore(String sentence, ArrayList<Scorer> scorers) {
        Integer score = DEFAULT_SCORE;

        for (Scorer scorer : scorers) {
            score += scorer.calculateScore(sentence);
        }

        return (score > MAX_SCORE) ? MAX_SCORE : score;
    }

    @Override
    public String getOpinion(String sentence) {
        return calculateOpinion(sentence);
    }

    private String calculateOpinion(String sentence) {
        Integer significantScore = calculateDecisiveScore(sentence);
        Integer insignificantScore = calculateInsignificantScore(sentence);

        return retrieveOpinion(significantScore, insignificantScore);
    }

    private Integer calculateDecisiveScore(String sentence) {
        return calculateScore(sentence, decisiveScorers);
    }

    private Integer calculateInsignificantScore(String sentence) {
        return calculateScore(sentence, otherScorers);
    }

    private String retrieveOpinion(Integer significantScore, Integer insignificantScore) {
        Integer significanceThreshold = 2;

        if (significantScore >= significanceThreshold) {
            return retrieveOpinionFromSignificantScore(significantScore);
        }

        return retrieveOpinionFromInsignificantScore(significantScore + insignificantScore);
    }

    private String retrieveOpinionFromSignificantScore(Integer score) {
        int numberOfOptions = DEFAULT_OPINIONS.length;

        if (score <= 0) {
            return DEFAULT_OPINIONS[0];
        }

        if (score >= numberOfOptions) {
            return DEFAULT_OPINIONS[numberOfOptions];
        }

        return DEFAULT_OPINIONS[score];
    }

    private String retrieveOpinionFromInsignificantScore(Integer score) {
        if (score >= 1) {
            return DEFAULT_OPINIONS[1];
        }

        return DEFAULT_OPINIONS[0];
    }
}
