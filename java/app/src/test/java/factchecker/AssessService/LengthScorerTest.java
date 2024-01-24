package factchecker.AssessService;

import static org.junit.jupiter.api.Assertions.assertSame;

import org.junit.jupiter.api.Test;

class LengthScorerTest {
    private final LengthScorer scorer = new LengthScorer();

    @Test
    void it_can_score_an_empty_string() {
        String sentence = "";

        assertSame(0, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_from_1_to_100() {
        String sentence = "t".repeat(100);

        assertSame(1, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_greater_than_100() {
        String sentence = "t".repeat(101);

        assertSame(2, scorer.calculateScore(sentence));
    }
}
