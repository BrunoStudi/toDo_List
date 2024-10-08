<?php

namespace App\Controller;

use DateTime;
use App\Entity\Todo;
use App\Entity\User;
use DateTimeImmutable;
use App\Form\TodoListFormType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ListController extends AbstractController
{
    #[Route('user/task', name: 'aff_list')]
    public function index(TodoRepository $todoRepo, Security $security, AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $security->getUser();
        
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $todo = $todoRepo->findAll();
        }
        else {
            $todo = $todoRepo->findBy(['AuthorId' => $user]);
        }
        
        return $this->render('list/index.html.twig', [
            'todo' => $todo
        ]);
    }

    #[Route('user/task/add', name: 'add_list')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $todo = new Todo();

        $form = $this->createForm(TodoListFormType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'état de la todo est null, le fixer à 0
            if ($todo->getEtat() === null) {
                $todo->setEtat(0);
            }
            $todo->setAuthorId($this->getUser());
            $entityManager->persist($todo);
            $entityManager->flush();

            // Redirection vers la page d'accueil
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('list/add.html.twig', [
            'todo' => $todo,
            'todoForm' => $form
        ]);
    }

    #[Route('user/task/edit/{id}', name: 'edt_list', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todo $todo, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de modification de la todo
        $form = $this->createForm(TodoListFormType::class, $todo);
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement des modifications en base de données
            $entityManager->flush();

            // Redirection vers la liste des todos
            return $this->redirectToRoute('aff_list', [], Response::HTTP_SEE_OTHER);
        }

        // Affichage du formulaire de modification de la todo.
        return $this->render('list/edit.html.twig', [
            'todo' => $todo,
            'todoForm' => $form,
        ]);
    }

    #[Route('user/task/remove/{id}', name: 'sup_list', methods: ['GET', 'POST'])]
    public function delete(Todo $todo, EntityManagerInterface $entityManager): Response
    {
        // Suppression de la todo.
        $entityManager->remove($todo);
        $entityManager->flush();

        // Redirection vers la liste des todos.
        return $this->redirectToRoute('aff_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('user/task/{id}/done', name: 'app_list_done', methods: ['POST'])]
    public function markAsDone(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        //$todo->setUpdatedAt(new \DateTimeImmutable); --> créer maj entity Toto FinishedAt()
        
        $item = $entityManager->getRepository(Todo::class)->find($id);
        
        if (!$item) {
            return new JsonResponse(['success' => false], 404);
        }

        // Toggle the state
        $item->setEtat($item->getEtat() == 1 ? 0 : 1);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'etat' => $item->getEtat()]);
    }
}
