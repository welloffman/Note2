<?php

namespace Acme\MainBundle\Helper;

/**
 * Для работы с слебными крошками
 */
class Breadcrumbs {
	private $doctrine;
	private $user;
	private $items;
	private $not_full;

	public function __construct($doctrine, $user) {
		$this->doctrine = $doctrine;
		$this->user = $user;
		$this->items = array();
		$this->not_full = false;
	}

	/**
	 * Получает массив крошек из базы
	 * @param  Dir $dir Раздел для которого получчаем крошки (максимум можно получить 20 крошек)
	 */
	public function fetch($dir) {
		if($dir) {
			$this->not_full = true;
			$item = $dir;
			for($i = 0; $i < 20; $i++) {
				$item = $this->getParentDir($item);
				if(!$item) {
					$this->not_full = false;
					break;
				}
				array_unshift($this->items, $item);
			}
		} else {
			$this->not_full = false;
		}
	}

	public function isNotFull() {
		return $not_full;
	}

	public function getData() {
		$data = array();
		foreach($this->items as $item) {
			$data[] = array('id' => $item->getId(), 'title' => $item->getTitle());
		}
		return $data;
	}

	private function getParentDir($dir) {
		$em = $this->doctrine->getManager();
		$dir_rep = $em->getRepository('AcmeModelBundle:Dir');
		return $dir_rep->findOneBy(array('id' => $dir->getPid(), 'user_id' => $this->user->getId()));
	}
}