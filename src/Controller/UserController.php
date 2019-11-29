<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends ControllerCore
{
	/**
	 * @Route("/login", name="app_login")
	 * @param Request $request
	 * @param AuthenticationUtils $authenticationUtils
	 * @return Response
	 */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

		return $this->show($request,'pages/user/login-form.twig', ['last_username' => $lastUsername, 'error' => $error] );
    }

	/**
	 * @Route("/logout", name="app_logout")
	 * @throws Exception
	 */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
