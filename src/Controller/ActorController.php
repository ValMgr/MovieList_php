<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class ActorController extends AbstractController
{

    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private $API_URL = "https://api.themoviedb.org/3";
    private $SLUG = "/search/person";
    private $API_KEY = '5ebe0843b2e373ffa159f5683b21b7de';

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
    public function result(RequestStack $requestStack): Response
    {
        $rq = $requestStack->getMainRequest();
        $actor = $this->getActor($rq->query->get('q'))['results'];
        // dd($actor);
        return $this->render('actor/actor.html.twig', [
            'controller_name' => 'ActorController',
            'actor' => $actor,
            'profil_url' => "https://www.themoviedb.org/t/p/w1280"
        ]);
    }

    private function getActor($q){
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
