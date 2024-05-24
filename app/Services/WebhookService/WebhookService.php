<?php

namespace App\Services\WebhookService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public static $instance;
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public static function getInstance(): WebhookService
    {
        if (!self::$instance) {
            self::$instance = new WebhookService();
        }
        return self::$instance;
    }

    public function sendWebHook(string|null $url, array $data): void
    {
        if (!$url) {
            return;
        }

        try {
            $this->client->post($url, [
                "json" => $data,
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }
}
