<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="catalogue")
 * @ApiResource(
 *   itemOperations={"get", "delete"},
 *   attributes={"route_prefix"="/v1.0"},
 *   normalizationContext={"groups" = {"read"}},
 *   denormalizationContext={"groups" = {"write"}}
 * )
 */
class Catalogue {
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   * @Groups({"read"})
   */
  private ?int $id = null;

  /**
   * @ORM\Column(length=255, unique=true, nullable=false)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public string $book_name;

  /**
   * @ORM\Column(length=10, nullable=false)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public int $available;

  /**
   * @ORM\Column(type="datetime")
   * @Groups({"read"})
   */
  public ?\DateTime $created_at = null;

  /**
   * @return int|null
   */
  public function getId(): ?int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getBookName(): string {
    return $this->book_name;
  }

  /**
   * @param string $book_name
   *
   * @return string
   */
  public function setBookName(string $book_name): string {
    $this->book_name = $book_name;
    return $this->book_name;
  }

  /**
   * @return int
   */
  public function getAvailable(): int {
    return $this->available;
  }

  /**
   * @param int $available
   *
   * @return int
   */
  public function setAvailable(int $available): int {
    $this->available = $available;
    return $this->available;
  }

  /**
   * Pre-persist gets triggered on insert.
   *
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
    return $this->book_name;
  }
}
