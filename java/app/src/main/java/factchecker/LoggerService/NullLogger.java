package factchecker.LoggerService;

public class NullLogger implements Logger {
    @Override
    public void fatal(String message) {
    }

    @Override
    public void error(String message) {
    }

    @Override
    public void warn(String message) {
    }

    @Override
    public void info(String message) {
    }

    @Override
    public void debug(String message) {
    }

    @Override
    public void trace(String message) {
    }

    @Override
    public void setLevel(Level level) {
    }

    @Override
    public void log(Level level, String message) {
    }
}
