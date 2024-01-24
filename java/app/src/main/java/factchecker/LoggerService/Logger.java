package factchecker.LoggerService;

public interface Logger {
    public enum Level {
        OFF(0, 0),
        FATAL(100, 0),
        ERROR(200, 3),
        WARN(300, 4),
        INFO(400, 6),
        DEBUG(500, 7),
        TRACE(600, 7);

        private final int numCode;
        private final int syslogCode;

        private Level(int numCode, int syslogCode) {
            this.numCode = numCode;
            this.syslogCode = syslogCode;
        }

        public final String getName() {
            return name();
        }

        public final int getNumericCode() {
            return numCode;
        }

        public final int getSyslogCode() {
            return syslogCode;
        }
    }

    /**
     * FATAL level: usually used to log most severe error messages.
     */
    void fatal(String message);

    /**
     * ERROR level: usually used to log error messages.
     */
    void error(String message);

    /**
     * WARNING level: usually used to log warning messages.
     */
    void warn(String message);

    /**
     * INFO level: usually used to log information messages.
     */
    void info(String message);

    /**
     * DEBUG level: usually used to log debug information traces.
     */
    void debug(String message);

    /**
     * TRACE level: usually used to log diagnostic information.
     */
    void trace(String message);

    /**
     * Set a logger level.
     */
    void setLevel(Level level);

    /**
     * Log a message with a specific level.
     */
    void log(Level level, String message);
}
