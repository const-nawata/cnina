<?php
namespace App\DataFixtures;

use App\Entity\Currency;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CurrencyFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}



	public function load(ObjectManager $manager)
	{
		$user = new Currency();
		$user->setName('Dollar');
		$user->setSymbol('$');
		$user->setRatio(1.0);
		$user->setIsAfterPos(false);
		$manager->persist($user);

		$user = new Currency();
		$user->setName('Euro');
		$user->setSymbol('€');
		$user->setRatio(0.858584);
		$user->setIsAfterPos(false);
		$manager->persist($user);

		$user = new Currency();
		$user->setName('Гривня');
		$user->setSymbol('₴');
		$user->setRatio(24.0);
		$user->setIsAfterPos(true);

		$user = new Currency();
		$user->setName('Рубль');
		$user->setSymbol('₽');
		$user->setRatio(64.0);
		$user->setIsAfterPos(true);
		$manager->persist($user);



		for ($i = 0; $i<100; $i++ ){
			$user = new Currency();
			$user->setName('Currency-'.$i);
			$user->setSymbol('c'.$i);
			$user->setRatio(rand(1000,99999)/1000);
			$user->setIsAfterPos(false);
			$manager->persist($user);
		}



		$manager->flush();
	}
}
