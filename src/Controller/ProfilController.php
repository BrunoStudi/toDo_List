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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(TodoRepository $todoRepo, Security $security, User $user, AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $security->getUser();

        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $todo = $todoRepo->findAll();
        }
        else {
            $todo = $todoRepo->findBy(['AuthorId' => $user]);
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'todo' => $todo
        ]);
    }

    #[Route('profil/edit/{id}', name: 'edt_profil', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de modification de la todo
        $form = $this->createForm(UserFormType::class, $user, [
            'attr' => ['enctype' => 'multipart/form-data'],
        ]);

        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            //ajouter image avatar
            $imageFile = $form->get('avatar')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // ceci est necessaire pour inclure le nom du fichier dans l'URL de façon sécurisé.
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Déplacer le fichier dans le repertoire approprié.
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... message d'erreur personalisé pour un echec d'upload.
                }
                // mise à jour des propriétées de 'imageFile' pour enregistrer le nom de l'image.
                $user->setAvatar($newFilename);

                // Enregistrement des modifications en base de données
                $user->setAvatar($newFilename);
                $entityManager->persist($user);
                $entityManager->flush();
            }

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
