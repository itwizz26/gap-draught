<?php

namespace App\Controller;

use App\Entity\Catalogue;
use App\Repository\CatalogueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Catalogue controller.
 */
class CatalogueController extends AbstractController {

  /**
   * The catalogue repository object.
   *
   * @var \App\Repository\CatalogueRepository
   */
  private CatalogueRepository $catalogueRepository;

  /**
   * @param \App\Repository\CatalogueRepository $catalogueRepository
   */
  public function __construct(CatalogueRepository $catalogueRepository) {
    $this->catalogueRepository = $catalogueRepository;
  }

  /**
   * Creates a catalogue entry.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/catalogues", name="catalogues", methods={"POST"})
   */
  public function index(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $name = $data['book_name'] ?? NULL;
    $available = $data['available'] ?? NULL;
    if (empty($name) || (empty($available) && !is_int($available))) {
      return new JsonResponse([
        'message' => 'Missing book name or available count!',
      ], Response::HTTP_OK);
    }
    $this->catalogueRepository->saveCatalogue($name, $available);
    return new JsonResponse([
        'message' => 'Book with name : "' . $name . '" successfully added.',
      ], Response::HTTP_CREATED
    );
  }

  /**
   * Fetch one book.
   *
   * @param int|string $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/catalogues/{id}", name="get_book", methods={"GET"})
   */
  public function get(int|string $id): JsonResponse {
    $book = $this->catalogueRepository->findOneBy(['id' => $id]);
    if($book instanceof Catalogue) {
      $data = [
        'Book ID' => $book->getId(),
        'Book name' => $book->getBookName(),
        'Number available' => $book->getAvailable(),
      ];
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No book found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Fetches all the books.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/catalogues", name="get_all_books", methods={"GET"})
   */
  public function getAll(): JsonResponse {
    $books = $this->catalogueRepository->findAll();
    if (!empty($books)) {
      $data = [];
      foreach ($books as $book) {
        $data[] = [
          'Book ID' => $book->getId(),
          'Book name' => $book->getBookName(),
          'Number available' => $book->getAvailable(),
        ];
      }
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No books found in the catalogue!',
    ], Response::HTTP_OK);
  }

  /**
   * Updates a book.
   *
   * @param $id
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/catalogues/update/{id}", name="update_book", methods={"PATCH"})
   */
  public function update($id, Request $request): JsonResponse {
    $book = $this->catalogueRepository->findOneBy(['id' => $id]);
    if ($book instanceof Catalogue) {
      $data = json_decode($request->getContent(), TRUE);
      $available = $data['available'] ?? NULL;
      if ((is_null($available) && !is_int($available))) {
        return new JsonResponse([
          'message' => 'Missing available count!',
        ], Response::HTTP_FORBIDDEN);
      }
      $book->setAvailable($available);
      $updated = $this->catalogueRepository->updateAvailability($book);
      return new JsonResponse($updated, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No book found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Deletes a book.
   *
   * @param int $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/catalogues/delete/{id}", name="delete_book", methods={"DELETE"})
   */
  public function delete(int $id): JsonResponse {
    $book = $this->catalogueRepository->findOneBy(['id' => $id]);
    if ($book instanceof Catalogue) {
      $this->catalogueRepository->removeBook($book);
      return new JsonResponse([
        'message' => 'Book deleted: #' . $id,
      ], Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No book found matching this ID!',
    ], Response::HTTP_OK);
  }
}
