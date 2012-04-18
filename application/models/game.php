<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Game extends Document {

    protected $level;
    protected $league;
    protected $division;
    protected $conference;
    protected $datetime;
    protected $startTime;
    protected $endTime;
    protected $duration;
    protected $location;
    protected $attendance;
    protected $innings;
    protected $away;
    protected $home;
    protected $umpires;
    protected $url;
    protected $urlVerified;
    protected $parsed;
    protected $page;
    protected $pointstreakId;
    protected $tasId;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('teamRepository', '_team');
    }
    
    /*
    public function __toString()
    {
        return 'test';
    }
    */
    
    public function getDatetime()
    {
        return $this->datetime->sec;
    }
    
    public function setDatetime($datetime)
    {
        if (!is_object($datetime)) {
            $datetime = new MongoDate(strtotime($datetime));
        }
        
        $this->datetime = $datetime;
    }
    
    public function getLevel()
    {
        return (string) $this->level;
    }
    
    public function setLevel($level)
    {
        $this->level = (string) $level;
    }
    
    public function getLeague()
    {
        if (is_array($this->league)) {
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
    
    public function getDivision()
    {
        return (string) $this->division;
    }
    
    public function setDivision($division)
    {
        $this->division = (string) $division;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    
    public function setLocation($location)
    {
        $this->location = $location;
    }
    
    public function getAttendance()
    {
        return $this->attendance;
    }
    
    public function setAttendance($attendance)
    {
        $this->attendance = $attendance;
    }
    
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }
    
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }
    
    public function getDuration()
    {
        return $this->duration;
    }
    
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
    
    public function getInnings()
    {
        return $this->innings;
    }
    
    public function setInnings($innings)
    {
        $this->innings = $innings;
    }

    public function getHome()
    {
        $this->load->model('Game/GameTeamRepository', '_gameteam');
                
        return $this->_gameteam->assign($this->home);
    }
    
    public function setHome($home)
    {
        if (is_object($home)) {
            $home = $home->toArray();
        }
    
        $this->home = $home;
    }
    
    public function getAway()
    {
        $this->load->model('Game/GameTeamRepository', '_gameteam');
                
        return $this->_gameteam->assign($this->away);
    }
    
    public function setAway($away)
    {
        if (is_object($away)) {
            $away = $away->toArray();
        }
    
        $this->away = $away;
    }
    
    public function getOpponent($team)
    {
        if ($this->getHome()->getTeam() == $team) {
            return $this->getAway()->getTeam();
        }
        
        return $this->getHome();
    }
        
    public function getResult($team)
    {
        if ($this->isWin($team)) {
            return 'W';
        }
        
        return 'L';
    }
    
    public function isWin($team)
    {
        if ($this->getHomeTeam() == $team) {
            if ($this->getHomeScore() > $this->getAwayScore()) {
                return true;
            } else {
                return false;
            }
        }
        
        if ($this->getHomeScore() > $this->getAwayScore()) {
            return false;
        } else {
            return true;
        }
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function getUrlVerified()
    {
        return $this->urlVerified;
    }
    
    public function setUrlVerified($verified)
    {
        $this->urlVerified = $verified;
    }
    
    public function getParsed()
    {
        return $this->parsed;
    }
    
    public function setParsed($parsed)
    {
        $this->parsed = $parsed;
    }
    
    public function getPage()
    {
        if (!$this->page) {
            return null;
        }
        
        $results = $this->mongo
            ->get_dbref($this->page);
            
        $this->load->model('pageRepository', '_page');
            
        return $this->_page->assign($results);
    }
    
    public function setPage($page)
    {
        if (is_object($page)) {
            $this->page = $this->mongo
                ->create_dbref('page', $page->getId());
        } else {
            $this->page = $page;
        }
    }
    
    public function getPointstreakId()
    {
        return $this->pointstreakId;
    }
    
    public function setPointstreakId($pointstreakId)
    {
        $this->pointstreakId = (int) $pointstreakId;
    }
    
    public function getGametrackerId()
    {
        return $this->gametrackerId;
    }

    public function setGametrackerId($gametrackerId)
    {
        $this->gametrackerId = $gametrackerId;
    }

}