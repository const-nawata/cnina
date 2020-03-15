<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
	 * @param array $post
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function saveFormData(array $post): void
	{
		$entity = ($post['id'] > 0)
			? $this->find($post['id'])
			: new User();

		$entity->setUsername($post['username']);
		$entity->setPassword($post['plainPassword']);
		$entity->setFirstname($post['firstname']);
		$entity->setSurname($post['surname']);
		$entity->setPostcode($post['postcode']);
		$entity->setAddress($post['address']);
		$entity->setPhone($post['phone']);
		$entity->setMailAddr($post['mailAddr']);

		$this->_em->persist($entity);
		$this->_em->flush();
	}

//______________________________________________________________________________

}
