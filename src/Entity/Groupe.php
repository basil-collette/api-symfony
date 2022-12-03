<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
#[ApiResource(operations: [
    //GET ___________________________________________________________________ GET
    new GetCollection(
        uriTemplate: '/group/getAll',
        normalizationContext: [
            'groups' => ['groupe:get_collection:read'],
        ]
    ),
    new GetCollection(
        //name: 'user_getall_nested',
        uriTemplate: '/groupe/getall_nested',
        normalizationContext: [
            'groups' => ['groupe:get_collection:read', 'groupe:get_collection_with_users:read'],
        ]
    ),
    new Get(
        normalizationContext: [
            'groups' => ['groupe:get:read'],
        ],
    ),
    //POST _____________________________________________________________________ POST
    new Post(
        security: "is_granted('ROLE_ADMIN')",
        uriTemplate: 'groupe/create',
        processor: GroupeProcessor::class,
        denormalizationContext: [
            'groups' => ['groupe:create:write'],
        ]
    ),
    //PUT _____________________________________________________________________ PUT
    new Put(
        security: "is_granted('ROLE_ADMIN')", 
        uriTemplate: '/groupe/modify/{id}',
        denormalizationContext: [
            'groups' => ['groupe:modify:write'],
        ],
    ),
    //DELETE ___________________________________________________________________ DELETE
    new Delete(
        security: "is_granted('ROLE_ADMIN')", 
        uriTemplate: '/groupe/delete/{id}'
        //controller
    ),
])]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupe:get:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupe:get_collection:read', 'groupe:get:read', 'groupe:get_collection_with_users:read', 'groupe:create:write', 'groupe:modify:write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: User::class)]
    #[Groups(['groupe:get_collection_with_users:read', 'groupe:create:write', 'groupe:modify:write'])]
    private Collection $users;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /* GETTER SETTER ________________________________________________ GETTER SETTER */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroupe($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupe() === $this) {
                $user->setGroupe(null);
            }
        }

        return $this;
    }
}
