<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(TodoRepository $todoRepo, Security $security, User $user): Response
    {
        $user = $security->getUser();
        $todo = $todoRepo->findBy(['AuthorId' => $user]);

        if ($user->getRoles('ROLE_ADMIN')) {
            $todo = $todoRepo->findAll();
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'todo' => $todo
        ]);
    }

    #[Route('profil/edit/{id}', name: 'edt_profil', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de modification de la todo
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement des modifications en base de données
            $entityManager->flush();

            // Redirection vers la liste des todos
            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }

        // Affichage du formulaire de modification de la todo.
        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'userForm' => $form,
        ]);
    }
}
