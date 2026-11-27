<?php

namespace App\Helpers\API;

use Illuminate\Support\Facades\Http;

class DeepSeek
{
    private $endpoint = 'https://api.deepseek.com/chat/completions';

    private $token;

    public $error;

    private $requestData = [
        'model'             => 'deepseek-flash',
        'messages'          => [],
        'thinking'          => ['type' => 'enabled'],
        'reasoning_effort'  => 'high',
        'stream'            => false,
    ];

    public function __construct()
    {
        $this->token = config('services.deepseek.api_key');
    }

    public function addMessage($text = '', $role = 'system')
    {
        $message = [
            'role'      => $role,
            'content'   => $text,
        ];
        $this->requestData['messages'][] = $message;

        return $this;
    }

    public function result()
    {
        try {
            $response = Http::retry(3, 3000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint, $this->requestData);
        }
        catch (\Illuminate\Http\Client\RequestException $e) {
            $this->error = $e->response->body();
            return false;
        }

        return $response->json();
    }
}