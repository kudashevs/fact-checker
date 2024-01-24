package factchecker.NotifierService;

public interface Notifier {
    /**
     * Send a notification message through a notification service.
     */
    void notify(String service, String message);
}
