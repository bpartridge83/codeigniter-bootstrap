<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Twig_Router
{
	function build($path, $params = null)
	{
        include(APPPATH.'config/routes.php');
        
        if (!array_key_exists($path, $route)) {
            return false;
        }
        
        if (!array_key_exists('parameters', $route[$path])) {
            return sprintf('/%s', $route[$path]['pattern']);
        }
        
        $output = $route[$path]['pattern'];
        
        foreach ($params as $key => $parameter) {
            $output = str_replace('{'.$key.'}', $parameter, $output);
        }
        
        if (strpos($output, '{') > 0) {
            print_r('"></a>Missing parameter for <pre>'.$path.'</pre>');
            die();
        }
        
        return sprintf('/%s', $output);
	}
	
}