<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\Dir;
use Acme\MainBundle\Helper\NavList;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    public function indexAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );

        print_r($dir->toArray()); exit;

        return $this->render('AcmeMainBundle:Default:test.html.twig', array(
            'items' => $note
        ));
    }
}
