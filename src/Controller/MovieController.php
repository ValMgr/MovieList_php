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
        dd($wishlist);
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
            'wishlist'  => $wishlist
        ]);
    }

    /**
     * @Route("/movie/search", name="search")
     */
    public function findMovies(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $movies = $client->fetchApi('GET', '/search/movie', 'query='.$rq->query->get('q'))['results'];
        return $this->render('movie/movieList.html.twig', [
            'controller_name' => 'MovieController',
            'movies' => $movies,
            'poster_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie", methods={"GET"})
     */
    public function findOneMovie(RequestStack $requestStack, TheMovieDbClient $client, ManagerRegistry $doctrine): Response
    {
        $rq = $requestStack->getMainRequest();
        $movie = $client->fetchApi('GET', '/movie/'.$rq->attributes->get('id'));
        $isSave = $doctrine->getRepository(Movie::class)->findOneByMovieId($rq->attributes->get('id'));
        $isSave = $isSave === null ? false : true;

        $actors = $client->fetchApi('GET', '/movie/'.$rq->attributes->get('id').'/credits');
        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movie,
            'actors' => $actors['cast'],
            'isSave' => $isSave,
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
        return $this->redirectToRoute('movie', ['id' => $rq->attributes->get('id')]);
    }

    /**
     * @Route("/movie/{id}", name="removeToList", methods={"DELETE"})
     */
    public function removeMovieToWatchList(RequestStack $requestStack, ManagerRegistry $doctrine) : Response
    {
        $rq = $requestStack->getMainRequest();
        $entityManager = $doctrine->getManager();

        $movie = $doctrine->getRepository(Movie::class)->findOneByMovieId($rq->attributes->get('id'));
        $entityManager->remove($movie);
        $entityManager->flush();
        return $this->redirectToRoute('movie', ['id' => $rq->attributes->get('id')]);
    }   

    /**
     * @Route("/dump/movies", name="dumpDb")
     */
    public function dumpDb(ManagerRegistry $doctrine) : Response
    {
        $entityManager = $doctrine->getManager();
        $movies = $doctrine->getRepository(Movie::class)->findAll();
        foreach ($movies as $id => $movie) {
            $entityManager->remove($movie);
        }
        $entityManager->flush();
        return $this->redirectToRoute('form');
    }

}
