<?php

namespace App\TheMovieDB;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class TheMovieDbClient{
    private $API_URL = "https://api.themoviedb.org/3";
    private $imdbToken;
    private $client;

    public function __construct(HttpClientInterface $client, $imdbToken)
    {
        $this->client = $client;
        $this->imdbToken = $imdbToken;
    }


    public function fetchApi($method = 'GET', $action = '', $params = ''){
        $response = $this->client->request($method, $this->API_URL . $action . '?api_key=' . $this->imdbToken . '&' . $params);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }

}