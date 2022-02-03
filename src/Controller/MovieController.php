<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RequestStack;
use App\TheMovieDB\TheMovieDbClient;


class MovieController extends AbstractController
{

    /**
     * @Route("/movie", name="form")
     * @Route("/", name="home")
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
    public function findMovies(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $movies = $client->fetchApi('GET', '/search/movie', 'query='.$rq->query->get('q'))['results'];
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
    public function findOneMovie(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $movie = $client->fetchApi('GET', '/movie/'.$rq->attributes->get('id'));
        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movie,
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }
}
