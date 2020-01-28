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
		$user->setFirstname('Root');
		$user->setSurname('Root-Admin');
		$user->setMailAddr('root@dummy-mail.tst');
		$user->setAddress('Root City, Dummy-Root street, 12');
		$user->setConfirmed(true);
		$user->setPhone('555-555-55');
		$user->setPostcode('RT-5566');
		$user->setCreatedAt(new \DateTime('2019-05-10 00:51:17'));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('admin1');
		$user->setRoles(['ROLE_ADMIN']);
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$user->setFirstname('Admin1');
		$user->setSurname('Admin1-Admin');
		$user->setMailAddr('admin1@dummy-mail.tst');
		$user->setAddress('Admin1 City, Dummy-Admin1 street, 42');
		$user->setConfirmed(true);
		$user->setPhone('555-555-53');
		$user->setPostcode('A1-1166');
		$user->setCreatedAt(new \DateTime('2019-12-18 17:27:41'));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('admin2');
		$user->setRoles(['ROLE_ADMIN']);
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$user->setFirstname('Admin2');
		$user->setSurname('Admin2-Admin');
		$user->setMailAddr('admin2@dummy-mail.tst');
		$user->setAddress('Admin2 City, Dummy-Admin2 street, 15');
		$user->setConfirmed(true);
		$user->setPhone('555-555-52');
		$user->setPostcode('RT-5566');
		$user->setCreatedAt(new \DateTime('2019-02-25 10:22:23'));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('user1');
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$user->setFirstname('User1');
		$user->setSurname('User1-User');
		$user->setMailAddr('user1@dummy-mail.tst');
		$user->setAddress('User1 City, Dummy-User1 street, 46');
		$user->setConfirmed(true);
		$user->setPhone('755-355-55');
		$user->setPostcode('U1-5649');
		$user->setCreatedAt(new \DateTime('2019-03-05 02:31:33'));
		$manager->persist($user);

		$user = new User();
		$user->setUsername('user2');
		$user->setPassword($this->passwordEncoder->encodePassword( $user, '111qqq' ));
		$user->setFirstname('User2');
		$user->setSurname('User2-User');
		$user->setMailAddr('user2@dummy-mail.tst');
		$user->setAddress('User2 City, Dummy-User2 street, 38');
		$user->setConfirmed(false);
		$user->setPhone('535-575-15');
		$user->setPostcode('U2-8438');
		$user->setCreatedAt(new \DateTime('2019-07-20 20:44:11'));
		$manager->persist($user);


		$manager->flush();
	}
}
