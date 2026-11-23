<?php

namespace App\Helpers\API;

use Illuminate\Support\Facades\Http;

class Telegram
{
    private $baseEndpoint = 'https://api.telegram.org/';

    private $endpoint = '';

    private $token;

    public $error;

    private $requestData = [];

    public function __construct()
    {
        $this->token = config('services.telegram.token');
    }

    public function setMethod($method)
    {
        $this->endpoint = $this->baseEndpoint . 'bot' . $this->token . '/' . $method;
        return $this;
    }

    public function setParameters($params)
    {
        $this->requestData = $params;
        return $this;
    }

    public function result()
    {
        try {
            $response = Http::retry(3, 3000)
                ->asForm()
                ->post($this->endpoint, $this->requestData);
        }
        catch (\Illuminate\Http\Client\RequestException $e) {
            $responseJson = $e->response->json();
            $this->error = $responseJson['description'];
            return false;
        }

        $responseJson = $response->json();

        if ($responseJson['ok'] == true) {
            return $response->json();
        }
        else {
            $this->error = $response->json();
            return false;
        }
    }
}