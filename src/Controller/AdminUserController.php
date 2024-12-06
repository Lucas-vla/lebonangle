<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Form\AdminUserType;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    // Route pour lister tous les utilisateurs admins
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(AdminUserRepository $adminUserRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Assurez-vous que seul un admin peut accéder à cette route.

        return $this->render('admin_user/index.html.twig', [
            'admin_users' => $adminUserRepository->findAll(),
        ]);
    }

    // Route pour créer un nouvel utilisateur admin
    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Accès restreint aux admins

        $adminUser = new AdminUser();
        $form = $this->createForm(AdminUserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $adminUser->setRoles(['ROLE_ADMIN']); // Assurer que le nouvel utilisateur est un administrateur
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index'); // Redirection vers la liste des utilisateurs
        }

        return $this->render('admin_user/new.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    // Route pour afficher un utilisateur admin spécifique
    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(AdminUser $adminUser): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Restreindre l'accès aux admins uniquement

        return $this->render('admin_user/show.html.twig', [
            'admin_user' => $adminUser,
        ]);
    }

    // Route pour éditer un utilisateur admin
    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminUser $adminUser, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Restreindre l'accès aux admins uniquement

        $form = $this->createForm(AdminUserType::class, $adminUser, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Sauvegarder les modifications

            return $this->redirectToRoute('admin_user_index'); // Rediriger vers la liste des utilisateurs
        }

        return $this->render('admin_user/edit.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    // Route pour supprimer un utilisateur admin
    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, AdminUser $adminUser, AdminUserRepository $adminUserRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Restreindre l'accès aux admins uniquement

        // Empêcher la suppression de son propre compte
        if ($this->getUser() === $adminUser) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_user_index');
        }

        // Empêcher la suppression s'il reste un seul administrateur
        $remainingAdmins = $adminUserRepository->count(['roles' => ['ROLE_ADMIN']]);
        if ($remainingAdmins <= 1) {
            $this->addFlash('error', 'Il doit rester au moins un administrateur.');
            return $this->redirectToRoute('admin_user_index');
        }

        // Vérifier le token CSRF avant de supprimer
        if ($this->isCsrfTokenValid('delete' . $adminUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adminUser);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
