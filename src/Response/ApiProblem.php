<?php

namespace App\Response;

use SoftPassio\ApiExceptionBundle\Component\Api\ApiProblemInterface;

class ApiProblem implements ApiProblemInterface
{
    public const KEY_DETAILS = 'detail';
    public const KEY_INVALID_PARAMS = 'invalid_params';
    public const KEY_TITLE = 'title';
    public const TITLE_NOT_FOUND = 'Resource not found';
    public const BAD_REQUEST = 'Form error';

    private const KEY_STATUS = 'status';
    private const KEY_TYPE = 'type';
    private const DEFAULT_TYPE = 'about:blank';

    private $statusCode;

    private $type;

    private $title;

    private $extraData = [];

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;

        if (null === $type) {
            $type = self::DEFAULT_TYPE;
        }

        $this->type = $type;
    }

    public function toArray()
    {
        return array_merge(
            [
                self::KEY_STATUS         => $this->statusCode,
                self::KEY_TYPE           => $this->type,
                self::KEY_INVALID_PARAMS => [],
            ],
            $this->extraData
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
