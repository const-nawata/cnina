<?php
namespace App\Controller;

use App\Entity\Currency;
use App\Form\CurrencyForm;
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
		$post	= $request->query->all();

		$fields	= [
			['field' => 'name',		'title' => 'form.denomination', 	'sortable' => true,		'searchable' => true,	'css' => '' ],
			['field' => 'ratio',	'title' => 'form.ratio-currency',	'sortable' => true,		'searchable' => true,	'css' => 'number-list-sell' ],
			['field' => 'symbol',	'title' => 'form.sign',				'sortable' => false,	'searchable' => false,	'css' => '' ],
			['field' => 'sample',	'title' => 'title.sample',			'sortable' => false,	'searchable' => false,	'css' => 'number-list-sell' ]
		];

		unset($fields[2]);	// Don't show symbol. Show combined value (sample).
		$fields	= array_values($fields);


		$page	= $request->query->getInt('page', 1);
		$limit	= $request->query->getInt('limit', 10);;


		$pagination	= $this->getDoctrine()
			->getRepository(Currency::class)->getPaginator($page, $limit);

		return $this->show($request,'layouts/base.table.twig', ['pagination' => $pagination, 'fields' => $fields, 'headerTitle'	=> 'title.currency',
			'itemPath'		=> 'currency_form',


			'table'	=> [
				'width' => 5,

				'input'		=> [
					'search'=> [
						'value'	=> empty($post['searchStr']) ? '' : $post['searchStr']
					]
				]
			],


			]);

/*


		//	Next dummy call is necessary to get correct is_after_pos value. Hernya kakaya to.
		$this->getDoctrine()->getRepository(Currency::class)->findAll();

		$table = $this->createDataTable([])
			->setName('list_category')
			->setTemplate('pages/currency/table.template.twig')
			->add('name', TextColumn::class,[])
			->add('ratio', NumberColumn::class, ['searchable' => false, 'className' => 'number-list-sell'])
			->add('symbol', TextColumn::class,['className' => 'number-list-sell', 'data' => function( Currency $currency, $symbol ) {
				$example	= rand(100,999).'.'.rand(0,9).rand(0,9);
				return $currency->getIsAfterPos() ? $example.$symbol : $symbol.$example;
			}])

			->createAdapter(ORMAdapter::class, [
				'entity' => Currency::class
			])
			->handleRequest($request);

		if ($table->isCallback()) {
			return $table->getResponse();
		}

		return $this->show($request, 'layouts/base.table.twig', [
			'table'	=> [
				'data'	=> $table,
				'width' => 5,

				'input'		=> [
					'search'=> [
						'value'	=> empty($post['searchStr']) ? '' : $post['searchStr']
					]
				]
			],

			'headerTitle'	=> 'title.currency',
			'itemPath'		=> 'currency_form',
		]);

*/




	}
//______________________________________________________________________________

	/**
	 * @param Currency $currency
	 * @return FormInterface
	 */
	private function generateCurrencyForm(Currency $currency ): FormInterface
	{
		return $this->createForm(CurrencyForm::class, $currency, [
			'action' => $this->generateUrl('currency_save'),
			'method' => 'POST'
			,'attr' => [
				'id'			=> 'dialog_form',
				'currency_id'	=> $currency->getId() ?? 0,
			]
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/form", name="currency_form")
 * @param Request $request
 * @return JsonResponse
 */
	public function getCurrencyForm(Request $request):JsonResponse
	{
		$repo		= $this->getDoctrine()->getRepository(Currency::class);
		$id			= $request->query->get('id');
		$data		= $repo->getFormData( $id );
		$currency	= $data['entity'];

		$content	= $this->render('dialogs/currency_modal.twig',[
			'currencyForm'	=> $this->generateCurrencyForm($currency)->createView(),
			'currency'		=> $currency,
		])->getContent();

		return new JsonResponse([ 'success'	=> true, 'html' => $content ]);
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
