<?php

namespace App\EventListener;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorListener
{
    private $twig;

    // Constructeur pour acceder a l'environement du template twig
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    // Capture de l'evenement d'erreur
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new Response();

        // Déterminer le code de statut HTTP (500, 404 etc..)
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR; // Erreur interne du serveur
        }

        // Déterminer le message d'erreur
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $message = 'l\'url de la page que vous essayez d\'atteindre n\'a pas été trouvée !';
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            $message = 'Accès refusé !';
        } else {
            $message = 'Une erreur serveur est survenue.';
        }

        // Rendu de la vue d'erreur
        $response->setContent(
            $this->twig->render('error/cutom_error.html.twig', [
                'message' => $message,
                'status_code' => $statusCode
            ])
        );
        $event->setResponse($response);  // On remplace la réponse par celle-ci
    }
}