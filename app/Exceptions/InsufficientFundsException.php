<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    protected $code = 409;
}
