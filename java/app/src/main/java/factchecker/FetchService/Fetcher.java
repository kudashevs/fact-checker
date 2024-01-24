package factchecker.FetchService;

import java.io.IOException;

public interface Fetcher {
    /**
     * Fetch the provided url.
     *
     * @throws java.io.IOException
     */
    String fetch(String url) throws IOException;
}
