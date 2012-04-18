<?php

class MY_Controller extends CI_Controller  {

    public $dm;
 
    function __construct()
    {
        parent::__construct();
        
        $this->mongo = $this->mongo_db;
        
        /* Twig Helpers */
        foreach ($this->twig_helpers as $helper) {
            $this->twig->add_function($helper);
        }
        
        /*
        $this->load->library('session');
        
        $this->load->library('mixpanel');
        
        $this->twig->add_function('debug');
        $this->twig->add_function('path');
		$this->twig->add_function('alert_exists');
		$this->twig->add_function('alert_get');
		$this->twig->add_function('stats_build_headers');
		$this->twig->add_function('stats_build_rows');
		$this->twig->add_function('pagination');
		$this->twig->add_function('assets');
		$this->twig->add_function('sbs_user');
		$this->twig->add_function('search_objects');
		
		$this->twig->add_function('airbrake', array('is_safe' => array('html')));
		$this->twig->add_function('google_analytics', array('is_safe' => array('html')));
		$this->twig->add_function('mixpanel', array('is_safe' => array('html')));
		$this->twig->add_function('quantcast', array('is_safe' => array('html')));
		$this->twig->add_function('crazyegg', array('is_safe' => array('html')));
		$this->twig->add_function('olark', array('is_safe' => array('html')));
		
		$this->storage->load_driver('rackspace-cf');
		*/
    }
    
    protected function response($content, $code = 200, $type = 'application/json')
    {
        return $this->output
            ->set_content_type($type)
            ->set_status_header($code)
            ->set_output(json_encode($content));
    }
    
    protected function getRemotePage($url)
    {
        print_r($url);
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20); 
        $page = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        print_r($page);
        die();
        
        return $page;
    }

} 