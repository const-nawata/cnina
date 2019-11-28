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
		return $this->show($request,'pages/index.twig', ['method_name' => 'Index']);
	}
//______________________________________________________________________________

/**
 * @Route("/dummy1", name="dummy1")
 * @param Request $request,
 * @return Response
 */
	public function dummy1(Request $request):Response
	{
		return $this->show($request,'pages/index.twig', ['method_name' => 'Dummy 1']);
	}
//______________________________________________________________________________

/**
 * @Route("/dummy2", name="dummy2")
 * @param Request $request,
 * @return Response
 */
	public function dummy2(Request $request):Response
	{
		return $this->show($request,'pages/index.twig', ['method_name' => 'Dummy 2']);
	}
//______________________________________________________________________________

}
