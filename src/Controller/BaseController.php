<?php
/**
 * Created by PhpStorm.
 * User: zarko
 * Date: 29.9.20.
 * Time: 21.51
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Api\ApiProblem;
use App\Api\ApiProblemException;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * @param $errors
     * @return array
     */
    public function getValidationErrors($errors): array
    {
        $messages = [];
        foreach ($errors as $violation) {
            $messages[] = $violation->getMessage();
        }
        return $messages;
    }

    /**
     * @param array $errors
     * @throws ApiProblemException
     */
    public function throwValidationException(array $errors)
    {
        $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
        $apiProblem->set('errors', $errors);
        throw new ApiProblemException($apiProblem);
    }
}