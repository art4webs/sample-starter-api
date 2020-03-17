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

    public function testGetUserInvalidSortOrderField(): void
    {
        $this->loadFixturesFromDirectory('scores/collection');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_field=xxxx');

        $this->assertResponse($response, 'user/invalid_sort_field', Response::HTTP_BAD_REQUEST);
    }

    public function testGetUserInvalidSortOrderDirection(): void
    {
        $this->loadFixturesFromDirectory('scores/collection');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_order=xxxx');

        $this->assertResponse($response, 'user/invalid_sort_direction', Response::HTTP_BAD_REQUEST);
    }

    public function testGetUserScoreFirstPageOrderScoreDescSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/sort');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_field=score');

        $this->assertResponse($response, 'user/success_sort_score_desc', Response::HTTP_OK);
    }

    public function testGetUserScoreFirstPageOrderScoreAscSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/sort');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_field=score&sort_order=asc');

        $this->assertResponse($response, 'user/success_sort_score_asc', Response::HTTP_OK);
    }

    public function testGetUserScoreFirstPageOrderFinishedAtDescSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/sort');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_field=finishedAt');

        $this->assertResponse($response, 'user/success_sort_finished_at_desc', Response::HTTP_OK);
    }

    public function testGetUserScoreFirstPageOrderFinishedAtAscSuccess(): void
    {
        $this->loadFixturesFromDirectory('scores/sort');
        $response = $this->makeRequest('GET', '/api/user/scores?sort_field=finishedAt&sort_order=asc');

        $this->assertResponse($response, 'user/success_sort_finished_at_asc', Response::HTTP_OK);
    }
}
