<?php

use Upsoftware\Core\Classes\Core;

if (! function_exists('core')) {
    /**
     * Core helper.
     */
    function core(): Core
    {
        return app('core');
    }
}
