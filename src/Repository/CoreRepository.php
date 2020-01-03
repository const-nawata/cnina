<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

class CoreRepository extends ServiceEntityRepository
{
	protected $logger;
	protected $paginator;
	protected $columns	= [];

	public function __construct( ManagerRegistry $registry, $entityClass, LoggerInterface $logger, PaginatorInterface $paginator )
	{
		$this->logger	= $logger;
		$this->paginator= $paginator;
		parent::__construct( $registry, $entityClass );
	}
//______________________________________________________________________________

	protected function getPagerQuery( $search='' )
	{
		$qb	= $this->createQueryBuilder('e');

		if( $search != '' ){
			$n	= 0;
			foreach( $this->columns as $column ){
				if( $column['searchable'] ){
					$qb->orWhere(
						$qb->expr()->like('e.'.$column['field'], ':p'.$n)
					)->setParameter('p'.$n,'%'.$search.'%');
					$n++;
				}
			}
		}

		return $qb->getQuery();
	}
//______________________________________________________________________________

	/**
	 * @param integer $page
	 * @param integer $limit
	 * @param string $search
	 * @return PaginationInterface
	 */
	public function getPaginator($page, $limit, $search = ''): PaginationInterface
	{
		$pagination = $this->paginator->paginate($this->getPagerQuery($search), $page, $limit);

		$pagination->setCustomParameters([
			'size'		=> 'small',
			'search'	=> $search,
			'columns'	=> $this->columns
		]);

		return $pagination;
	}
//______________________________________________________________________________

	/**
	 * @param integer $id
	 * @return array: Currency data
	 */
	public function getFormData($id = 0): array
	{
		$entity = ($id > 0)
			? $this->find($id)
			: new $this->_entityName();

		return [
			'entity' => $entity
		];
	}
//______________________________________________________________________________

}
