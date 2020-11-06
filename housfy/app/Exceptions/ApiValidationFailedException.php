<?php

namespace App\Exceptions;

use Exception;

use App\Exceptions\ApiException;

class ApiValidationFailedException extends ApiException
{
    /**
     * @var int
     */
    protected $defaultCode = 422;
    
}