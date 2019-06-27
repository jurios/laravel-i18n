<?php


namespace Kodilab\LaravelI18n\Translations;


use Illuminate\Contracts\Support\Arrayable;
use Kodilab\LaravelFilters\Filters\CollectionFilters;

class Collection extends \Illuminate\Support\Collection
{
    public function filters(string $filter_class, array $input = [], string $prefix = '')
    {
        /** @var CollectionFilters $filters */
        $filters = new $filter_class();

        return $filters->apply($this, $input, $prefix);
    }

    protected function operatorForWhere($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            $value = true;
            $operator = '=';
        }
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        return function ($item) use ($key, $operator, $value) {
            if ($item instanceof Arrayable) {
                $item = $item->toArray();
            }
            $retrieved = data_get($item, $key);
            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });
            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }
            switch ($operator) {
                default:
                case '=':
                case '==':  return $retrieved == $value;
                case '!=':
                case '<>':  return $retrieved != $value;
                case '<':   return $retrieved < $value;
                case '>':   return $retrieved > $value;
                case '<=':  return $retrieved <= $value;
                case '>=':  return $retrieved >= $value;
                case '===': return $retrieved === $value;
                case '!==': return $retrieved !== $value;
            }
        };
    }
}