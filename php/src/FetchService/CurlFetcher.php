<?php

declare(strict_types=1);

namespace FactChecker\FetchService;

use FactChecker\Exceptions\RequestError;

class CurlFetcher implements Fetcher
{
    private $curl;

    public function __construct()
    {
        $this->init();
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    private function init(): void
    {
        $this->curl = curl_init();

        curl_setopt_array($this->curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => ["cache-control: no-cache"],
        ]);
    }

    public function fetch(string $url): string
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            $errorMessage = curl_error($this->curl);
            curl_close($this->curl);
            throw new RequestError('Cannot retrieve API data because of ' . $errorMessage);
        }

        return $response;
    }
}
