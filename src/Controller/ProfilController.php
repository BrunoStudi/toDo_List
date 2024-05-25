<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(TodoRepository $todoRepo): Response
    {
        $todo = $todoRepo->findAll();

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'todo' => $todo
        ]);
    }
}
