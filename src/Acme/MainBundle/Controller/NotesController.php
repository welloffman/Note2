<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\ModelBundle\Entity\Dir;
use Acme\ModelBundle\Entity\Note;
use Acme\ModelBundle\Entity\PositionDir;
use Acme\ModelBundle\Entity\PositionNote;
use Acme\MainBundle\Helper\NavList;
use Acme\MainBundle\Helper\Breadcrumbs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotesController extends Controller {

	/**
	 * Вывод стартовой страницы записей
	 */
    public function indexAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );

        if(!$dir) {
            $position = new PositionDir();
            $position->setPos(0);
            
            $dir = new Dir();
            $dir->setUserId($user->getId());
            $dir->setTitle('Главный раздел');
            $dir->setPosition($position);

            $em->persist($dir);
            $em->persist($position);
            $em->flush();
        }
        //$em->remove($dir);
        //$em->flush();

        return $this->render('AcmeMainBundle:Notes:index.html.twig', array(
            'json_string' => json_encode(array())
        ));
    }

    /**
     * Получение post запроса и сохранение пришдших элементов в базу
     * @return JsonResponse
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
                    if(isset($item['content'])) $note->setContent($item['content']);

                    $position = $note->getPosition();
                    $position->setPos($item['position']['pos']);
                    $note->setPosition($position);

                    $dir_n = $note->getDir();

                    $em->persist($note);
                }
            }
        }
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    /**
     * Получение записи
     * @return JsonResponse
     */
    public function getNoteAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        $note = $note_rep->find( $request->get('note_id') );
        if(!$note) return new Response( json_encode(array('success' => false)) );

        $parent_dir = $note->getDir();

        if($parent_dir->getUserId() != $user->getId()) return new Response( json_encode(array('success' => false)) );

        $note_data = $note->toArray();
        $note_data['parent_dir'] = $parent_dir->getId();

        return new JsonResponse(array('success' => true, 'note' => $note_data));
    }

    /**
     * Получение раздела
     * @return JsonResponse
     */
    public function getDirAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;

        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');

        $dir = $dir_rep->findOneBy( array('id' => $request->get('dir_id'), 'user_id' => $user->getId()) );
        if(!$dir) return new Response( json_encode(array('success' => false)) );

        return new JsonResponse(array('success' => true, 'dir' => $dir->toArray()));
    }

    /**
     * Получение списка записей и разделов
     * @return JsonResponse
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

        return new JsonResponse(array( 
            'success' => true, 
            'items' => $nav_list->getItems(), 
            'dir_id' => $dir->getId()
        ));
    }

    /**
     * Получение хлебных крошек
     * @return JsonResponse
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

        return new JsonResponse(array( 
            'success' => true,
            'dir_title' => $dir->getTitle(), 
            'items' => $breadcrumbs->getData() 
        ));
    }

    /**
     * Сохранение Раздела (Если раздел существует, позицию и родительский раздел не перезаписывает)
     * @return JsonResponse
     */
    public function saveDirAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;
        $dir_data = $request->get('dir_data');
        
        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        
        if(isset($dir_data["id"])) {
            $dir = $dir_rep->findOneBy( array('id' => $dir_data['id'], 'user_id' => $user->getId()) );
            if(!$dir) return new Response( json_encode(array('success' => false)) );
        } else {
            if(isset($dir_data['pid'])) {
                $parent_dir = $dir_rep->findOneBy( array('id' => $dir_data['pid'], 'user_id' => $user->getId()) );
            } else {
                $parent_dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );
            }
            if(!$parent_dir) return new Response( json_encode(array('success' => false)) );

            $nav_list = new NavList($this->getDoctrine(), $user);

            $position = new PositionDir();
            $position->setPos($nav_list->fetch($parent_dir)->getLength() + 1);
            $em->persist($position);

            $dir = new Dir();
            $dir->setPosition($position);
            $dir->setPid($parent_dir->getId());
            $dir->setUserId($user->getId());
        }

        $dir->setTitle($dir_data['title']);

        $em->persist($dir);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    /**
     * Сохранение записи (Если запись существует, позицию и родительский раздел не перезаписывает)
     * @return JsonResponse
     */
    public function saveNoteAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;
        $note_data = $request->get('note_data');
        
        $em = $this->getDoctrine()->getManager();
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        if(isset($note_data["id"])) {
            $note = $note_rep->find($note_data['id']);
            if(!$note) return new Response( json_encode(array('success' => false)) );
        }
        else {
            $dir_rep = $em->getRepository('AcmeModelBundle:Dir');

            if(isset($note_data['pid'])) {
                $dir = $dir_rep->findOneBy( array('id' => $note_data['pid'], 'user_id' => $user->getId()) );
            } else {
                $dir = $dir_rep->findOneBy( array('pid' => null, "user_id" => $user->getId()) );
            }
            if(!$dir) return new Response( json_encode(array('success' => false)) );

            $nav_list = new NavList($this->getDoctrine(), $user);

            $note = new Note();
            $position = new PositionNote();
            $position->setPos($nav_list->fetch($dir)->getLength() + 1);
            $em->persist($position);

            $note->setDir($dir);
            $note->setPosition($position);
        }

        $note->setTitle($note_data['title']);
        $note->setContent($note_data['content']);

        $em->persist($note);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    public function deleteAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $request = $this->get('request')->request;
        $em = $this->getDoctrine()->getManager();
        $dir_rep = $em->getRepository('AcmeModelBundle:Dir');
        $note_rep = $em->getRepository('AcmeModelBundle:Note');

        $id_list = $request->get('ids');
        if( !isset($id_list) ) return new JsonResponse(array('success' => false));

        $ids = array_merge( array('dir'=>array(), 'note'=>array()), $id_list );
        foreach($ids['note'] as $note_id) {
            $note = $note_rep->find($note_id);
            if($note) {
                $parent_dir = $note->getDir();
                if($parent_dir->getUserId() == $user->getId()) $em->remove($note);
            }
        }
        foreach($ids['dir'] as $dir_id) {
            $dir = $dir_rep->findOneBy( array('id' => $dir_id, 'user_id' => $user->getId()) );
            if($dir) $em->remove($dir);
        }
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

}
