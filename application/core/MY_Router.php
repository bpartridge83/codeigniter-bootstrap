<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	/**
	 *  Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	private
	 * @return	void
	 */
	function _parse_routes()
	{
		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);
		$uri = str_replace(':', '/', $uri);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
            $action = explode(':', $this->routes[$uri]['action']);
            $action[1] = sprintf('%sAction', $action[1]);
            
			return $this->_set_request($action);
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
            $route = str_replace(':', '/', $val['pattern']);

            if (is_array($val) && array_key_exists('parameters', $val)) {
                foreach ($val['parameters'] as $req_key => $req_val) {
                    $route = str_replace(sprintf('{%s}', $req_key), $req_val, $route);
                }
            }
		
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $route));
    
			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri))
			{
				// Do we have a back-reference?
				/*
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}
				*/
				
				$params = array();
                $slices = array();
                $temp = explode('/', $val['pattern']);
                
                foreach ($temp as $key => $param) {
                    if (strpos($param, '{') > -1) {
                        $slices[$key] = $param;
                    }
                }
                
                $uri_pieces = explode('/', $uri);
                
                foreach ($uri_pieces as $key => $piece) {
                    if (array_key_exists($key, $slices)) {
                        $params[] = $piece;
                    }
                }
                
                $action = explode(':', $val['action']);
                $action[1] = sprintf('%sAction', $action[1]);

				return $this->_set_request(array_merge($action, $params));
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request($this->uri->segments);
	}
    
}  