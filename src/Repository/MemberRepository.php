<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Member repository object.
 */
class MemberRepository extends ServiceEntityRepository {

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  private EntityManagerInterface $manager;

  /**
   * Class constructor.
   *
   * @param \Doctrine\Persistence\ManagerRegistry $registry
   * @param \Doctrine\ORM\EntityManagerInterface $manager
   */
  public function __construct (ManagerRegistry $registry, EntityManagerInterface $manager) {
    parent::__construct($registry, Member::class);
    $this->manager = $manager;
  }

  /**
   * Saves a member entry.
   *
   * @param string $name
   */
  public function saveMember(string $name) {
    $member = new Member();
    $member->setFullName($name);
    $this->manager->persist($member);
    $this->manager->flush();
  }

  /**
   * Updates member full name.
   *
   * @param \App\Entity\Member $member
   *
   * @return \App\Entity\Member
   */
  public function updateMember(Member $member): Member {
    $this->manager->persist($member);
    $this->manager->flush();
    return $member;
  }

  /**
   * Removes a member from the system.
   *
   * @param \App\Entity\Member $member
   */
  public function removeMember(Member $member) {
    $this->manager->remove($member);
    $this->manager->flush();
  }
}
