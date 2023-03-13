<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/superAdmin')]
class AdminRoleManagementController extends AbstractController
{
    #[Route('/role', name: 'admin.role.user')]
    public function userManagement(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('admin/user/role.user.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/role/{id}', name: 'admin.user.edit')]
    public function userEdit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $role = ["ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"];
        $resultat = array_diff($role, [$user->getRoles()[0]]);

        if($request->request->count()){
            if(in_array($request->request->get('roles'), $role)) {
                $user->setRoles([$request->request->get('roles')]);
                $manager->flush();
                $this->addflash('success', $user->getUsername() . ' a été modifié avec succés');
                return $this->redirectToRoute('admin.role.user');
            }
        }

        return $this->render('admin/user/edit.user.html.twig', [
            'user' => $user,
            'choices' => $resultat
        ]);       
    }
}
