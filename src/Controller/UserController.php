<?php

namespace App\Controller;

use App\Document\Score;
use App\Repository\ScoreRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends ApiBaseController
{
    /**
     * Get users score.
     *
     * @Route(path="/scores", name="get users score", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Scores returned successfully",
     *     @SWG\Schema(
     *         title="data",
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Score::class, groups={"List"}))
     *     )
     * ),
     * @SWG\Response(
     *     response=204,
     *     description="No content, score data empty",
     * ),
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Page number",
     *     default=1
     * ),
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Scores limit per page",
     *     default=ApiBaseController::PAGINATION_PAGE_LIMIT
     * ),
     * @SWG\Parameter(
     *     name="sort_field",
     *     in="query",
     *     type="string",
     *     description="Available sort fields: score, finishedAt",
     *     default="score"
     * ),
     * @SWG\Parameter(
     *     name="sort_order",
     *     in="query",
     *     type="string",
     *     description="e.g.: ASC|DESC",
     *     default="DESC"
     * ),
     * @SWG\Tag(name="User")
     */
    public function score(DocumentManager $documentManager, Request $request): JsonResponse
    {
        $sortBy = $request->query->get('sort_field', ScoreRepository::SORT_FIELD_SCORE);
        $sortingDirection = $request->query->get('sort_order', ScoreRepository::SORT_DESC);

        $scoreRepository = $documentManager->getRepository(Score::class);
        assert($scoreRepository instanceof ScoreRepository);

        return $this->paginatedResponse(
            $scoreRepository->getScores($sortBy, $sortingDirection),
            ['List']
        );
    }
}
