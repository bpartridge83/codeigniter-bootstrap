<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Conference extends Document implements Sluggable {

    protected $slug;
    protected $level;
    protected $league;
    protected $division;

    public function __construct($name = null)
    {
        parent::__construct();
        $this->load->model('teamRepository', '_team');
        
        if ($name) {
            $this->setName($name);
        }
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setSlug($slug = null, $auto = true)
    {
        if (!$slug && $auto) {
            $slug = $this->slugify->create($this);
        }
        
        if ($slug) {
            $this->slug = $slug;
        }
    }
    
    public function getTeams()
    {
        return $this->_team->findAllByConference($this->getId());
    }
    
    public function getTeamsForYear($year)
    {
        return $this->_team->findAllByConferenceWithYear($this->getId(), $year);
    }
    
    public function getSeasons()
    {
        return $this->_conference->findUniqueSeasonsWithConference($this);
    }
    
    public function hasLevel()
    {
        return (bool) $this->level;
    }
    
    public function getLevel()
    {
        if (is_object($this->level)) {
            $results = $this->mongo
                ->get_dbref($this->level);
                
            $this->load->model('LevelRepository', '_level');
                
            return $this->_level->assign($results);
        }
        
        return $this->level;
    }
    
    public function setLevel($level)
    {
        if (is_object($level)) {
            $level = $this->mongo
                ->create_dbref('level', $level->getId());
        }
    
        $this->level = $level;
    }
    
    public function hasLeague()
    {
        return (bool) $this->league;
    }
    
    public function getLeague()
    {
        if (is_object($this->league)) {
            $results = $this->mongo
                ->get_dbref($this->league);
                
            $this->load->model('LeagueRepository', '_league');
                
            return $this->_league->assign($results);
        }
        
        return $this->league;
    }
    
    public function setLeague($league)
    {
        if (is_object($league)) {
            $league = $this->mongo
                ->create_dbref('league', $league->getId());
        }
    
        $this->league = $league;
    }
    
    public function hasDivision()
    {
        return (bool) $this->division;
    }
    
    public function getDivision()
    {
        if (is_object($this->division)) {
            $results = $this->mongo
                ->get_dbref($this->division);
                
            $this->load->model('DivisionRepository', '_division');
                
            return $this->_division->assign($results);
        }
        
        return $this->division;
    }
    
    public function setDivision($division)
    {
        if (is_object($division)) {
            $division = $this->mongo
                ->create_dbref('division', $division->getId());
        }
    
        $this->division = $division;
    }
    
}