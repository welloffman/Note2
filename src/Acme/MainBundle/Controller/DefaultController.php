<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\User;

class DefaultController extends Controller {
    public function indexAction() {
    	$id = 1;
    	$user = $this->getDoctrine()->getRepository('AcmeModelBundle:User')->find($id);
		if (!$user) {
	        throw $this->createNotFoundException('No user found for id ' . $id);
	    }

        return $this->render('AcmeMainBundle:Default:index.html.twig', array('user_login' => $user->getUsername()));
    }

    public function headerAction() {
    	return $this->render('AcmeMainBundle:Default:header.html.twig');
    }
}
