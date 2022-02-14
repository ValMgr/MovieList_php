<?php

namespace App\TheMovieDB;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class TheMovieDbClient{
    private $API_URL = "https://api.themoviedb.org/3";
    private $API_KEY = '5ebe0843b2e373ffa159f5683b21b7de';
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    public function fetchApi($method = 'GET', $action = '', $params = ''){
        $response = $this->client->request($method, $this->API_URL . $action . '?api_key=' . $this->API_KEY . '&' . $params);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }

}