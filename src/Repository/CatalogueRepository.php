<?php

namespace App\Repository;

use App\Entity\Catalogue;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Catalogue repository object.
 */
class CatalogueRepository extends ServiceEntityRepository {

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
    parent::__construct($registry, Catalogue::class);
    $this->manager = $manager;
  }

  /**
   * Saves a book entry.
   *
   * @param string $name
   * @param int $available
   */
  public function saveCatalogue(string $name, int $available) {
    $catalogue = new Catalogue();
    $catalogue->setBookName($name);
    $catalogue->setAvailable($available);
    $this->manager->persist($catalogue);
    $this->manager->flush();
  }

  /**
   * Updates book availability.
   *
   * @param \App\Entity\Catalogue $book
   *
   * @return \App\Entity\Catalogue
   */
  public function updateAvailability(Catalogue $book): Catalogue {
    $this->manager->persist($book);
    $this->manager->flush();
    return $book;
  }

  /**
   * Removes a book from catalogue.
   *
   * @param \App\Entity\Catalogue $catalogue
   */
  public function removeBook(Catalogue $catalogue) {
    $this->manager->remove($catalogue);
    $this->manager->flush();
  }
}
