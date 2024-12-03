<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quack')]
final class QuackController extends AbstractController
{
    #[Route(name: 'app_quack_index', methods: ['GET', 'POST'])]
    public function index(QuackRepository $quackRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quack = new Quack();
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);
        $duck = $this->getUser();

        if ($form->isSubmitted()) {
            if (!$this->isGranted('ROLE_USER')) {
                throw $this->createAccessDeniedException('You must be logged in to post a quack.');
            }
            if ($form->isValid()) {
                $quack->setDuck($this->getUser());
                $entityManager->persist($quack);
                $entityManager->flush();
                return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render('quack/index.html.twig', [
            'quacks' => $quackRepository->findAll(),
            'quack' => $quack,
            'duck' => $duck,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_quack_show', methods: ['GET'])]
    public function show(Quack $quack): Response
    {
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quack_delete', methods: ['POST'])]
    public function delete(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
    }
}
