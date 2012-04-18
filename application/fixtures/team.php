<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Team {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('TeamRepository', '_team');
    }

    public function load()
    {
        $this->clear();
        
        // Miami Hurricanes
        
        $team = new Team('Miami, FL');
        $team->setOfficialName('University of Miami');
        $team->setNickname('Hurricanes');
        
        $team->setCity('Miami');
        $team->setState('FL');
        
        $team->setNcaaId(415);
        
        $level = $this->CI->_level->findOneBySlug('college');
        $team->setLevel($level);
        
        $league = $this->CI->_league->findOneBySlug('ncaa');
        $team->setLeague($league);
        
        $division = $this->CI->_division->findOneBySlug('d1');
        $team->setDivision($division);
        
        $conference = $this->CI->_conference->findOneBySlug('atlantic-coast');
        $team->setConference($conference);
        
        $team->save();
        
        print_r(sprintf("Created Team: %s (%s)\n", $team->getName(), $team->getId()));
        
        // Maryland Terrapins
        
        $team = new Team('Maryland');
        $team->setOfficialName('University of Maryland');
        $team->setNickname('Terrapins');
        
        $team->setCity('College Park');
        $team->setState('FL');
        
        $team->setNcaaId(392);
        
        $level = $this->CI->_level->findOneBySlug('college');
        $team->setLevel($level);
        
        $league = $this->CI->_league->findOneBySlug('ncaa');
        $team->setLeague($league);
        
        $division = $this->CI->_division->findOneBySlug('d1');
        $team->setDivision($division);
        
        $conference = $this->CI->_conference->findOneBySlug('atlantic-coast');
        $team->setConference($conference);
        
        $team->save();
        
        print_r(sprintf("Created Team: %s (%s)\n", $team->getName(), $team->getId()));
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'team');
    }

}