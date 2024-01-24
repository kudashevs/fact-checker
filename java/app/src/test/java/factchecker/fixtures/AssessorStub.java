package factchecker.fixtures;

import factchecker.AssessService.Assessor;

public class AssessorStub implements Assessor {
    @Override
    public Integer getScore(String sentence) {
        return 4;
    }

    @Override
    public String getOpinion(String sentence) {
        return "believable";
    }
}
