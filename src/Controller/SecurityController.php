<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends AbstractController
{
    #[Route('/Connexion', name: 'login', methods: ["GET", "POST"])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('home');
        }
        
        // Récupération l' URL précédent
        $referer = $request->headers->get('referer');
        $urlCurrent = "http://" . $request->headers->get('host') . "/" . $request->get('_route');
        $compareUrl = 1;

        if($urlCurrent == $referer) 
        {
            $compareUrl = 0;
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'referer' => $referer,
            'compareUrl' => $compareUrl, 
        ]);
    }


    #[Route('/Inscription', name: 'registration')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, RequestStack $requeststack)
    {

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash)
                 ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();

            //Se logger directement après l'enregistrement
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $requeststack->getSession()->set('_security_main', serialize($token));

            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/Déconnexion', name: 'logout')]
    public function logout()
    {
    }
}
