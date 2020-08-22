<?php

namespace App\Exceptions;

use Exception;

class JsonValidationException extends Exception
{
    protected $code = 400;
}
