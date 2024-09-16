<?php

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CepService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://brasilapi.com.br/api/',
            'timeout'  => 5.0,
        ]);
    }

    public function getAddressByCep($cep)
    {
        try {
            $response = $this->client->get("cep/v2/{$cep}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return null;
        }
    }
}
