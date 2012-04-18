<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class GearmanRouter extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('gearman');
    }

	public function worker()
	{
        return $this->gearman->worker();
    }
    
    public function client()
	{
        return $this->gearman->client();
    }
    
}