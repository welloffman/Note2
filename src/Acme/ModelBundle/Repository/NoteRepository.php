<?php
/**
 * Заготовка для сложных запросов модели Note
 */
namespace Acme\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NoteRepository extends EntityRepository {

	/**
	 * Получение Записей и их позиций по id родительского раздела
	 * @param  int $dir_id id родительского каталога
	 * @return array | null        
	 */
    public function findJoinedPosition($dir_id)
	{
	    $query = $this->getEntityManager()->createQuery(' SELECT n, p, d FROM AcmeModelBundle:Note n JOIN n.position p JOIN n.dir d WHERE d.id=:dir_id')
	    ->setParameter('dir_id', $dir_id);
		
		try {
	        return $query->getResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}

}