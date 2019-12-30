<?php
namespace App\Controller;

/**
 * Created by PhpStorm.
 * User: Nawata
 * Date: 22.11.2019
 * Time: 16:30
 */

use App\DTO\DeleteEntityDto;
use App\Form\DeleteEntityForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Contracts\Translation\TranslatorInterface;

//use App\CInterface\AuxToolsInterface;

class ControllerCore  extends AbstractController
{

	protected $params;
	protected $logger;
	protected $translator;
//	protected $tools;


	public function __construct( ParameterBagInterface $params, LoggerInterface $logger, TranslatorInterface $translator
//		, AuxToolsInterface $tools
	){
		$this->params = $params;
		$this->logger = $logger;
		$this->translator	= $translator;
//		$this->tools	= $tools;
	}
//______________________________________________________________________________

	/**
	 * extends render method
	 * @param string $view
	 * @param array $parameters
	 * @param Response|null $response
	 * @param Request $request
	 * @return Response
	 */
	protected function show(Request $request, string $view, array $parameters = [], Response $response = null ): Response
	{
		$parameters['locale_name']		= 'langs.'.$request->getLocale();
		$parameters['shop_name']	= $this->params->get('shop_name');

		return $this->render( $view, $parameters, $response );
	}
//______________________________________________________________________________

	protected function getQueryStr( Request $request ){
		$params	= $request->query->all();
		$query	= [];
		foreach( $params as $param => $value ){
			$query[]	= $param.'='.$value;
		}
		$query	= implode('&', $query);
		return empty($query) ? '' : '?'.$query;
	}
//______________________________________________________________________________

/*
	protected function createDataTable(array $options = [])
	{
		return $this->datatableFactory->create($options);
	}
//______________________________________________________________________________
*/


	protected function getFormError( $form ){
		$fields = $form->all();

		$message	=
		$error_field= '';

		foreach ( $fields as $field ) {
			$message	= $field->getErrors(true)->__toString();

			if(!empty($message)){
				$message	= str_replace( 'ERROR: ', '', $message);
				$error_field	= $field->getName();
				break;
			}
		}

		return [ 'message' => $message, 'field' => $error_field ];
	}
//______________________________________________________________________________

	protected function getDeleteEntityFormInstance( int $id, string $entityName): FormInterface
	{
		$form_data	= new DeleteEntityDto();
		$form_data->setId( $id );
		$form_data->setEntityName( ucfirst( $entityName ) );

		return $this->createForm( DeleteEntityForm::class, $form_data,
			[
				'action' => $this->generateUrl(strtolower($entityName).'_delete'),
				'method' => 'POST'
			]
		);
	}

	protected function getDeleteEntityFormView( int $id, string $entityName): string
	{
		return $this->render('dialogs/delete_entity_form.twig', [
			'form'	=> $this->getDeleteEntityFormInstance( $id, $entityName)->createView()
		])->getContent();

	}
//______________________________________________________________________________
	protected function deleteEntity(Request $request): array
	{
		$post	= $request->request->all()['delete_entity_form'];
		$error	= ['message' => ''];

		$em	= $this->getDoctrine()->getManager();
		$con= $em->getConnection();
		$con->beginTransaction();

		try {
			$form	= $this->getDeleteEntityFormInstance( $post['id'], $post['entityName']);

			$form->handleRequest( $request );

			if( $success = ($form->isSubmitted() && $form->isValid()) ) {
				$repo		= $this->getDoctrine()->getRepository('App\\Entity\\'.$post['entityName']);
				$currency	= $repo->find($post['id']);
				$em->remove($currency);
				$em->flush();
				$con->commit();
			}else{
				$error_content	= $this->getFormError( $form );;
				throw new \Exception(serialize( $error_content ), 1);
			}
		} catch ( \Exception $e) {
			$success	= false;
			$message	= $e->getMessage();

			$error	=  ( $e->getCode() == 1 )
				? unserialize( $message )
				: ['message' => $message.' / '.$e->getCode()];

			$con->rollBack();
		}

		return [
			'success'	=> $success,
			'error'		=> $error
		];
	}
//______________________________________________________________________________

}