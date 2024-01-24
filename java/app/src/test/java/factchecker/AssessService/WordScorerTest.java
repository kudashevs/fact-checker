package factchecker.AssessService;

import static org.junit.jupiter.api.Assertions.assertSame;

import org.junit.jupiter.api.Test;

class WordScorerTest {
    private final WordScorer scorer = new WordScorer();

    @Test
    void it_can_score_a_sentence_without_cats() {
        String sentence = "this is a test sentence without target";

        assertSame(0, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_with_one_cat() {
        String sentence = "this is a test sentence with one cat";

        assertSame(2, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_with_two_cats() {
        String sentence = "this is a test sentence with cat and cat";

        assertSame(3, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_with_three_cats() {
        String sentence = "this is a test sentence with cats, cats, and cats";

        assertSame(4, scorer.calculateScore(sentence));
    }

    @Test
    void it_can_score_a_sentence_with_three_and_more_cats() {
        String sentence = "this is a test sentence with cats, cats, cats, and cats";

        assertSame(4, scorer.calculateScore(sentence));
    }
}
