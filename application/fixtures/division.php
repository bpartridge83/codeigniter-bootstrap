<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Division {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('DivisionRepository', '_division');
    }

    public function load()
    {
        $this->clear();
        
        $division = new Division('D1');
        
        $level = $this->CI->_level->findOneBySlug('college');
        $division->setLevel($level);
        
        $league = $this->CI->_league->findOneBySlug('ncaa');
        $division->setLeague($league);
        
        $division->save();
        
        print_r(sprintf("Created Division: %s (%s)\n", $division->getName(), $division->getId()));
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'division');
    }

}