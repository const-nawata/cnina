<?php

namespace App\Repository;

use App\Entity\Currency;

use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends CoreRepository
{

//	private $viewFields	= [
//		['field' => 'name',		'title' => 'form.denomination', 	'sortable' => true,		'searchable' => true,	'css' => '' ],
//		['field' => 'ratio',	'title' => 'form.ratio-currency',	'sortable' => true,		'searchable' => true,	'css' => 'number-list-sell' ],
//		['field' => 'symbol',	'title' => 'form.sign',				'sortable' => false,	'searchable' => false,	'css' => '' ],
////		['field' => 'sample',	'title' => 'title.sample',			'sortable' => false,	'searchable' => false,	'css' => 'number-list-sell' ]
//	];

    public function __construct( ManagerRegistry $registry, LoggerInterface $logger, PaginatorInterface $paginator )
    {
    	$this->viewFields	= [
			['field' => 'name',		'title' => 'form.denomination', 	'sortable' => true,		'searchable' => true,	'css' => '' ],
			['field' => 'ratio',	'title' => 'form.ratio-currency',	'sortable' => true,		'searchable' => true,	'css' => 'number-list-sell' ],
			['field' => 'symbol',	'title' => 'form.sign',				'sortable' => false,	'searchable' => false,	'css' => '' ],
//		['field' => 'sample',	'title' => 'title.sample',			'sortable' => false,	'searchable' => false,	'css' => 'number-list-sell' ]
		];
        parent::__construct( $registry, Currency::class, $logger, $paginator );
    }
//______________________________________________________________________________


	/**
	 * @param integer $page
	 * @param integer $limit
	 * @param string $search
	 * @return PaginationInterface
	 */
	public function getPaginator( $page, $limit, $search='' ){
		$query = $this->getPagerQueryMod( $search );

		$pagination = $this->paginator->paginate( $query, $page, $limit );

		$fields	= $this->viewFields;
		unset($fields[2]);

		$items	= $pagination->getItems();

		foreach ( $items as &$item ){
			$val	= rand(11, 9999);
			$rval	= rand(0, 99);
			$rval	= $rval > 9 ? $rval : '0'.$rval;
			$val	= $val.'.'.$rval;
			$item->sample	= $item->getIsAfterPos() ? $val.$item->getSymbol() : $item->getSymbol().$val;
		}

		$pagination->setItems($items);

		return $pagination;
	}

	/**
	 * @param integer $id
	 * @return array: Currency data
	 */
	public function getFormData( $id=0 ): array
	{
		if( $id > 0){
			$entity = $this->find($id);
		}else{
			$entity = new Currency();
			$entity->setIsAfterPos(true);
		}

		return [
			'entity'	=> $entity
		];
	}
//______________________________________________________________________________

	/**
	 * @param array $post
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function saveFormData( array $post ): void
	{
		$entity	= ( $post['id'] > 0 )
			? $this->find( $post['id'] )
			: new Currency();

		$post['ratio']	= str_replace(',', '.', $post['ratio']);

		$entity->setName($post['name']);
		$entity->setSymbol($post['symbol']);
		$entity->setRatio($post['ratio']);
		$entity->setIsAfterPos((bool)$post['isAfterPos']);

		$this->_em->persist( $entity );
		$this->_em->flush();
	}
//______________________________________________________________________________

	public function getDefault( $ratio = 1): array
	{
		$qb = $this->createQueryBuilder('c')
			->andWhere('c.ratio = :ratio')
			->setParameter('ratio', $ratio )
			->getQuery();

		$list	= $qb->execute();
		if( count($list) > 0 ){
			$currency	= $list[0];
			return [
				'id'	=> $currency->getId(),
				'name'	=> $currency->getName()
			];
		}

		$list	= $this->findAll();
		if( count($list) > 0 ){
			$currency	= $list[0];
			return [
				'id'	=> $currency->getId(),
				'name'	=> $currency->getName()
			];
		}

		return [
			'id'	=> 0,
			'name'	=> 'Undefined'
		];
	}
//______________________________________________________________________________

}
