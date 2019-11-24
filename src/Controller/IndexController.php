<?php
namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//	Annotations
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 * @author Nawata
 */
class IndexController extends ControllerCore
{

/**
 * @Route("/", name="index")
 * @param Request $request,
 * @return Response
 */
	public function index(Request $request):Response
	{
		return $this->show($request,'pages/index.twig');
	}
//______________________________________________________________________________

}
