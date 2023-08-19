<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/register')]
    public function register(Request $request, UserRepository $repo, UserPasswordHasherInterface $hasher): Response
    {
        $errors = [];
        $formData = $request->request->all();
        if(!empty($formData)) {
            $user=new User($formData['email'],$formData['password']);
            if($user->getPassword() != $formData['repeat-password']) {
                $errors[] = 'Password did not match.';
            }
            if($repo->findByEmail($user->getEmail())) {
                $errors[] = 'User already exists with that email.';
            }
            //Si on a pas d'erreur alors on fait persister le user
            if(empty($errors)) {
                //On hash le mot de passe du user
                $hash = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hash);
                $repo->persist($user);
            }


        }
        return $this->render('auth/register.html.twig', [
            'errors' => $errors
        ]);
    }

    #[Route("/login")]

    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastEmail = $authenticationUtils->getLastUsername();    
          return $this->render('auth/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_email' => $lastEmail,
            'error'=> $error,
        ]);
    }

    #[Route("/protected")]
    public function protected() {
        return $this->render('auth/protected.html.twig',[
        ]);
    }
}
