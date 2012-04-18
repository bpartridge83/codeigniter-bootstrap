<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Coach extends Document implements Sluggable {

    protected $ncaaId;
    protected $slug;
    protected $firstName;
    protected $lastName;

    public function __construct()
    {
        parent::__construct();
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
    
    public function getName()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    
    public function getNcaaId()
    {
        return $this->ncaaId;
    }
    
    public function setNcaaId($ncaaId)
    {
        $this->ncaaId = $ncaaId;
    }
    
    public function hasSeasons()
    {
        return ($this->getSeasons()) ? true : false;
    }
    
    public function hasSeason($year)
    {
        foreach ($this->seasons as $key => $temp) {
            if ($temp['year'] == $year) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getSeasons()
    {
        $this->load->model('teamRepository', '_team');
        
        return $this->_team->findSeasonsWithCoach($this);
    }
    
    public function getSeason($year)
    {
        foreach ($this->seasons as $key => $season) {
            if ($season['year'] == $year) {
                $this->load->model('seasonRepository', '_season');
                return $this->_season->assign($season);
            }
        }
        
        return null;
    }
    
    public function addSeason($season)
    {
        foreach ($this->seasons as $key => $temp) {
            if ($temp['year'] == $season->getYear()) {
                unset($this->seasons[$key]);
            }
        }
        
        $season = $season->toArray();
        $this->seasons = array_values($this->seasons);
        array_push($this->seasons, $season);
        
        return $this->seasons;
    }
    
    public function getWins()
    {
        $seasons = $this->getSeasons();
        
        $wins = 0;
        
        foreach ($seasons as $season) {
            $wins += $season->getWins();
        }
        
        return $wins;
    }
    
    public function getLosses()
    {
        $seasons = $this->getSeasons();
        
        $losses = 0;
        
        foreach ($seasons as $season) {
            $losses += $season->getLosses();
        }
        
        return $losses;
    }
    
    public function getTeams()
    {
        $seasons = $this->getSeasons();
        
        $teams = array();
        
        foreach ($seasons as $season) {
            if (!in_array($season->getTeam(), $teams)) {
                $teams[] = $season->getTeam();
            }
        }
        
        return $teams;
    }
        
}