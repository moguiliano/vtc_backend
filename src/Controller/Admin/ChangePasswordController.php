<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin;

class ChangePasswordController extends AbstractController
{
    #[Route('/admin/change-password', name: 'admin_change_password')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
        Security $security,
    ): Response {
        /** @var Admin $admin */
        $admin = $this->getUser();

        $error = null;
        $success = false;

        if ($request->isMethod('POST')) {
            $current  = $request->request->get('current_password');
            $new      = $request->request->get('new_password');
            $confirm  = $request->request->get('confirm_password');

            if (!$hasher->isPasswordValid($admin, $current)) {
                $error = 'Mot de passe actuel incorrect.';
            } elseif (strlen($new) < 8) {
                $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            } elseif ($new !== $confirm) {
                $error = 'Les deux mots de passe ne correspondent pas.';
            } else {
                $admin->setPassword($hasher->hashPassword($admin, $new));
                $em->flush();
                $success = true;
            }
        }

        return $this->render('admin/change_password.html.twig', [
            'error'   => $error,
            'success' => $success,
        ]);
    }
}
