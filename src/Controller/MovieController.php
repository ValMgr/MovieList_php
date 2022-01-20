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
     * @Route("/movie", name="form")
     */
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
        ]);
    }

    /**
     * @Route("/movie/search", name="search")
     */
    public function findMovies(RequestStack $requestStack): Response
    {
        $rq = $requestStack->getMainRequest();
        $movies = $this->fetchApi('GET', '/search/movie', 'query='.$rq->query->get('q'))['results'];
        // dd($movies);
        return $this->render('movie/movieList.html.twig', [
            'controller_name' => 'MovieController',
            'movies' => $movies,
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie")
     */
    public function findOneMovie(RequestStack $requestStack): Response
    {
        $rq = $requestStack->getMainRequest();
        $movie = $this->fetchApi('GET', '/movie/'.$rq->attributes->get('id'));
        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movie,
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    private function fetchApi($method = 'GET', $action, $params = ''){
        $response = $this->client->request($method, $this->API_URL . $action . '?api_key=' . $this->API_KEY . '&' . $params);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }
}
