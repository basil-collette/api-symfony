<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\GetCollection;
use App\State\UserProcessor;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[ApiResource(operations: [
    //POST _____________________________________________________________________ POST
    new Post(
        denormalizationContext: [
            'groups' => ['user:connect:read'],
        ]
    ),
    new Post(
        uriTemplate: 'user/create',
        processor: UserProcessor::class,
        denormalizationContext: [
            'groups' => ['user:register:write'],
        ]
    ),
    //GET _____________________________________________________________________ GET
    new GetCollection(
        uriTemplate: '/user/getall_nested',
        normalizationContext: [
            'groups' => ['user:get_collection:read', 'user:get_collection_with_groupe:read'],
        ]
    ),
    new GetCollection(
        uriTemplate: '/user/getall',
        normalizationContext: [
            'groups' => ['user:get_collection:read'],
        ],
    ),
    new Get(
        security: "is_granted('ROLE_ADMIN') or object == user",
        uriTemplate: '/user/get/{id}',
        normalizationContext: [
            'groups' => ['user:get:read'],
        ]
    ),
    //PUT _____________________________________________________________________ PUT
    new Put(
        security: "is_granted('ROLE_ADMIN') or object == user", 
        uriTemplate: '/user/modify/{id}',
        denormalizationContext: [
            'groups' => ['user:modify:write'],
        ],
    ),
    //DELETE ___________________________________________________________________ DELETE
    new Delete(
        security: "is_granted('ROLE_ADMIN')", 
        uriTemplate: '/user/delete/{id}',
        //controller:
    ),
    //new Patch(),
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:get_collection:read', 'user:modify:write', 'user:get:read', 'user:register:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:get:read', 'user:modify:write', 'user:register:write', 'user:get_collection:read'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:register:write', 'user:modify:write', 'user:get:read', 'groupe:get_collection_with_users:read'])]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:connect:read', 'user:modify:write', 'user:register:write'])]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:register:write', 'user:modify:write', 'user:get:read', 'user:get_collection_with_groupe:read'])]
    private ?Groupe $groupe = null;

    //#[ORM\Column(type: 'boolean')]
    //private $isVerified = false;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:connect:read', 'user:register:write', 'groupe:get_collection_with_users:read'])]
    private ?string $username = null;

    /* GETTER SETTER ________________________________________________ GETTER SETTER */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /*
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    */

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}
