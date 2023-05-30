<?php

namespace NadzorServera\Skijasi\Module\Commerce\Helper;

class Route
{
    public static function getController($key)
    {
        // get config 'controllers' from config/skijasi-commerce.php
        $controllers = config('skijasi-commerce.controllers');

        // if the key is not found, return $key
        if (! isset($controllers[$key])) {
            return 'NadzorServera\\Skijasi\\Module\\Commerce\\Controllers\\'.$key;
        }

        // return the value of the key
        return $controllers[$key];
    }
}
