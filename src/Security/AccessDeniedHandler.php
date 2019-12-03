<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
	protected $urlGenerator;

	public function __construct( UrlGeneratorInterface $urlGenerator ){
		$this->urlGenerator	= $urlGenerator;
	}

	public function handle(Request $request, AccessDeniedException $accessDeniedException)
	{
		return new RedirectResponse($this->urlGenerator->generate('index'));
	}
}
