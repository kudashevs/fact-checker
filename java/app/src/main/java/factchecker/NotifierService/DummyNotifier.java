package factchecker.NotifierService;

public class DummyNotifier implements Notifier {
    @Override
    public void notify(String service, String message) {
        // can throw an Exception
    }
}
