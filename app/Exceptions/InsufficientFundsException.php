<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    protected $message = 'Недостаточно средств на счёте';

    protected $code = 409;
}
