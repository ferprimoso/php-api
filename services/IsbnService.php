<?php

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class IsbnService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://brasilapi.com.br/api/',
            'timeout'  => 5.0,
        ]);
    }

    public function getBookByIsbn($isbn)
    {
        try {
            $response = $this->client->request('GET', "isbn/v1/{$isbn}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return null;
        }
    }

}
