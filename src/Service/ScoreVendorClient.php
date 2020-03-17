<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class ScoreVendorClient
{
    private const API_URL = 'https://private-b5236a-jacek10.apiary-mock.com/results/games/1';

    /**
     * @var HttpClient
     */
    private $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function getScore(): array
    {
        $response = $this->client->request('GET', self::API_URL);

        $data = json_decode($response->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception('Invalid API response.');
        }

        return $data;
    }
}
