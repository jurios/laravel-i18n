<?php


namespace Kodilab\LaravelI18n\Support;


/**
 * Class Arr
 * @package Kodilab\LaravelI18n\Support
 *
 * Extended Arr Laravel Support class in order to provide some helper methods to export an array to "string". This is
 * used basically when an array must to be "saved" in a file
 *
 */
class Arr extends \Illuminate\Support\Arr
{
    public static function toString(array $array)
    {
        $content = "[" . PHP_EOL;
        $content = $content . self::printArrayContent($array, 1);

        return $content . PHP_EOL . "];";
    }

    /**
     * Returns the array content (without '[]') string
     *
     * @param array $array
     * @param int $tabulation_level
     * @return string
     */
    private static function printArrayContent(array $array, int $tabulation_level = 0)
    {
        $content = '';

        for ($i = 0; $i < count($array); $i++) {
            $key = array_keys($array)[$i];
            if (is_array($array[$key])) {

                $content .= printTab($tabulation_level + 1);

                $content .= "'" . $key . "'" . self::printArrayTab(1) . "=> [" . PHP_EOL;

                $content .= self::printArrayContent($array[$key], $tabulation_level + 1);

                $content .= self::printArrayTab($tabulation_level + 1) . "], \n";

            } else {

                $content .= self::printArrayItem($key, $array[$key], $tabulation_level + 1);
            }
        }
        return $content;
    }

    /**
     * Returns blank spaces token
     *
     * @param $times
     * @return string
     */
    private static function printArrayTab($times)
    {
        $content = "";
        for ($i = 0; $i < $times; $i++) {
            $content = $content . "    ";
        }
        return $content;
    }

    /**
     * Returns a "key => value" token
     *
     * @param $key
     * @param $value
     * @param $level
     * @return string
     */
    private static function printArrayItem($key, $value, $level)
    {
        $content = self::printArrayTab($level);
        $content = $content . "'" . $key . "'" . printTab(1) . "=> '" . $value . "'," . PHP_EOL;
        return $content;
    }
}