<?php

namespace Kodilab\LaravelI18n\Filters;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Kodilab\LaravelFilters\QueryFilters;

class LocaleFilter extends QueryFilters
{
    public function __construct(Request $request, string $prefix = null)
    {
        parent::__construct($request, $prefix);

        if (!isset($this->filters['order_asc']) && !isset($this->request['order_desc']))
        {
            $this->filters['order_desc'] = 'enabled';
        }

        $this->pagination = 15;
    }
}