<?php
namespace App\State;

use App\Entity\User;
use App\Entity\Groupe;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProvider extends BaseProvider
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        //
    }

    public function encodePassword(string $plainPassword): string
    {
        $user = new User();
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );

        return $user->getPassword();
    }

    public function getFullName(User $user): string
    {
		return sprintf(
				'%s %s',
				$user->getFirstName(),
				$user->getLastName(),
			);
    }
}
