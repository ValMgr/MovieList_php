<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

use App\TheMovieDB\TheMovieDbClient;

class ActorController extends AbstractController
{
    /**
     * @Route("/actor", name="actor")
     */
    public function index(): Response
    {
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
        ]);
    }

    /**
     * @Route("/actor/search", name="actor-search")
     */
    public function result(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $actor = $client->fetchApi('GET', '/search/person', 'query='.$rq->query->get('q'))['results'];
        // dd($actor);
        return $this->render('actor/actorList.html.twig', [
            'controller_name' => 'ActorController',
            'actor' => $actor,
            'profil_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    /**
     * @Route("/actor/{id}", name="one-actor")
     */
    public function oneActor(RequestStack $requestStack, TheMovieDbClient $client): Response
    {
        $rq = $requestStack->getMainRequest();
        $actor = $client->fetchApi('GET', '/person/'.$rq->attributes->get('id'));
        $movies = $client->fetchApi('GET', '/person/'.$rq->attributes->get('id').'/movie_credits');
        // dd($movies);
        return $this->render('actor/actor.html.twig', [
            'controller_name' => 'ActorController',
            'actor' => $actor,
            'movies' => $movies['cast'],
            'profil_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }
}
