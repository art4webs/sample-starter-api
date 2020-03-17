<?php

namespace App\Factory;

use App\Response\ApiProblem;
use SoftPassio\ApiExceptionBundle\Component\Factory\ApiProblemFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiProblemFactory implements ApiProblemFactoryInterface
{
    private const API_PROBLEM_KEYS = [ApiProblem::KEY_DETAILS, ApiProblem::KEY_INVALID_PARAMS];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function create($statusCode, $type = null, $details = null): ApiProblem
    {
        $apiProblem = new ApiProblem($statusCode, $type);
        if (null === $details) {
            return $apiProblem;
        }

        if (!is_array($details)) {
            $details = [
                ApiProblem::KEY_DETAILS => $details,
                ApiProblem::KEY_TITLE   => ApiProblem::BAD_REQUEST,
            ];
        }

        $this->setDetails($apiProblem, $details);
        $this->setTitle($apiProblem, $details);

        return $apiProblem;
    }

    private function setTitle(ApiProblem $apiProblem, array $details): void
    {
        if (array_key_exists(ApiProblem::KEY_TITLE, $details)) {
            $apiProblem->set(ApiProblem::KEY_TITLE, $details[ApiProblem::KEY_TITLE]);
        }
    }

    private function setDetails(ApiProblem $apiProblem, array $details): void
    {
        foreach ($details as $key => $value) {
            if (!in_array($key, self::API_PROBLEM_KEYS)) {
                continue;
            }

            $apiProblem->set($key, $value);
        }
    }
}
