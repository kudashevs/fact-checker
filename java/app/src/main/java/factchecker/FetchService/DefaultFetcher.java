package factchecker.FetchService;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

public class DefaultFetcher implements Fetcher {
    @Override
    public String fetch(String url) throws IOException, InterruptedException {
        var uri = URI.create(url);
        HttpRequest request = HttpRequest.newBuilder(uri)
            .header("accept", "application/json")
            .timeout(Duration.ofSeconds(3))
            .build();

        HttpResponse<String> response = HttpClient.newHttpClient()
            .send(request, HttpResponse.BodyHandlers.ofString());

        return response.body();
    }
}
