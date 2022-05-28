<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="stock")
 * @ApiResource(
 *   itemOperations={"get", "delete"},
 *   attributes={"route_prefix"="/v1.0"},
 *   normalizationContext={"groups" = {"read"}},
 *   denormalizationContext={"groups" = {"write"}}
 * )
 */
class Stock {
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   * @Groups({"read"})
   */
  private ?int $id = null;

  /**
   * @ORM\Column(length=50)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public string $member_name;

  /**
   * @ORM\Column(length=255, unique=true)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public string $book_name;

  /**
   * @ORM\Column(type="boolean")
   * @Groups({"read", "write"})
   */
  public bool $returned = FALSE;

  /**
   * @ORM\Column(type="boolean")
   * @Groups({"read", "write"})
   */
  public bool $blocked = FALSE;

  /**
   * @ORM\Column(length=10)
   * @Assert\NotBlank()
   * @Groups({"read", "write"})
   */
  public int $amount_owing;

  /**
   * @ORM\Column(type="datetime")
   * @Groups({"read"})
   */
  public ?\DateTime $created_at = null;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="catalogues")
   */
  private ?Member $member;

  /**
   * @return int|null
   */
  public function getId(): ?int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getMemberName(): string {
    return $this->member_name;
  }

  /**
   * @param string $name
   *
   * @return string
   */
  public function setMemberName(string $name): string {
    $this->member_name = $name;
    return $this->member_name;
  }

  /**
   * @return string
   */
  public function getBookName(): string {
    return $this->book_name;
  }

  /**
   * @param string $name
   *
   * @return string
   */
  public function setBookName(string $name): string {
    $this->book_name = $name;
    return $this->book_name;
  }

  /**
   * @return bool
   */
  public function getReturned(): bool {
    return $this->returned;
  }

  /**
   * @param bool $returned
   *
   * @return bool
   */
  public function setReturned(bool $returned): bool {
    $this->returned = $returned;
    return $this->returned;
  }

  /**
   * @return bool
   */
  public function getBlocked(): bool {
    return $this->blocked;
  }

  /**
   * @param string $blocked
   *
   * @return bool
   */
  public function setBlocked(string $blocked): bool {
    $this->blocked = $blocked;
    return $this->blocked;
  }

  /**
   * @return int
   */
  public function getAmountOwing(): int {
    return $this->amount_owing;
  }

  /**
   * @param int $amount
   *
   * @return int
   */
  public function setAmountOwing(int $amount): int {
    $this->amount_owing = $amount;
    return $this->amount_owing;
  }

  /**
   * @return \App\Entity\Member|null
   */
  public function getMember(): ?Member {
    return $this->member;
  }

  /**
   * @param \App\Entity\Member|null $member
   *
   * @return $this
   */
  public function setMember(?Member $member): self {
    $this->member = $member;
    return $this;
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
    return $this->book_name;
  }
}
