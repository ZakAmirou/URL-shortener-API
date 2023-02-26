<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use GuzzleHttp\Client;

class thousandShortenUrlTest extends TestCase
{
    public function testConcurrentRequests()
    {
        $client = new Client();
        $endpoint = '/api/shorten';
        $url = 'http://localhost' . $endpoint;
        $payload = ['original_url' => 'https://www.eurosender.com/en/order/details'];

        $responses = [];

        for ($i = 0; $i < 1000; $i++) {
            $response = $client->postAsync($url, [
                'json' => $payload,
                'headers' => ['Accept' => 'application/json']
            ]);
            $responses[] = $response;
        }

        foreach ($responses as $response) {
            $this->assertEquals(200, $response->wait()->getStatusCode());
        }
    }
}
