<?php

namespace App\Controller;

use App\Response\ApiProblem;
use App\Response\SuccessResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use SoftPassio\ApiExceptionBundle\Component\Factory\ApiProblemFactoryInterface;
use SoftPassio\ApiExceptionBundle\Exception\ApiProblemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ApiBaseController extends AbstractController
{
    private const SUCCESS_RESPONSE = 'success_response';
    private const ERROR_RESPONSE = 'error_response';
    private const DETAIL_TRANSLATION_KEY = 'detail';
    private const TITLE_TRANSLATION_KEY = 'title';

    public const PAGINATION_PAGE_LIMIT = 20;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ApiProblemFactoryInterface
     */
    protected $apiProblemFactory;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        SerializerInterface $serializer,
        RequestStack $requestStack,
        ApiProblemFactoryInterface $apiProblemFactory,
        PaginatorInterface $paginator,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->request = $requestStack->getCurrentRequest();
        $this->apiProblemFactory = $apiProblemFactory;
        $this->paginator = $paginator;
        $this->translator = $translator;
    }

    protected function paginatedResponse($query, array $groups = ['Default'], array $extraData = [])
    {
        $requestQuery = $this->request->query;

        $page = ($requestQuery->get('page')) ? (int) $requestQuery->get('page') : 1;
        $limit = ($requestQuery->get('limit')) ? (int) $requestQuery->get('limit') : self::PAGINATION_PAGE_LIMIT;

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $page, $limit, ['wrap-queries' => true]);

        $items = $pagination->getItems();
        if (empty($items)) {
            return $this->emptyView();
        } else {
            $data = [
                'current_page'   => $pagination->getCurrentPageNumber(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'total_items'    => $pagination->getTotalItemCount(),
                'items'          => $items,
            ];

            if (!empty($extraData)) {
                $data = array_merge($data, $extraData);
            }
        }

        return $this->serializedResponse($data, $groups);
    }

    protected function serializedResponse($data, array $groups = ['Default']): Response
    {
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->setSerializeNull(true);

        $response = $this->serializer->serialize($data, 'json', $context);

        return new JsonResponse($response, 200, [], true);
    }

    protected function notFoundResponse($message, array $parameters = [])
    {
        $details = [
            ApiProblem::KEY_DETAILS => $this->translateMessage($message, $parameters),
            ApiProblem::KEY_TITLE   => ApiProblem::TITLE_NOT_FOUND,
        ];
        $apiProblem = $this->apiProblemFactory->create(Response::HTTP_NOT_FOUND, null, $details);

        throw new ApiProblemException($apiProblem);
    }

    protected function emptyView()
    {
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    protected function errorResponse(string $translationKey, array $errors = [])
    {
        list($title, $detail) = $this->getResponseMessages(self::ERROR_RESPONSE, $translationKey);

        $apiProblem = $this->apiProblemFactory->create(
            Response::HTTP_BAD_REQUEST,
            null,
            [
                ApiProblem::KEY_DETAILS        => $detail,
                ApiProblem::KEY_TITLE          => $title,
                ApiProblem::KEY_INVALID_PARAMS => $this->translateMessage($errors),
            ]
        );

        throw new ApiProblemException($apiProblem);
    }

    protected function notFoundView($message, array $parameters = [])
    {
        $apiProblem = $this->apiProblemFactory->create(
            Response::HTTP_NOT_FOUND,
            null,
            [
                ApiProblem::KEY_DETAILS => $this->translateMessage($message, $parameters),
                ApiProblem::KEY_TITLE   => ApiProblem::TITLE_NOT_FOUND,
            ]
        );

        throw new ApiProblemException($apiProblem);
    }

    protected function errorView($errors, array $parameters = [])
    {
        $apiProblem = $this->apiProblemFactory->create(
            Response::HTTP_BAD_REQUEST,
            null,
            $this->translateMessage($errors, $parameters)
        );

        throw new ApiProblemException($apiProblem);
    }

    protected function successResponse(string $translationKey, array $params = [])
    {
        list($title, $detail) = $this->getResponseMessages(self::SUCCESS_RESPONSE, $translationKey, $params);
        $data = $this->serializer->serialize(new SuccessResponse($title, $detail), 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    private function getResponseMessages(string $responseType, string $translationKey, array $params = []): array
    {
        $translationKeyTitle = implode('.', [$responseType, $translationKey, self::TITLE_TRANSLATION_KEY]);
        $translationKeyDetail = implode('.', [$responseType, $translationKey, self::DETAIL_TRANSLATION_KEY]);

        $translatedTitle = $this->translator->trans($translationKeyTitle, $params);
        $translatedDetail = $this->translator->trans($translationKeyDetail, $params);

        return [$translatedTitle, $translatedDetail];
    }

    private function translateMessage($message, array $parameters = [])
    {
        if (!is_array($message)) {
            return $this->translator->trans($message, $parameters);
        }
        $translatedMessages = [];
        foreach ($message as $key => $part) {
            if (is_array($part)) {
                $translatedMessages[$key] = $this->translateMessage($part);
            } else {
                $translatedMessages[$key] = $this->translator->trans($part);
            }
        }

        return $translatedMessages;
    }
}
