<?php

namespace App\Exceptions;

use Exception;

use App\Exceptions\ApiException;

class ApiResourceNotFoundException extends ApiException
{
    /**
     * @var int
     */
    protected $defaultCode = 404;
    protected $defaultMessage = 'API Resource not found';
    
}