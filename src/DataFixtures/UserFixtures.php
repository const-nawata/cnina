<?php
namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}



	public function load(ObjectManager $manager)
	{
		$user = new User();
		$user->setUsername('root');
		$user->setRoles(['ROLE_ROOT']);
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('admin1');
		$user->setRoles(['ROLE_ADMIN']);
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('admin2');
		$user->setRoles(['ROLE_ADMIN']);
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('user1');
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('user2');
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$manager->persist($user);


		$manager->flush();
	}
}
