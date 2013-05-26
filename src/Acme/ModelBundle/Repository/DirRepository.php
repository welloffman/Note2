<?php
/**
 * Заготовка для сложных запросов модели Dir
 */
namespace Acme\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DirRepository extends EntityRepository {

	/**
	 * Получение разделов и их позиций по id родительского раздела и id пользователя
	 * @param  int $pid id родительского запроса
	 * @param  int $user_id id пользователя
	 * @return array | null        
	 */
    public function findJoinedPosition($pid, $user_id)
	{
	    $query = $this->getEntityManager()->createQuery(' SELECT d, p FROM AcmeModelBundle:Dir d JOIN d.position p WHERE d.user_id=:user_id and d.pid=:pid')
	    ->setParameter('user_id', $user_id)->setParameter('pid', $pid);
		
		try {
	        return $query->getResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}

}