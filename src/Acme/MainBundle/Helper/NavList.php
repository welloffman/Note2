<?php

namespace Acme\MainBundle\Helper;

/**
 * Для работы с единым списком разделов и записей принадлежащих одному родительскому разделу
 */
class NavList {
    private $doctrine;
    private $user;
    private $items;

    /**
     * 
     * @param Doctrine $doctrine Объект для работы с базой данных
     * @param User $user Объект пользователя
     */
    public function __construct($doctrine, $user) {
        $this->doctrine = $doctrine;
        $this->user = $user;
        $this->items = array();
    }

    /**
     * Получает список из базы
     * @param  Dir $dir Родительский раздел
     * @return NavList 
     */
    public function fetch($dir) {
        if($dir) {
            $em = $this->doctrine->getManager();
            $dir_rep = $em->getRepository('AcmeModelBundle:Dir');

            //$children_dirs = $dir_rep->findBy( array('pid' => $dir->getId(), 'user_id' => $user->getId()) );
            $children_dirs = $dir_rep->findJoinedPosition($dir->getId(), $this->user->getId());

            foreach($children_dirs as $d) {
                $item = $d->toArray();
                $item['type'] = 'dir';
                $this->items[] = $item;
            }

            $notes_collection = $em->getRepository('AcmeModelBundle:Note')->findJoinedPosition($dir->getId());
            foreach($notes_collection as $n) {
                $item = $n->toArray();
                $item['position'] = $n->getPosition()->toArray();
                $item['type'] = 'note';
                $this->items[] = $item;
            }
        }

        return $this;
    }

    /**
     * Сортировка элементов списка по позиции
     * @return NavList 
     */
    public function sortByPosition() {
        usort($this->items, function($a, $b) {
            if($a['position']['pos'] > $b['position']['pos']) return 1;
            else if($a['position']['pos'] < $b['position']['pos']) return -1;
            else return 0;
        });

        return $this;
    }

    /**
     * Возвращает массив элементов списка
     * @return array
     */
    public function getItems() {
        return $this->items;
    }

    public function getLength() {
        return count($this->items);
    }
}
