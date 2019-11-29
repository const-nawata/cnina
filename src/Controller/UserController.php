<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

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
//______________________________________________________________________________

	/**
	 * @Route("/logout", name="app_logout")
	 * @throws Exception
	 */
	public function logout()
	{
		throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
	}
//______________________________________________________________________________

	/**
	 * @Route("/register", name="user_register")
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @return Response
	 */
	public function register( Request $request, UserPasswordEncoderInterface $passwordEncoder ): Response
	{
		$user = new User();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'register']]);
		$form->handleRequest($request);

		$errs = '';

		if ($form->isSubmitted() && $form->isValid()) {
			$user->setPassword( $passwordEncoder->encodePassword( $user, $form->get('plainPassword')->getData()));

			$user->setConfirmed(false);
			$user->setRoles(['ROLE_USER']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			/*		//INFO: Left for any case.

						return $guardHandler->authenticateUserAndHandleSuccess(
							$user,
							$request,
							$authenticator,
							'register'
						);
			*/


			//TODO: Create page about successful registering. End implement confirmation mail sending.
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

		$err_field = $err_mess = $scs_message = '';

		if( $form->isSubmitted() ) {
			$pass	= $form->get('plainPassword')->getData();

			if( !$form->isValid()){
				$error		= $this->getFormError( $form );
				$err_mess	= $error['message'];
				$err_field	= $error['field'];
			}

			$err_mess	= (($err_field != 'plainPassword') || (!empty($pass) && strlen($pass) < 6)) ? $err_mess : '' ;

			if(empty($err_mess)){

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
			'errMessage'	=> $err_mess,
			'scsMessage'	=> $scs_message
		]);
	}
//______________________________________________________________________________
}
