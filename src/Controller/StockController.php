<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Repository\StockRepository;
use App\Repository\MemberRepository;
use App\Repository\CatalogueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Stock controller.
 */
class StockController extends AbstractController {

  /**
   * The stock repository object.
   *
   * @var \App\Repository\StockRepository
   */
  private StockRepository $stockRepository;

  /**
   * The member entity repository.
   *
   * @var \App\Repository\MemberRepository
   */
  private MemberRepository $memberRepository;

  /**
   * The catalogue repository object.
   *
   * @var \App\Repository\CatalogueRepository
   */
  private CatalogueRepository $catalogueRepository;

  /**
   * Stock object constructor.
   *
   * @param \App\Repository\StockRepository $stockRepository
   * @param \App\Repository\MemberRepository $memberRepository
   * @param \App\Repository\CatalogueRepository $catalogueRepository
   */
  public function __construct(StockRepository $stockRepository, MemberRepository $memberRepository, CatalogueRepository $catalogueRepository) {
    $this->stockRepository = $stockRepository;
    $this->memberRepository = $memberRepository;
    $this->catalogueRepository = $catalogueRepository;
  }

  /**
   * Creates a stock entry.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/stocks", name="stocks", methods={"POST"})
   */
  public function index(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $member_name = $data['member_name'] ?? NULL;
    $stock_name = $data['book_name'] ?? NULL;
    $returned = $data['returned'] ?? FALSE;
    $blocked = $data['blocked'] ?? FALSE;
    $amount = $data['amount_owing'] ?? NULL;
    $member = $this->memberRepository->findOneBy(['full_name' => $member_name]);
    $catalogue = $this->catalogueRepository->findOneBy(['book_name' => $stock_name]);
    $this->blockMember($member_name);
    if ($catalogue->getAvailable() === 0) {
      return new JsonResponse([
        'message' => 'There are no books with this title available to loan!',
      ], Response::HTTP_FORBIDDEN);
    }
    if (empty($member)) {
      return new JsonResponse([
        'message' => 'This user is not a member of the library!',
      ], Response::HTTP_FORBIDDEN);
    }
    if (empty($catalogue)) {
      return new JsonResponse([
        'message' => 'We do not have that book in the library.',
      ], Response::HTTP_FORBIDDEN);
    }
    if ($this->memberCanBorrow($member_name) === FALSE) {
      return new JsonResponse([
        'message' => 'Member has reached the maximum amount allowable to borrow a book!',
      ], Response::HTTP_FORBIDDEN);
    }
    $books = $this->stockRepository->findBy(['member_name' => $member_name]);
    foreach ($books as $book) {
      if ($book->getBlocked()) {
        return new JsonResponse([
          'message' => 'Member is blocked from borrowing a book!',
        ], Response::HTTP_FORBIDDEN);
      }
    }
    if (is_null($returned) || is_null($blocked) || is_null($amount)) {
      return new JsonResponse([
        'message' => 'You are missing some stock details!',
      ], Response::HTTP_FORBIDDEN);
    }
    $this->stockRepository->saveStock($member, $catalogue, $returned, $blocked, $amount);
    return new JsonResponse([
      'message' => 'Book with name "' . $catalogue->getBookName() . '" successfully loaned.',
    ], Response::HTTP_CREATED);
  }

  /**
   * Fetch one stock.
   *
   * @param int|string $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/stocks/{id}", name="get_stock", methods={"GET"})
   */
  public function get(int|string $id): JsonResponse {
    $stock = $this->stockRepository->findOneBy(['id' => $id]);
    $yes_no = [
      1 => 'Yes',
      0 => 'No',
    ];
    if($stock instanceof Stock) {
      $data = [
        'Stock ID' => $stock->getId(),
        'Member name' => $stock->getMemberName(),
        'Book loaned' => $stock->getBookName(),
        'Book returned' => $yes_no[$stock->getReturned()],
        'Member blocked' => $yes_no[$stock->getBlocked()],
        'Amount Owing' => 'R' . $stock->getAmountOwing(),
      ];
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No stock found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Fetches all the books.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/stocks", name="get_all_stock", methods={"GET"})
   */
  public function getAll(): JsonResponse {
    $stocks = $this->stockRepository->findAll();
    if (!empty($stocks)) {
      $yes_no = [
        1 => 'Yes',
        0 => 'No',
      ];
      $data = [];
      foreach ($stocks as $stock) {
        $data[] = [
          'Stock ID' => $stock->getId(),
          'Member name' => $stock->getMemberName(),
          'Book loaned' => $stock->getBookName(),
          'Book returned' => $yes_no[$stock->getReturned()],
          'Member blocked' => $yes_no[$stock->getBlocked()],
          'Amount Owing' => 'R' . $stock->getAmountOwing(),
        ];
      }
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No stock found in the stock!',
    ], Response::HTTP_OK);
  }

  /**
   * Updates a book entry.
   *
   * @param $id
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/stocks/update/{id}", name="update_stock", methods={"PATCH"})
   */
  public function update($id, Request $request): JsonResponse {
    $stock = $this->stockRepository->findOneBy(['id' => $id]);
    if ($stock instanceof Stock) {
      $data = json_decode($request->getContent(), TRUE);
      $returned = $data['returned'] ?? NULL;
      $blocked = $data['blocked'] ?? NULL;
      $amount = $data['amount_owing'] ?? NULL;
      if (is_null($returned) || is_null($blocked) || empty($amount)) {
        return new JsonResponse([
          'message' => 'Missing stock details!',
        ], Response::HTTP_OK);
      }
      $stock->setReturned($returned);
      $stock->setBlocked($blocked);
      $stock->setAmountOwing($amount);
      $updated = $this->stockRepository->updateStock($stock);
      return new JsonResponse($updated, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No stock found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Deletes a book.
   *
   * @param int $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/stocks/delete/{id}", name="delete_stock", methods={"DELETE"})
   */
  public function delete(int $id): JsonResponse {
    $stock = $this->stockRepository->findOneBy(['id' => $id]);
    if ($stock instanceof Stock) {
      $this->stockRepository->removeBook($stock);
      return new JsonResponse([
        'message' => 'Stock deleted: #' . $id,
      ], Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No stock found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * @param string $name
   *
   * @return bool
   */
  public function memberCanBorrow(string $name): bool {
    $books = $this->stockRepository->findBy(['member_name' => $name]);
    if (empty($books) || count($books) < 5) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @param string $name
   */
  public function blockMember(string $name) {
    $books = $this->stockRepository->findBy(['member_name' => $name]);
    foreach ($books as $book) {
      $date = date_diff(new \DateTime(), $book->created_at);
      if ($date->days >= 7) {
        $book->setBlocked(TRUE);
        $days_overdue = $date->days - 7;
        $overdue_amount = $book->getAmountOwing();
        if ($days_overdue > 0 && $days_overdue <= 7) {
          $overdue_amount = $book->getAmountOwing() + (2 * $days_overdue);
        }
        elseif ($days_overdue > 7) {
          $overdue_amount = $book->getAmountOwing() + 50;
        }
        $book->setAmountOwing($overdue_amount);
        $this->stockRepository->updateStock($book);
      }
    }
  }
}
