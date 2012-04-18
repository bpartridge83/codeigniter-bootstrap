<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_League {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('LeagueRepository', '_league');
    }

    public function load()
    {
        $this->clear();
        
        $league = new League('NCAA');
        $summer = $this->CI->_level->findOneBySlug('college');
        $league->setLevel($summer);
        $league->save();
        
        print_r(sprintf("Created League: %s (%s)\n", $league->getName(), $league->getId()));
        
        $league = new League('Cape Cod Baseball');
        $summer = $this->CI->_level->findOneBySlug('summer');
        $league->setLevel($summer);
        $league->save();
        
        print_r(sprintf("Created League: %s (%s)\n", $league->getName(), $league->getId()));
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'league');
    }

}