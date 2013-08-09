<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\User;

class ProfileController extends Controller {
    public function indexAction() {
    	return $this->render('AcmeMainBundle:Profile:index.html.twig');
    }
}
