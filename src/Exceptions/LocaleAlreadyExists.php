<?php

namespace Kodilab\LaravelI18n\Exceptions;


use Kodilab\LaravelI18n\Facades\i18n;
use Throwable;

class LocaleAlreadyExists extends \Exception
{
    public function __construct(string $language, string $region, $code = 0, Throwable $previous = null)
    {
        $reference = i18n::generateReference($language, $region);

        parent::__construct("Locale '{$reference}' already exists.", $code, $previous);
    }
}