<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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

	public function __construct(ManagerRegistry $registry, LoggerInterface $logger, PaginatorInterface $paginator)
	{
		$this->columns	= [
			['field' => 'name',		'sortable' => true,	'searchable' => true, 'title' => 'form.denomination', 'css' => ''],
			['field' => 'ratio',	'sortable' => true,	'searchable' => true, 'title' => 'form.ratio-currency', 'css' => 'number-list-sell'],
			['field' => 'sample',	'sortable' => false,'searchable' => false, 'title' => 'title.sample', 'css' => 'number-list-sell']
		];

		parent::__construct($registry, Currency::class, $logger, $paginator);
	}
//______________________________________________________________________________

	/**
	 * @param integer $id
	 * @return array: Currency data
	 */
	public function getFormData($id = 0): array
	{
		if ($id > 0) {
			$entity = $this->find($id);
		} else {
			$entity = new Currency();
			$entity->setIsAfterPos(true);
		}

		return [
			'entity' => $entity
		];
	}
//______________________________________________________________________________

	/**
	 * @param array $post
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function saveFormData(array $post): void
	{
		$entity = ($post['id'] > 0)
			? $this->find($post['id'])
			: new Currency();

		$post['ratio'] = str_replace(',', '.', $post['ratio']);

		$entity->setName($post['name']);
		$entity->setSymbol($post['symbol']);
		$entity->setRatio($post['ratio']);
		$entity->setIsAfterPos((bool)$post['isAfterPos']);

		$this->_em->persist($entity);
		$this->_em->flush();
	}

//______________________________________________________________________________

	public function getDefault($ratio = 1): array
	{
		$qb = $this->createQueryBuilder('c')
			->andWhere('c.ratio = :ratio')
			->setParameter('ratio', $ratio)
			->getQuery();

		$list = $qb->execute();
		if (count($list) > 0) {
			$currency = $list[0];
			return [
				'id' => $currency->getId(),
				'name' => $currency->getName()
			];
		}

		$list = $this->findAll();
		if (count($list) > 0) {
			$currency = $list[0];
			return [
				'id' => $currency->getId(),
				'name' => $currency->getName()
			];
		}

		return [
			'id' => 0,
			'name' => 'Undefined'
		];
	}
//______________________________________________________________________________

}
