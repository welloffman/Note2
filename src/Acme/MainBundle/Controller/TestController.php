<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\Dir;
use Acme\MainBundle\Helper\NavList;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    public function indexAction() {
        /*$em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        $note = $note_rep->find(1944);
        $note->setTitle('<p>444-----------</p>');
        $items = $note->toArray(); 

        return $this->render('AcmeMainBundle:Default:test.html.twig', array(
            'items' => $items
        ));*/


        $user = $this->get('security.context')->getToken()->getUser();
        //$request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        //todo: Сделать проверку доступа пользователя к записи

        $note = $note_rep->find( 1945 );

        print_r($note->toArray());

        return $this->render('AcmeMainBundle:Default:test.html.twig', array(
            'items' => $note
        ));
    }
}
