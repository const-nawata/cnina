<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class CurrencyController
 * @Route("/user")
 * @package App\Controller
 */
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
	 * @param TranslatorInterface $translator
	 * @return Response
	 */
	public function register( Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator ): Response
	{
		$user = new User();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'register', 'level'=>'user']]);
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
	 * @deprecated
	 *
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
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'edit', 'level'=>'user']]);
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

	/**
	 * @Route("/list", name="user_list")
	 * @param Request $request
	 * @return Response
	 */
	public function getUserList( Request $request): Response
	{
		$page	= $request->query->getInt('page', 1);
		$limit	= $request->query->getInt('limit', 10);
		$search	= $request->query->get('search', '');

		$pagination	= $this->getDoctrine()->getRepository(User::class)->getPaginator($page, $limit, $search);

		return $this->show($request,'layouts/base.table.twig', ['pagination' => $pagination, 'entityTitle'	=> 'title.user',
			'table'	=> [
//				'width' => 5
			],
		]);

	}
//______________________________________________________________________________

	/**
	 * @Route("/deleteform", name="user_delete_form")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function showDelUserForm( Request $request ): JsonResponse
	{
		return new JsonResponse([
			'success'	=> true,
			'html'		=> $this->getDeleteEntityFormView( $request->query->get('id'), 'Currency' )
		]);
	}
//______________________________________________________________________________

	/**
	 * @Route("/form", name="user_form")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function showUserForm(Request $request):JsonResponse
	{
		$repo	= $this->getDoctrine()->getRepository(User::class);
		$id		= $request->query->get('id');
		$data	= $repo->getFormData( $id );
		$user	= $data['entity'];
		$user->setPassword('');

		$content	= $this->render('dialogs/user_form.twig',[
			'form'	=> $this->createForm(UserForm::class, $user,
				[
					'action' => $this->generateUrl('user_save'),
					'method' => 'POST',
					'attr' => ['mode'=> ($id > 0 ? 'edit' : 'register'), 'level'=>'admin']
				])->createView(),
			'user'		=> $user,
		])->getContent();

		return new JsonResponse([ 'success'	=> true, 'entityId' => $id, 'html' => $content ]);
	}
//______________________________________________________________________________

	/**
	 * @Route("/save", name="user_save")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function saveUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator ): JsonResponse
	{
		$post	= $request->request->all()['user_form'];
		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con		= $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo	= $this->getDoctrine()->getRepository(User::class);
			$data	= $repo->getFormData($post['id']);
			$user	= $data['entity'];

			$form = $this->createForm(UserForm::class, $user,
				[
					'action' => $this->generateUrl('user_save'),
					'method' => 'POST',
					'attr' => ['mode'=> ($post['id'] > 0 ? 'edit' : 'register')]
				]);

			$form->handleRequest( $request );

			$err_field = $err_mess = $scs_message = '';

			if( $form->isSubmitted() ) {
				$pass	= $post['plainPassword'];

				if( !$form->isValid()){
					$error_content		= $this->getFormError( $form );
					$err_mess	= $error_content['message'];
					$err_field	= $error_content['field'];
				}

				$err_mess	= (($err_field != 'plainPassword') || (!empty($pass) && strlen($pass) < 6)) ? $err_mess : '' ;

				if(empty($err_mess) || $post['id'] > 0){
					$post['plainPassword']	= !empty($pass)
						? $passwordEncoder->encodePassword( $user, $post['plainPassword'])
						: $user->getPassword();

					$repo->saveFormData( $post );
					$search	= $user->getUsername();
					$con->commit();

//					$scs_message	= $translator->trans('message.savedsuccess',[],'prompts');
				}else{
					$error_content	= $this->getFormError( $form );
					throw new \Exception(serialize( $error_content ), 1);
				}
			}
			$success	= true;

		} catch ( \Exception $e) {
			$success	= false;
			$message	= $e->getMessage();

			$error	=  ( $e->getCode() == 1 )
				? unserialize( $message )
				: ['message' => $message.' / '.$e->getCode(), 'field' => 'general'];

			$con->rollBack();
		}

		return new JsonResponse([
			'success'	=> $success,
			'error'		=> $error,

			'table'	=> [
				'input'	=> [
					'search'=> [
						'value'	=> $search
					]
				]
			]
		]);
	}
//______________________________________________________________________________

}
