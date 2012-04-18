<?php

if (!function_exists('enable_profiler')) {

    function enable_profiler($output, $force = false)
    {
        if ($force || isset($_GET['profile'])) {
            $output->enable_profiler(TRUE);
            $GLOBALS['profiler_enabled'] = true;
        }
        
        return null;
    }

}
