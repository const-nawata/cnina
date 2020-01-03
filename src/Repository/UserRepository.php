<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends CoreRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger, PaginatorInterface $paginator)
    {
		$this->columns	= [
			['field' => 'username',	'sortable' => true,	'searchable' => true, 'title' => 'form.username', 'css' => ''],
			['field' => 'firstname','sortable' => true,	'searchable' => true, 'title' => 'form.firstname', 'css' => ''],
			['field' => 'surname',	'sortable' => true,	'searchable' => true, 'title' => 'form.surname', 'css' => ''],
			['field' => 'address',	'sortable' => true,	'searchable' => true, 'title' => 'form.address', 'css' => ''],
			['field' => 'phone',	'sortable' => true,	'searchable' => true, 'title' => 'form.phone', 'css' => ''],
			['field' => 'postcode',	'sortable' => true,	'searchable' => true, 'title' => 'form.postcode', 'css' => ''],
			['field' => 'mailAddr',	'sortable' => true,	'searchable' => true, 'title' => 'form.mail', 'css' => '']
		];

		parent::__construct($registry, User::class, $logger, $paginator);
    }

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 * @param UserInterface $user
	 * @param string $newEncodedPassword
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

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


}
