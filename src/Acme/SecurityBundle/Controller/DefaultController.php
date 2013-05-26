<?php

namespace Acme\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Acme\ModelBundle\Entity\User;

class DefaultController extends Controller {
    public function indexAction($name) {
        return $this->render('AcmeSecurityBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * Страница авторизации
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'AcmeSecurityBundle:Default:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    /**
     * Страница регистрации
     */
    public function registrationAction() {
        $error = null;

        $req = $this->getRequest();
        $username = $req->request->get('username');
        $password = $req->request->get('password');
        $email = $req->request->get('email');

        if($username || $password || $email) {
            $user = new User();
            $user->setUsername($username);

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $enc_password = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($enc_password);

            $user->setEmail($email);
            $user->setCreated(new \DateTime());

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render(
            'AcmeSecurityBundle:Default:registration.html.twig', array(
                'error' => $error
            )
        );
    }
}
