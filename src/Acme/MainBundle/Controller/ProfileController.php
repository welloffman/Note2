<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Acme\ModelBundle\Entity\User;

class ProfileController extends Controller {
    public function indexAction() {
    	$user = $this->get('security.context')->getToken()->getUser();
    	$req = $this->getRequest()->request;
    	$error = array();
    	$success = array();

    	if($req->get('is_update')) {
	        if(!$req->get('login')) {
                $error = array('message' => 'Введите имя пользователя');
            } else if(strlen($req->get('newpass')) < 6) {
                $error = array('message' => 'Введите новый пароль не менее 6-ти символов');
            } else if( $req->get('email') != $user->getEmail() && $this->getDoctrine()->getManager()->getRepository('AcmeModelBundle:User')->findOneBy(array('email' => $req->get('email'))) ) {
                $error = array('message' => 'Такой Email уже используется');
            } else {
                $factory = $this->get('security.encoder_factory');
		        $encoder = $factory->getEncoder($user);
		        $enc_old_password = $encoder->encodePassword($req->get('oldpass'), $user->getSalt());
		        if($user->getPassword() != $enc_old_password) {
		        	$error = array('message' => 'Неправильный старый пароль');
		        } else {
		        	$enc_new_password = $encoder->encodePassword($req->get('newpass'), $user->getSalt());
		        	$user->setPassword($enc_new_password);
		        	$user->setUsername($req->get('login'));
		        	$user->setEmail($req->get('email'));
		        	$this->getDoctrine()->getEntityManager()->flush();
		        	$success = array('message' => 'Данные успешно сохранены');
		        }
            }
    	}
    	
    	return $this->render( 'AcmeMainBundle:Profile:index.html.twig', array('login' => $user->getUsername(), 'email' => $user->getEmail(), 'error' => $error, 'success' => $success) );
    }
}
