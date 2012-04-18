<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Conference {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('ConferenceRepository', '_conference');
    }

    public function load()
    {
        $this->clear();
        
        $conference = new Conference('Atlantic Coast');
        
        $level = $this->CI->_level->findOneBySlug('college');
        $conference->setLevel($level);
        
        $league = $this->CI->_league->findOneBySlug('ncaa');
        $conference->setLeague($league);
        
        $division = $this->CI->_division->findOneBySlug('d1');
        $conference->setDivision($division);
        
        $conference->save();
        
        print_r(sprintf("Created Conference: %s (%s)\n", $conference->getName(), $conference->getId()));
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'conference');
    }

}