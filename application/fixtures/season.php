<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Season {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('SeasonRepository', '_season');
    }

    public function load()
    {
        $this->clear();
        
        // Alfredo Rodriguez (Maryland)
        
        $season = new Season();
        $season->setYear(2012);
        
        $season->save();
        
        print_r(sprintf("Created Season: %s, %s (%s)\n", $player->getName(), $season->getYear(), $player->getId()));
        
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'player');
    }

}