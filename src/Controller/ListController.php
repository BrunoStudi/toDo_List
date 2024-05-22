<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoListFormType;
use App\Repository\TodoRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListController extends AbstractController
{
    #[Route('/list', name: 'aff_list')]
    public function index(TodoRepository $todoRepo): Response
    {
        $todo = $todoRepo->findAll();

        return $this->render('list/index.html.twig', [
            'todo' => $todo
        ]);
    }

    #[Route('/add', name: 'add_list')]
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

    #[Route('/edit/{id}', name: 'edt_list', methods: ['GET', 'POST'])]
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

    #[Route('/remove/{id}', name: 'sup_list', methods: ['GET', 'POST'])]
    public function delete(Todo $todo, EntityManagerInterface $entityManager): Response
    {
        // Suppression de la todo.
        $entityManager->remove($todo);
        $entityManager->flush();

        // Redirection vers la liste des todos.
        return $this->redirectToRoute('aff_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/list/{id}/done', name: 'app_list_done', methods: ['GET'])]
    public function done(Todo $todo, EntityManagerInterface $entityManager): Response
    {
        // Modification de l'état de la todo à fait.
        if($todo->getEtat() === 0) {
            $todo->setEtat(1);
            //$todo->setUpdatedAt(new \DateTimeImmutable); --> créer maj entity Toto FinishedAt()
            $entityManager->flush();
        }
        else {
            $todo->setEtat(0);
            $entityManager->flush();
        }

        // Redirection vers la liste des todos.
        return $this->redirectToRoute('aff_list');
    }
}
