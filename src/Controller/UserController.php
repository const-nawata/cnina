<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use App\Security\LoginFormAuthenticator as LoginAuth;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends ControllerCore
{
	/**
	 * @Route("/login", name="user_login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @return Response
	 */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

	/**
	 * @Route("/logout", name="user_logout")
	 * @throws Exception
	 */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }


	/**
	 * @Route("/register", name="user_register")
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param GuardAuthenticatorHandler $guardHandler
	 * @param LoginAuth $authenticator
	 * @return Response
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginAuth $authenticator): Response
	{
		$user = new User();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'register']]);
		$form->handleRequest($request);

		$errs = '';

		if ($form->isSubmitted() && $form->isValid()) {
			$user->setPassword(
				$passwordEncoder->encodePassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$user->setConfirmed(false);

			$user->setRoles(['ROLE_USER']);

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();




/*
			return $guardHandler->authenticateUserAndHandleSuccess(
				$user,
				$request,
				$authenticator,
				'register'
			);
*/
			return $this->show($request, 'dummy.html.twig', []);
		}else{
			$error			= $this->getFormError( $form );
			$errs			= $error['message'];
		}

		return $this->show($request, 'pages/user/user-form.twig', [
			'userForm' => $form->createView(),
			'title' => 'title.registering',
			'errMessage'	=> $errs,
			'scsMessage'	=> ''
		]);
	}
//______________________________________________________________________________

	/**
	 * @Route("/edit", name="user_edit")
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TranslatorInterface $translator
	 * @return Response
	 *
	 */
	public function edit( Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator ): Response
	{
		$user = $this->getUser();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'edit']]);
		$form->handleRequest($request);

		$error_field = $errs = $scs_message = '';

		if ($form->isSubmitted() ) {
			$pass	 = $form->get('plainPassword')->getData();

			if( !$form->isValid())
				list($errs, $error_field)	= $this->getFormError( $form );

			$errs	= (($error_field != 'plainPassword') || (!empty($pass) && strlen($pass) < 6)) ? $errs : '' ;

			if(empty($errs)){

				!empty($pass)
					? $user->setPassword($passwordEncoder->encodePassword( $user, $pass))
					: $user->setPassword($user->getPassword());

				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();
				$scs_message	= $translator->trans('message.savedsuccess',[],'prompts');
			}
		}

		return $this->show($request, 'pages/user/user-form.twig', [
			'userForm'		=> $form->createView(),
			'title'			=> 'title.edit',
			'errMessage'	=> $errs,
			'scsMessage'	=> $scs_message
		]);
	}
//______________________________________________________________________________

}
