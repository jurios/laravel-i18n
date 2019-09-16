<?php

namespace Kodilab\LaravelI18n\Exceptions;


use Kodilab\LaravelI18n\i18n;
use Throwable;

class LocaleAlreadyExists extends \Exception
{
    public function __construct(string $iso, string $region, $code = 0, Throwable $previous = null)
    {
        $name = i18n::generateName($iso, $region);

        parent::__construct("Locale '{$name}' already exists.", $code, $previous);
    }
}