<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpService
{
    private function prepareRequest(array $headers = [], array $options = []): PendingRequest
    {
        $request = Http::acceptJson();

        if (!empty($headers)) {
            $request = $request->withHeaders($headers);
        }

        if (!empty($options)) {
            $request = $request->withOptions($options);
        }

        return $request;
    }

    public function get(string $url, array $query = [], array $headers = [], array $options = []): Response
    {
        return $this->prepareRequest($headers, $options)->get($url, $query);
    }

    public function post(string $url, array $data = [], array $headers = [], array $options = []): Response
    {
        return $this->prepareRequest($headers, $options)->post($url, $data);
    }
}

