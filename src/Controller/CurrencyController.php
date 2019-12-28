<?php
namespace App\Controller;

use App\Form\CurrencyForm;
use App\Form\DeleteForm;
use App\Entity\Currency;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CurrencyController
 * @Route("/currency")
 * @package App\Controller
 */
class CurrencyController extends ControllerCore
{

	/**
	 * @Route("/list", name="currency_list")
	 * @param Request $request
	 * @return Response
	 */
	public function getCurrencyList( Request $request): Response
	{
		$page	= $request->query->getInt('page', 1);
		$limit	= $request->query->getInt('limit', 10);
		$search	= $request->query->get('search', '');

		$pagination	= $this->getDoctrine()->getRepository(Currency::class)->getPaginator($page, $limit, $search);

		return $this->show($request,'layouts/base.table.twig', ['pagination' => $pagination, 'entityTitle'	=> 'title.currency',
			'editPath' => 'currency_form', 'deletePath' => 'del_currency_form',

			'table'	=> [
				'width' => 5
			],
		]);

	}
//______________________________________________________________________________

	/**
	 * @Route("/delete", name="currency_delete")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function deleteCurrency(Request $request): JsonResponse
	{
		$post	= $request->request->all()['currency_form'];

		return new JsonResponse([
			'success'	=> true,
			'error'		=> null
		]);
	}
//______________________________________________________________________________

	/**
	 * @Route("/delcurrencyform", name="del_currency_form")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function showDelCurrencyForm(Request $request ): JsonResponse
	{
		$id	= $request->query->get('id');

		$content	= $this->render('dialogs/delete_modal.twig',[
			'deleteForm'	=> $this->createForm( DeleteForm::class, ['id' => $id],
			[
				'action' => $this->generateUrl('currency_delete'),
				'method' => 'POST'
			]
		)->createView()])->getContent();

		return new JsonResponse([ 'success'	=> true, 'html' => $content ]);
	}
//______________________________________________________________________________

	/**
	 * @param Currency $currency
	 * @return FormInterface
	 */
	private function generateCurrencyForm(Currency $currency ): FormInterface
	{
		return $this->createForm(CurrencyForm::class, $currency,
		[
			'action' => $this->generateUrl('currency_save'),
			'method' => 'POST'
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/form", name="currency_form")
 * @param Request $request
 * @return JsonResponse
 */
	public function showCurrencyForm(Request $request):JsonResponse
	{
		$repo		= $this->getDoctrine()->getRepository(Currency::class);
		$id			= $request->query->get('id');
		$data		= $repo->getFormData( $id );
		$currency	= $data['entity'];

		$content	= $this->render('dialogs/currency_form.twig',[
			'form'	=> $this->generateCurrencyForm($currency)->createView(),
			'currency'		=> $currency,
		])->getContent();

		return new JsonResponse([ 'success'	=> true, 'entityId' => $id, 'html' => $content ]);
	}
//______________________________________________________________________________

/**
 * @Route("/save", name="currency_save")
 * @param Request $request
 * @return JsonResponse
 */
	public function saveCurrency(Request $request): JsonResponse
	{
		$post	= $request->request->all()['currency_form'];
		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con		= $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo		= $this->getDoctrine()->getRepository(Currency::class);
			$data		= $repo->getFormData($post['id']);
			$currency	= $data['entity'];

			$form = $this->generateCurrencyForm($currency);

			$form->handleRequest( $request );

			if( $success = ($form->isSubmitted() && $form->isValid()) ) {
				$repo->saveFormData( $post );
				$search	= $currency->getName();
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
