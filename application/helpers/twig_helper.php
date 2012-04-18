<?php

if ($dh = opendir(APPPATH.'/helpers/twig')) {
    get_instance()->twig_helpers = array();
    while (($file = readdir($dh)) !== false) {
        if ( strlen($file) > 3) {
            require_once(sprintf(APPPATH.'/helpers/twig/%s', $file));
            array_push(get_instance()->twig_helpers, str_replace('.php', '', $file));
        }
    }
}
