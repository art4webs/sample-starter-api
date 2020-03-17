<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class SuccessResponse
{
    /** @var string */
    private $detail;

    /** @var int */
    private $status;

    /** @var string */
    private $type;

    /** @var string */
    private $title;

    /** @var array */
    private $invalidParams;

    private const DEFAULT_TYPE = 'about:blank';

    public function __construct(string $detail, string $title)
    {
        $this->status = Response::HTTP_OK;
        $this->type = self::DEFAULT_TYPE;
        $this->invalidParams = [];
        $this->detail = $detail;
        $this->title = $title;
    }
}
