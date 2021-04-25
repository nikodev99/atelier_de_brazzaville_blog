<?php

namespace Framework\Database;

use Exception;
use Throwable;

class NoRecordException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
