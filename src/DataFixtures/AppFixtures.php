<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Catalogue;
use App\Entity\Stock;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void {
    for ($i = 1; $i < 11; $i++) {
      $catalogue = new Catalogue();
      $catalogue->setBookName('Book name: volume ' . $i);
      $catalogue->setAvailable(2 + $i);

      $member = new Member();
      $member->setFullName('Member Number' . $i);

      $manager->persist($catalogue);
      $manager->persist($member);
    }

    $book = new Stock();
    $book->setMember($member);
    $book->setBookName('Book name: volume 1');
    $book->setMemberName('Member Number1');
    $book->setReturned(FALSE);
    $book->setBlocked(FALSE);
    $book->setAmountOwing(0);

    $manager->persist($book);
    $manager->flush();
  }
}
