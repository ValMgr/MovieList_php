<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;

use App\TheMovieDB\TheMovieDbClient;

use App\Entity\Movie;



class MovieController extends AbstractController
{

    /**
     * @Route("/movie", name="form")
     * @Route("/", name="home")
     */
    public function index(ManagerRegistry $doctrine): Response
    {

        $wishlist = $doctrine->getRepository(Movie::class)->findAll();
        // dd($wishlist);

        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
            'wishlsit'  => $wishlist
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
     * @Route("/movie/{id}", name="movie", methods={"GET"})
     */
    public function findOneMovie(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $movie = $client->fetchApi('GET', '/movie/'.$rq->attributes->get('id'));
        // dd($movie);

        $actors = $client->fetchApi('GET', '/movie/'.$rq->attributes->get('id').'/credits');
        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movie,
            'actors' => $actors['cast'],
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    /**
     * @Route("/movie/{id}", name="addToList", methods={"POST"})
     */
    public function addMovieToWatchList(RequestStack $requestStack, ManagerRegistry $doctrine) : Response
    {

        $entityManager = $doctrine->getManager();
        $rq = $requestStack->getMainRequest();

        $movie = new Movie();
        $movie->setMovieId($rq->attributes->get('id'));
        $movie->setName($rq->get('title'));
        $movie->setAddDate(new \DateTime());

        $entityManager->persist($movie);
        $entityManager->flush();

        // return new Response("Add movie to wishlist");
        return $this->redirectToRoute($request->attributes->get('_route'));
    }

}
