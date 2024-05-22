<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        SettingsRepository $settingsRepo, 
        Request $request): Response
    {
        $session = $request->getSession();
        $data = $settingsRepo->findAll();
        $session->set("setting", $data[0]);
        
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/error', name: 'app_error')]
    public function errorPage() 
    {
        return $this->render('404/404.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
}
