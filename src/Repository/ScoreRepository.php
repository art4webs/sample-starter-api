<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ScoreRepository extends DocumentRepository
{
    public const SORT_DESC = 'desc';
    public const SORT_ASC = 'asc';

    private const SORT_DIRECTIONS_FIELDS = [
        self::SORT_DESC,
        self::SORT_ASC,
    ];

    public const SORT_FIELD_SCORE = 'score';
    public const SORT_FIELD_FINISHED = 'finishedAt';

    private const SORT_TARGET_FIELDS = [
        self::SORT_FIELD_SCORE,
        self::SORT_FIELD_FINISHED,
    ];

    public function getScores(string $sortBy = self::SORT_FIELD_SCORE, string $sortingDirection = self::SORT_DESC): array
    {
        $this->validateParams($sortBy, $sortingDirection);

        return $this->findBy([], [$sortBy => $sortingDirection]);
    }

    private function validateParams(string $sortBy, string $sortingDirection): bool
    {
        if (!in_array($sortBy, self::SORT_TARGET_FIELDS)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid sort target field');
        }

        if (!in_array($sortingDirection, self::SORT_DIRECTIONS_FIELDS)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid sort directional field');
        }

        return true;
    }
}
