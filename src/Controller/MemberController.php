<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Member controller.
 */
class MemberController extends AbstractController {

  /**
   * The member repository object.
   *
   * @var \App\Repository\MemberRepository
   */
  private MemberRepository $memberRepository;

  /**
   * @param \App\Repository\MemberRepository $memberRepository
   */
  public function __construct(MemberRepository $memberRepository) {
    $this->memberRepository = $memberRepository;
  }

  /**
   * Creates a member entry.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/members", name="members", methods={"POST"})
   */
  public function index(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $full_name = $data['full_name'] ?? NULL;
    if (is_null($full_name)) {
      return new JsonResponse([
        'message' => 'Missing full name!',
      ], Response::HTTP_FORBIDDEN);
    }
    $this->memberRepository->saveMember($full_name);
    return new JsonResponse([
      'message' => 'Member with name : "' . $full_name . '" successfully added.',
    ], Response::HTTP_CREATED);
  }

  /**
   * Fetch one member record.
   *
   * @param int|string $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/members/{id}", name="get_member", methods={"GET"})
   */
  public function get(int|string $id): JsonResponse {
    $member = $this->memberRepository->findOneBy(['id' => $id]);
    if($member instanceof Member) {
      $data = [
        'Member ID' => $member->getId(),
        'Full name' => $member->getFullName(),
        'Book borrowed' => $member->getBooks(),
      ];
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No member found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Fetches all the books.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/members", name="get_all_members", methods={"GET"})
   */
  public function getAll(): JsonResponse {
    $members = $this->memberRepository->findAll();
    if (!empty($books)) {
      $data = [];
      foreach ($members as $member) {
        $data[] = [
          'Member ID' => $member->getId(),
          'Full name' => $member->getFullName(),
          'Book borrowed' => $member->getBooks(),
        ];
      }
      return new JsonResponse($data, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No members found in the catalogue!',
    ], Response::HTTP_OK);
  }

  /**
   * Updates a member.
   *
   * @param $id
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/members/update/{id}", name="update_member", methods={"PATCH"})
   */
  public function update($id, Request $request): JsonResponse {
    $member = $this->memberRepository->findOneBy(['id' => $id]);
    if ($member instanceof Member) {
      $data = json_decode($request->getContent(), TRUE);
      $name = $data['full_name'] ?? NULL;
      if (is_null($name)) {
        return new JsonResponse([
          'message' => 'Missing full name!',
        ], Response::HTTP_FORBIDDEN);
      }
      $member->setFullName($name);
      $updated = $this->memberRepository->updateMember($member);
      return new JsonResponse($updated, Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No member found matching this ID!',
    ], Response::HTTP_OK);
  }

  /**
   * Removes a member from the system.
   *
   * @param int $id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @Route("/api/v1.0/members/delete/{id}", name="delete_member", methods={"DELETE"})
   */
  public function delete(int $id): JsonResponse {
    $member = $this->memberRepository->findOneBy(['id' => $id]);
    if ($member instanceof Member) {
      $this->memberRepository->removeMember($member);
      return new JsonResponse([
        'message' => 'Member removed: #' . $id,
      ], Response::HTTP_OK);
    }
    return new JsonResponse([
      'message' => 'No member found matching this ID!',
    ], Response::HTTP_OK);
  }
}
