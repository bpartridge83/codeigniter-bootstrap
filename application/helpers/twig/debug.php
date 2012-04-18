<?php

if (!function_exists('debug')) {

    function debug()
    {
        if (ENVIRONMENT && ENVIRONMENT == 'production') {
            print_r('<script type="text/javascript">debug.setLevel(0)</script>');
        }
        
        return false;
    };    
    
}