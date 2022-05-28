<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="members")
 * @ApiResource(
 *   itemOperations={"get", "delete"},
 *   attributes={"route_prefix"="/v1.0"},
 *   normalizationContext={"groups" = {"read"}},
 *   denormalizationContext={"groups" = {"write"}}
 * )
 */
class Member {
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   * @Groups({"read"})
   */
  private ?int $id = null;

  /**
   * @ORM\Column(length=100, unique=true)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public string $full_name;

  /**
   * @ORM\Column(type="datetime")
   * @Groups({"read"})
   */
  public ?\DateTime $created_at = null;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Stock", mappedBy="members")
   */
  private Collection $books;

  /**
   * Entity constructor.
   */
  public function __construct() {
    $this->books = new ArrayCollection();
  }

  /**
   * @return int|null
   */
  public function getId(): ?int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getFullName(): string {
    return $this->full_name;
  }

  /**
   * @param string $full_name
   *
   * @return string
   */
  public function setFullName(string $full_name): string {
    $this->full_name = $full_name;
    return $this->full_name;
  }

  /**
   * Pre-persist gets triggered on Insert
   * @ORM\PrePersist
   */
  public function updatedTimestamps() {
    if ($this->created_at == null) {
      $this->created_at = new \DateTime('now');
    }
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->full_name;
  }

  /**
   * @return Collection
   */
  public function getBooks(): Collection {
    return $this->books;
  }
}