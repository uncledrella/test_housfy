<?php

namespace App\Exceptions;

use Exception;

use App\Exceptions\ApiException;

class ApiItemNotFoundException extends ApiException
{
    /**
     * @var int
     */
    protected $defaultCode = 404;
    
}