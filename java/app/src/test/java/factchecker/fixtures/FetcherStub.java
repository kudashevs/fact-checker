package factchecker.fixtures;

import factchecker.FetchService.Fetcher;

public class FetcherStub implements Fetcher {
    @Override
    public String fetch(String url) {
        /*
         * The JSON schema is equal to the cat facts public API.
         * For more information @see https://catfact.ninja/fact
         */
        return "{\"fact\":\"A simple fact without any target word.\",\"length\":38}";
    }
}
