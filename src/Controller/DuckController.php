<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Form\DuckType;
use App\Repository\DuckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/duck')]
final class DuckController extends AbstractController
{

    #[Route('/account', name: 'app_duck_account')]
    public function display(): Response
    {
        return $this->render('duck/account.html.twig');
    }

    #[Route('/{id}', name: 'app_duck_show', methods: ['GET'])]
    public function show(Duck $duck): Response
    {
        return $this->render('duck/show.html.twig', [
            'duck' => $duck,
        ]);
    }




    #[Route('/edit', name: 'app_duck_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $duck = $this->getUser();

        $form = $this->createForm(DuckType::class, $duck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $duck->setPassword($userPasswordHasher->hashPassword($duck, $plainPassword));

            $entityManager->persist($duck);
            $entityManager->flush();

            return $this->redirectToRoute('app_duck_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('duck/edit.html.twig', [
            'duckEditForm' => $form,
            'duck' => $duck,
        ]);
    }




    #[Route('/delete/{id}', name: 'app_duck_delete', methods: ['POST'])]
    public function delete(Request $request, Duck $duck, EntityManagerInterface $entityManager, Response $response): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$this->isCsrfTokenValid('delete' . $duck->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        if ($this->getUser() === $duck) {
            $this->container->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
        }

        $entityManager->remove($duck);
        $entityManager->flush();

        $duck = $this->getUser();
        if (in_array('ROLE_ADMIN', $duck->getRoles())) {
            return $this->redirectToRoute('app_duck_index');
        }
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }
}
