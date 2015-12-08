<?php

namespace Zethzer\NewsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommentaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentaireRepository extends EntityRepository
{
	public function getByNews($newsId)
	{
		$qb = $this->createQueryBuilder('c')
				   ->leftJoin('c.user', 'u')
				   		->addSelect('u')
				   ->orderBy('c.date', 'DESC')
				   ->where('c.article = :id')
				   		->setParameter('id', $newsId);
		
		return $qb->getQuery()->getResult();
	}

	public function getAllComments()
	{
		$qb = $this->createQueryBuilder('c');
		
		return $qb->getQuery()->getResult();
	}
}
