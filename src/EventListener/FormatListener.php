<?php

namespace App\EventListener;

use SoftPassio\ApiExceptionBundle\Component\Factory\ApiProblemFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;

class FormatListener
{
    /**
     * @var ApiProblemFactoryInterface
     */
    private $factory;

    public function __construct(ApiProblemFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return true;
        }

        $pathInfo = explode('/', $request->getPathInfo());

        if ($pathInfo && 'api' === $pathInfo[0]) {
            $request->headers->set('Content-Type', 'application/json');
        }

        $format = $request->headers->get('Content-Type');
        if ('application/json' === $format) {
            $data = json_decode($request->getContent(), true);

            if (null === $data) {
                return true;
            }

            if (
                JSON_ERROR_NONE !== json_last_error()
                && 'doc' !== $pathInfo[2]
                && in_array($request->getMethod(), [Request::METHOD_PATCH, Request::METHOD_POST, Request::METHOD_PUT])
            ) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, 'Niepoprawny format danych');
            }

            $request->request->replace($data);

            return true;
        }
    }
}
