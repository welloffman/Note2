<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\Dir;
use Acme\ModelBundle\Entity\Note;
use Acme\MainBundle\Helper\NavList;
use Acme\MainBundle\Helper\Breadcrumbs;
use Symfony\Component\HttpFoundation\Response;

class NotesController extends Controller {

	/**
	 * Вывод стартовой страницы записей
	 */
    public function indexAction() {
        return $this->render('AcmeMainBundle:Notes:index.html.twig', array(
            'json_string' => json_encode(array())
        ));
    }

    /**
     * Получение post запроса и сохранение пришдших элементов в базу
     * @return Response
     */
    public function saveNavListAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;
        $nav_list_data = $request->get('items');
        
        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        foreach($nav_list_data as $item) {
            if($item['type'] == 'dir') {
                $dir = $dir_rep->find($item['id']);
                if($dir && $dir->getUserId() == $item['user_id']) {
                    $dir->setTitle($item['title']);
                    $dir->setPid($item['pid']);

                    $position = $dir->getPosition();
                    $position->setPos($item['position']['pos']);
                    $dir->setPosition($position);
                    $em->persist($dir);
                }
            }
            else if($item['type'] == 'note') {
                $note = $note_rep->find($item['id']);
                if($note && $note->getDir()->getUserId() == $user->getId()) {
                    $note->setTitle($item['title']);
                    $note->setContent($item['content']);

                    $position = $note->getPosition();
                    $position->setPos($item['position']['pos']);
                    $note->setPosition($position);

                    $dir_n = $note->getDir();

                    $em->persist($note);
                }
            }
        }
        $em->flush();


        return new Response( json_encode(array('success' => true)) );
    }

    /**
     * Получение записи
     * @return Response
     */
    public function getNoteAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        //todo: Сделать проверку доступа пользователя к записи

        $note = $note_rep->find( $request->get('note_id') )->toArray();

        return new Response( json_encode(array( 'success' => true, 'note' => $note)) );
    }

    /**
     * Получение списка записей и разделов
     * @return Response
     */
    public function getNavListAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');

        if($request->get('dir_id')) {
            $dir = $dir_rep->findOneBy( array('id' => $request->get('dir_id'), 'user_id' => $user->getId()) );
        }
        else {
            $dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );
        }

        $nav_list = new NavList($this->getDoctrine(), $user);
        $nav_list->fetch($dir)->sortByPosition();

        return new Response( json_encode(array( 
            'success' => true, 
            'items' => $nav_list->getItems(), 
            'dir_id' => $dir->getId()
        )) );
    }

    /**
     * Получение хлебных крошек
     * @return Response
     */
    public function getBreadcrumbsAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');

        $breadcrumbs = new Breadcrumbs($this->getDoctrine(), $user);

        if($request->get('dir_id')) {
            $dir = $dir_rep->findOneBy( array('id' => $request->get('dir_id'), 'user_id' => $user->getId()) );
            $breadcrumbs->fetch($dir);
        }
        else {
            $dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );
        }

        return new Response( json_encode(array( 
            'success' => true,
            'dir_title' => $dir->getTitle(), 
            'items' => $breadcrumbs->getData() 
        )) );
    }
}
