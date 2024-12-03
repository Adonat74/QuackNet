<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Quack;
use App\Form\CommentType;
use App\Form\QuackType;
use App\Repository\CommentRepository;
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
    public function index(QuackRepository $quackRepository, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {

        $quack = new Quack();

        $quackForm = $this->createForm(QuackType::class, $quack);
        $quackForm->handleRequest($request);
        $duck = $this->getUser();

        if ($quackForm->isSubmitted()) {
            if (!$this->isGranted('ROLE_USER')) {
                throw $this->createAccessDeniedException('You must be logged in to post a quack.');
            }
            if ($quackForm->isValid()) {
                $quack->setDuck($this->getUser());
                $entityManager->persist($quack);
                $entityManager->flush();
                return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
            }
        }





        $quacks = $quackRepository->findAll();

        $forms = [];
        $comment = new Comment();

        foreach ($quacks as $quack) {
            // Create a form with a unique CSRF token ID
            $form = $this->createForm(CommentType::class, $comment, [
                'csrf_token_id' => 'comment_form_' . $quack->getId(),
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if (!$this->isGranted('ROLE_USER')) {
                    throw $this->createAccessDeniedException('You must be logged in to post a comment.');
                }
                if ($form->isValid()) {
                    $comment->setDuck($this->getUser());
                    $comment->setQuack($quack);
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    return $this->redirectToRoute('app_quack_index');
                }
            }
            $forms[$quack->getId()] = $form->createView();
        }


        return $this->render('quack/index.html.twig', [
            'forms' => $forms,
            'quacks' => $quacks,
            'quack' => $quack,
            'duck' => $duck,
            'quackForm' => $quackForm,
            'comments' => $commentRepository->findAll()
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
