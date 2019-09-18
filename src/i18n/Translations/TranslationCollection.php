<?php


namespace Kodilab\LaravelI18n\i18n\Translations;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class TranslationCollection extends Collection
{
    public function add($item)
    {
        if (get_class($item) === Translation::class) {
            return $this->put($item->getPath(), $item);
        }
    }

    public function toRaw()
    {
        $result = [];

        /** @var Translation $item */
        foreach ($this->items as $item) {
            $result[$item->getPath()] = $item->getTranslation();
        }

        return $result;
    }

    /**
     * Custom pluck for classes
     *
     * @param array|string $value
     * @param null $key
     * @return Collection|\Illuminate\Support\Collection
     */
    public function pluck($value, $key = null)
    {
        if (count ($this->items) === 0 || is_array($this->first())) {
            return parent::pluck($value, $key);
        }

        //TODO: Some use cases of pluck are not being considered here
        $collection = new Collection();

        foreach ($this->items as $item) {
            $collection->add($item->$value);
        }

        return $collection;
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