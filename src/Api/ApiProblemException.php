<?php

declare(strict_types=1);

namespace App\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    /**
     * @var ApiProblem $apiProblem
     */
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, string $message = null, \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $this->apiProblem = $apiProblem;

        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem $apiProblem
     */
    public function getApiProblem(): ApiProblem
    {
        return $this->apiProblem;
    }
}