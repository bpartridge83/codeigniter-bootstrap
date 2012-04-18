<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index()
	{
        enable_profiler($this->output);
        
        $params = array();
        
        return $this->twig->display('index.html.twig', $params);
	}
    	
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */