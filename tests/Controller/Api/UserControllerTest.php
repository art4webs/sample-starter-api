<?php

namespace App\Tests\Controller\Api;

use App\Controller\ApiBaseController;
use App\Tests\Cases\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    public function testGetUserScoreFirstPageSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/collection');
        $response = $this->makeRequest('GET', '/api/user/scores');

        $response = json_decode($response->getContent(), true);
        $this->assertEquals(ApiBaseController::PAGINATION_PAGE_LIMIT, count($response['data']));
    }

    public function testGetUserScoreSecondPageSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/collection');
        $response = $this->makeRequest('GET', '/api/user/scores?page=2');

        $response = json_decode($response->getContent(), true);
        $this->assertEquals(10, count($response['data']));
    }

    public function testGetUserScoreThirdEmptyPageSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/collection');
        $response = $this->makeRequest('GET', '/api/user/scores?page=3');

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @dataProvider getSuccessDataTests
     * @param string $requestPath
     * @param string $expectedResponse
     * @param int $responseCode
     */
    public function testGetScoresSuccess(string $requestPath, string $expectedResponse, int $responseCode)
    {
        $this->loadFixturesFromDirectory('scores/sort');
        $response = $this->makeRequest('GET', $requestPath);

        $this->assertResponse($response, $expectedResponse, $responseCode);
    }

    public function getSuccessDataTests(): array
    {
        return [
            ['/api/user/scores?sort_field=xxxx', 'user/invalid_sort_field', Response::HTTP_BAD_REQUEST],
            ['/api/user/scores?sort_order=xxxx', 'user/invalid_sort_direction', Response::HTTP_BAD_REQUEST],
            ['/api/user/scores?sort_field=score', 'user/success_sort_score_desc', Response::HTTP_OK],
            ['/api/user/scores?sort_field=score&sort_order=asc', 'user/success_sort_score_asc', Response::HTTP_OK],
            ['/api/user/scores?sort_field=finishedAt', 'user/success_sort_finished_at_desc', Response::HTTP_OK],
            ['/api/user/scores?sort_field=finishedAt&sort_order=asc', 'user/success_sort_finished_at_asc', Response::HTTP_OK],
        ];
    }
}
