<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Repository\DuckRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/duck/all', name: 'app_duck_index', methods: ['GET'])]
    public function index(DuckRepository $duckRepository): Response
    {
        return $this->render('duck/index.html.twig', [
            'ducks' => $duckRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_duck_show', methods: ['GET'])]
    public function show(Duck $duck): Response
    {
        return $this->render('duck/show.html.twig', [
            'duck' => $duck,
        ]);
    }
}
