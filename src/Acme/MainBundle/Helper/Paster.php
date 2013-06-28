<?php

namespace Acme\MainBundle\Helper;

use Acme\ModelBundle\Entity\Dir;
use Acme\ModelBundle\Entity\Note;
use Acme\ModelBundle\Entity\PositionDir;
use Acme\ModelBundle\Entity\PositionNote;
use Acme\MainBundle\Helper\NavList;

/**
 * Для Класс для клонирования и переноса разделов и записей
 */
class Paster {
    private $doctrine;
    private $user;
    private $action_type;
    private $target_dir;
    private $errors;

    /**
     * 
     * @param Doctrine $doctrine Объект для работы с базой данных
     * @param User $user Объект пользователя
     * @param String $action_type Тип вставки: copy или cut
     * @param Dir $target_dir раздел, в который будем вставлять
     * @param Array $errors Текстовые описания ошибок
     */
    public function __construct($doctrine, $user, $action_type, $target_dir) {
        $this->doctrine = $doctrine;
        $this->user = $user;
        $this->action_type = $action_type;
        $this->target_dir = $target_dir;
        $this->errors = array();
    }

    public function getErrors() {
        return $this->errors;
    }

    public function paste($item) {
        if($item["type"] == 'dir') $this->pasteDir((int)$item['id']);
        else if($item["type"] == 'note') $this->pasteNote((int)$item['id']);
    }

    private function pasteDir($id) {
        $em = $this->doctrine->getManager();
        $dir = $em->getRepository('AcmeModelBundle:Dir')->findOneBy(array('id' => $id, "user_id" => $this->user->getId()));

        if($dir && !$this->isSelfPaste($dir)) {
            $nav_list = new NavList($this->doctrine, $this->user);
            $pos = $nav_list->fetch($this->target_dir)->getLength() + 1;

            if($this->action_type == 'copy') {
                $clone = $this->copy($dir);
                $clone->getPosition()->setPos($pos);
                $clone->setPid($this->target_dir->getId());
                $em->persist($clone);
                $em->flush();
                $this->copyDirContent($dir, $clone);
            } else if($this->action_type == 'cut') {
                $dir->setPid( $this->target_dir->getId() );
                $dir->getPosition()->setPos($pos);
                $em->flush();
            }
        }
    }

    private function pasteNote($id) {
        $em = $this->doctrine->getManager();
        $note = $em->getRepository('AcmeModelBundle:Note')->find($id);

        if($note->getDir()->getUserId() == $this->user->getId()) {
            $nav_list = new NavList($this->doctrine, $this->user);
            $pos = $nav_list->fetch($this->target_dir)->getLength() + 1;

            if($this->action_type == 'copy') {
                $clone = $this->copy($note);
                $clone->getPosition()->setPos($pos);
                $clone->setDir($this->target_dir);
            } else if($this->action_type == 'cut') {
                $note->setDir($this->target_dir);
                $note->getPosition()->setPos($pos);
            }
            $em->flush();
        }
    }

    /**
     * Проверка на вставку в саму себя
     * @param Dir $dir копируемый раздел
     * @return boolean (Если уровень вложенности больше 100 - вернет true)
     */
    private function isSelfPaste($dir) {
        $tmp = $this->target_dir;
        $i = 0;
        do {
            if($tmp->getId() == $dir->getId() || $i > 100) {
                $this->errors[] = $i > 100 ? 'Слишком большая вложенность' : 'Нельзя копировать папку саму в себя';
                return true;
            }
            if($tmp->getPid()) $tmp = $this->doctrine->getManager()->getRepository('AcmeModelBundle:Dir')->find($tmp->getPid());
            $i++;
        } while ($tmp->getPid());

        return false;
    }

    /**
     * Копирует содержимое разделов (рекурсивно)
     * @param Dir $dir_source откуда
     * @param Dir $dir_dest куда
     */
    private function copyDirContent($dir_source, $dir_dest) {
        $notes = $this->doctrine->getManager()->getRepository('AcmeModelBundle:Note')->findJoinedPosition($dir_source->getId());
        foreach($notes as $note) {
            $clone = $this->copy($note);
            $clone->setDir($dir_dest);
        }
        $this->doctrine->getManager()->flush();

        $dirs = $this->doctrine->getManager()->getRepository('AcmeModelBundle:Dir')->findJoinedPosition($dir_source->getId(), $this->user->getId());
        foreach($dirs as $dir) {
            $clone = $this->copy($dir);
            $clone->setPid($dir_dest->getId());
            $this->doctrine->getManager()->flush();            
            $this->copyDirContent($dir, $clone);
        }
    }

    /**
     * Создание копии раздела или записи (без сохранения)
     * @param Dir|Note $item копируемый элемент
     * @return Dir|Note
     */
    private function copy($item) {
        if($item->getType() == 'dir') {
            $position = new PositionDir();
            $clone = new Dir();
            $clone->setUserId($item->getUserId());
            $clone->setPid($item->getPid());
            
        } else if($item->getType() == 'note') {
            $position = new PositionNote();
            $clone = new Note();
            $clone->setDir($item->getDir());
            $clone->setContent($item->getContent());
        }

        $position->setPos($item->getPosition()->getPos());
        $this->doctrine->getManager()->persist($position);

        $clone->setPosition($position);
        $clone->setTitle( $item->getTitle() );
        $this->doctrine->getManager()->persist($clone);

        return $clone;
    }
}
