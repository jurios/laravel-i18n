<?php

namespace Kodilab\LaravelI18n\Exceptions;

use Throwable;


class MissingLanguageException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}