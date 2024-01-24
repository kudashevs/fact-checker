package factchecker.AssessService;

import static org.junit.jupiter.api.Assertions.assertSame;

import org.junit.jupiter.api.Test;
import org.junit.jupiter.params.ParameterizedTest;
import org.junit.jupiter.params.provider.Arguments;
import org.junit.jupiter.params.provider.MethodSource;

import java.util.stream.Stream;

class DefaultAssessorTest {
    private final Assessor assessor = DefaultAssessor.create();

    @Test
    void it_can_assess_an_empty_sentence() {
        Assessor assessor = DefaultAssessor.create();

        assertSame(0, assessor.getScore(""));
    }

    @ParameterizedTest
    @MethodSource("provideSentencesWithScore")
    void it_can_get_a_score_for_a_sentence(String sentence, Integer expected) {
        Assessor assessor = DefaultAssessor.create();

        assertSame(expected, assessor.getScore(sentence));
    }

    private static Stream<Arguments> provideSentencesWithScore() {
        return Stream.of(
            Arguments.of("this is a short sentence without target", 1),
            Arguments.of("this is a short sentence with cat", 3),
            Arguments.of("this is a short sentence with cat and cat", 4),
            Arguments.of("this is a short sentence with cat, cat, and cat", 5),
            Arguments.of("this is a long sentence with cat " + "t".repeat(100), 4),
            Arguments.of("this is a long sentence with cat and cat " + "t".repeat(100), 5),
            Arguments.of("this is a long sentence with cat, cat, and cat " + "t".repeat(100), 5)
        );
    }

    @Test
    void it_can_get_an_opinion_for_an_empty_sentence() {
        assertSame("unassessable", assessor.getOpinion(""));
    }

    @ParameterizedTest
    @MethodSource("provideSentencesWithOpinion")
    void it_can_get_an_opinion_for_a_sentence(String sentence, String opinion) {
        assertSame(opinion, assessor.getOpinion(sentence));
    }

    private static Stream<Arguments> provideSentencesWithOpinion() {
        return Stream.of(
            Arguments.of("this is a short sentence without target", "unreliable"),
            Arguments.of("this is a short sentence with cat", "plausible"),
            Arguments.of("this is a short sentence with cat and cat", "believable"),
            Arguments.of("this is a short sentence with cat, cat, and cat", "credible"),
            Arguments.of("this is a long sentence with cat " + "t".repeat(100), "plausible"),
            Arguments.of("this is a long sentence with cat and cat " + "t".repeat(100), "believable"),
            Arguments.of("this is a long sentence with cat, cat, and cat " + "t".repeat(100), "credible")
        );
    }

}
