<?php

namespace App\Repository;

use App\Entity\Stock;
use App\Entity\Member;
use App\Entity\Catalogue;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Stock repository object.
 */
class StockRepository extends ServiceEntityRepository {

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
    parent::__construct($registry, Stock::class);
    $this->manager = $manager;
  }

  /**
   * Saves a book entry.
   *
   * @param Member $member
   * @param Catalogue $catalogue
   * @param bool $returned
   * @param bool $blocked
   * @param int $amount
   */
  public function saveStock(Member $member, Catalogue $catalogue, bool $returned, bool $blocked, int $amount): void {
    $stock = new Stock();
    $stock->setMember($member);
    $stock->setMemberName($member->full_name);
    $stock->setBookName($catalogue->getBookName());
    $stock->setReturned($returned);
    $stock->setBlocked($blocked);
    $stock->setAmountOwing($amount);
    $available = $catalogue->getAvailable() - 1;
    $catalogue->setAvailable($available);
    $this->manager->persist($stock);
    $this->manager->persist($catalogue);
    $this->manager->flush();
  }

  /**
   * Updates book availability.
   *
   * @param \App\Entity\Stock $book
   *
   * @return \App\Entity\Stock
   */
  public function updateStock(Stock $book): Stock {
    $this->manager->persist($book);
    $this->manager->flush();
    return $book;
  }

  /**
   * Removes a book from stock.
   *
   * @param \App\Entity\Stock $stock
   */
  public function removeBook(Stock $stock) {
    $this->manager->remove($stock);
    $this->manager->flush();
  }
}
