<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use RuntimeException;

class GeminiService
{
    private string $gApiKey;
    private string $aptUrl;
    private HttpService $httpService;

    public function __construct()
    {
        $this->gApiKey = (string) env('G_TOKEN');
        $this->aptUrl = (string) env('G_URL');

        if ($this->gApiKey === '') {
            throw new RuntimeException('ai model token is not configured.');
        }

        $this->httpService = new HttpService();
    }

    public function makeGptRequest(array $payload = []): Response
    {
        $headers = [
            'x-goog-api-key' => $this->gApiKey,
            'Content-Type' => 'application/json',
        ];

        $response = $this->httpService->post($this->aptUrl, $payload, $headers);

        if ($response->failed()) {
            throw new RuntimeException(
                sprintf('gpt request failed: %s', $response->body())
            );
        }

        return $response;
    }
}