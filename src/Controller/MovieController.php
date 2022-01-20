<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class MovieController extends AbstractController
{

    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private $API_URL = "https://api.themoviedb.org/3";
    private $SLUG = "/search/movie";
    private $API_KEY = '5ebe0843b2e373ffa159f5683b21b7de';

    /**
     * @Route("/", name="movie")
     */
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function result(RequestStack $requestStack): Response
    {
        $rq = $requestStack->getMainRequest();
        $movies = $this->getMovie($rq->query->get('q'))['results'];
        // dd($movies);
        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movies[0],
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    private function getMovie($q){
        $response = $this->client->request(
            'GET',
            $this->API_URL . $this->SLUG . '?api_key=' . $this->API_KEY . '&query=' . $q
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }
}
