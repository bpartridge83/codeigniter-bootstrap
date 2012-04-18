<?php

if (!function_exists('path')) {

    function path($path, $params = null)
    {
		$CI=& get_instance();
        $CI->load->library('Twig_Router');
		
		return $CI->twig_router->build($path, $params);
    }
    
}