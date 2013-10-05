<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Acme\ModelBundle\Entity\User;

class DefaultController extends Controller {
	public function indexAction() {
		if( $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
			return $this->redirect($this->generateUrl('notes'));
		}

		$request = $this->getRequest();
		$session = $request->getSession();

		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		return $this->render('AcmeMainBundle:Default:index.html.twig', array(
			'last_username' => $session->get(SecurityContext::LAST_USERNAME),
			'error' => $error,
		));
	}

	public function headerAction() {
		return $this->render('AcmeMainBundle:Default:header.html.twig');
	}
}
