<?php

namespace App\Repository;

use App\Entity\Currency;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoreRepository extends ServiceEntityRepository
{
	protected $logger;
	protected $paginator;
	protected $viewFields;

	public function __construct( ManagerRegistry $registry, $entityClass, LoggerInterface $logger, PaginatorInterface $paginator )
	{
		$this->logger	= $logger;
		$this->paginator= $paginator;
		parent::__construct( $registry, $entityClass );
	}
//______________________________________________________________________________

	public function getPagerQuery( $fields=[], $search='' )
	{
		$qb	= $this->createQueryBuilder('record');

		if( $search != '' ){
			$n	= 0;
			foreach( $fields as $opts ){
				if( $opts['searchable'] ){
					$qb->orWhere(
						$qb->expr()->like('record.'.$opts['field'], ':p'.$n)
					)->setParameter('p'.$n,'%'.$search.'%');
					$n++;
				}
			}
		}

		return $qb->getQuery();
	}
//______________________________________________________________________________

	public function getPagerQueryMod( $search='' )
	{
		$qb	= $this->createQueryBuilder('record');

		if( $search != '' ){
			$n	= 0;
			foreach( $this->viewFields as $opts ){
				if( $opts['searchable'] ){
					$qb->orWhere(
						$qb->expr()->like('record.'.$opts['field'], ':p'.$n)
					)->setParameter('p'.$n,'%'.$search.'%');
					$n++;
				}
			}
		}

		return $qb->getQuery();
	}
//______________________________________________________________________________

}
