<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\User;

class DefaultController extends Controller {
    public function indexAction() {
    	return $this->render('AcmeMainBundle:Default:index.html.twig');
    }

    public function headerAction() {
    	return $this->render('AcmeMainBundle:Default:header.html.twig');
    }
}
