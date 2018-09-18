<?php

namespace Kodilab\LaravelI18n\Filters;


use Kodilab\LaravelFilters\QueryFilter;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;

class LanguageFilter extends QueryFilter
{
    public function __construct(Request $request, Repository $config)
    {
        parent::__construct($request, $config);

        if (!$this->request()->has($this->addPrefix('order_asc')) && !$this->request()->has($this->addPrefix('order_desc')))
        {
            $this->request->merge([ $this->addPrefix('order_desc') => 'enabled']);
        }

        $this->paginate = 15;
    }
}