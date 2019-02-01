<?php

namespace Kodilab\LaravelI18n\Controllers;


class I18nController extends \Illuminate\Routing\Controller
{
    /**
     * Returns the view which is defined in the configuration. If it isn't, then returns $default
     * @param string $function
     * @param $default
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getConfigView(string $function, $default)
    {
        try {
            $index = $this->getConfigIndex($function);
        }
        catch (\ReflectionException $e)
        {
            return $default;
        }

        return config('i18n.views.' . $index, $default);
    }

    /**
     * Returns the controller action
     *
     * @param string $function
     * @return string
     * @throws \ReflectionException
     */
    private function getConfigIndex(string $function)
    {
        $class = (new \ReflectionClass($this))->getShortName();

        return $class . '@' . $function;
    }
}
