<?php
namespace App\State;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private UserPasswordhasherInterface $passwordHasher
	) {
		//
	}

	public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
	{
		if (false === $data instanceof User) {
			return;
		}

		if ($operation->getName() === 'post') {
			$data->setCreatedAt(new \DateTimeImmutable());
		}
		$data->setUpdatedAt(new \DateTimeImmutable());

		$data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));
		
		$this->entityManager->persist($data);
		$this->entityManager->flush();
	}
}