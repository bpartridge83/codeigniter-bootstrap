<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

require_once('seasonable.php');

class Team extends Document implements Sluggable, Seasonable {

    protected $slug;
    protected $alternateSlugs = array();
    protected $officialName;
    protected $shortName;
    protected $nickname;
    protected $city;
    protected $state;
    protected $level;
    protected $league;
    protected $division;
    protected $conference;
    protected $ncaaId;
    protected $bisId;
    protected $lahmanId;
    protected $boydsworldId;
    protected $alternateNcaaIds = array();
    protected $seasons = array();

    public function __construct($name = null)
    {
        parent::__construct();
        
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
    
    public function getAlternateSlugs()
    {
        return $this->alternateSlugs;
    }
    
    public function addAlternateSlug($slug)
    {
        if ($this->hasAlternateSlug($slug)) {
            return true;
        }
        
        $this->alternateSlugs[] = $slug;
        
        return $this->getAlternateSlugs();
    }
    
    public function removeAlternateSlug($slug)
    {
        foreach ($this->alternateSlugs as $key => $alternateSlug) {
            if ($alternateSlug == $slug) {
                unset($this->alternateSlugs[$key]);
                return true;
            }
        }
        
        return null;
    }
    
    public function hasAlternateSlug($slug)
    {
        if (in_array($slug, $this->alternateSlugs)) {
            return true;
        }
        
        return false;
    }
    
    public function setAlternateSlugs($slugs)
    {
        $this->alternateSlugs = $slugs;
    }
    
    public function getOfficialName()
    {
        return $this->officialName;
    }
    
    public function setOfficialName($officialName)
    {
        $this->officialName = $officialName;
    }
    
    public function getShortName()
    {
        if ($this->shortName) {
            return $this->shortName;
        }
        
        return $this->name;
    }
    
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }
    
    public function getNickname()
    {
        return $this->nickname;
    }
    
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function setCity($city)
    {
        $this->city = (string) $city;
    }
    
    public function getState()
    {
        return $this->state;
    }
    
    public function setState($state)
    {
        $this->state = (string) $state;
    }
    
    public function getCityState()
    {
        if ($this->getCity() && $this->getState()) {
            return sprintf('%s, %s', $this->getCity(), $this->getState());
        } elseif ($this->getCity()) {
            return $this->getCity();
        } else {
            return $this->getState();
        }
        
        return null;
    }
    
    public function setCityState($cityState)
    {
        $location = explode(',', $cityState);
        
        $this->setCity(trim($location[0]));
        $this->setState(trim($location[1]));
    }
    
    public function getNcaaId()
    {
        return (int) $this->ncaaId;
    }
    
    public function setNcaaId($ncaaId)
    {
        $this->ncaaId = (int) $ncaaId;
    }
    
    public function getLahmanId()
    {
        return $this->lahmanId;
    }
    
    public function setLahmanId($lahmanId)
    {
        $this->lahmanId = $lahmanId;
    }
    
    public function getBoydsworldId()
    {
        return $this->boydsworldId;
    }
    
    public function setBoydsworldId($boydsworldId)
    {
        $this->boydsworldId = $boydsworldId;
    }
    
    public function getAlternateNcaaIds()
    {
        return $this->alternateNcaaIds;
    }
    
    public function addAlternateNcaaId($ncaaId)
    {
        if ($this->hasAlternateNcaaId($ncaaId)) {
            return true;
        }
        
        $this->alternateNcaaIds[] = $ncaaId;
        
        return $this->getAlternateNcaaIds();
    }
    
    public function removeAlternateNcaaId($ncaaId)
    {
        foreach ($this->alternateNcaaIds as $key => $alternateNcaaId) {
            if ($alternateNcaaId == $ncaaId) {
                unset($this->alternateNcaaIds[$key]);
                return true;
            }
        }
        
        return null;
    }
    
    public function hasAlternateNcaaId($ncaaId)
    {
        if (in_array($ncaaId, $this->alternateNcaaIds)) {
            return true;
        }
        
        return false;
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
    
    public function hasConference()
    {
        return (bool) $this->conference;
    }
        
    public function getConference()
    {
        $seasons = $this->getSeasons();
        
        if (count($seasons)) {
            foreach ($seasons as $season) {
                if ($season->hasConference()) {
                    return $season->getConference();
                }
            }
        }
        
        if ($this->hasConference()) {
            $results = $this->mongo
                ->get_dbref($this->conference);
                
            $this->load->model('conferenceRepository', '_conference');
                
            return $this->_conference->assign($results);
        }
        
        return null;
    }
    
    public function setConference($conference)
    {
        if (is_object($conference)) {
            $this->conference = $this->mongo
                ->create_dbref('conference', $conference->getId());
        } else {
            $this->conference = $conference;
        }
    }
    
    public function getRecordForBetween($start, $finish)
    {
        $range = $this->date->range($start, $finish);
        
        $this->load->model('gameRepository', '_game');
        
        return $this->_game->findAllForTeamBetweenDates($this, $range['start'], $range['finish']);
    }
    
    public function getRecordForWeek()
    {
        $range = $this->date->range('april 1st 2011', 'april 10th 2011');
        
        $this->load->model('gameRepository', '_game');
        
        return $this->_game->findAllForTeamBetweenDates($this, $range['start'], $range['finish']);
    }
    
    public function getRecordForMonth()
    {
        $range = $this->date->range();
        
        $this->load->model('gameRepository', '_game');
        
        return $this->_game->findAllForTeamBetweenDates($this, $range['start'], $range['finish']);
    }
    
    public function getRecordForSeason($year = null)
    {
        $range = $this->date->season($year);
        
        $this->load->model('gameRepository', '_game');
        
        return $this->_game->findAllForTeamBetweenDates($this, $range['start'], $range['finish']);
    }
    
    public function findAllUnverifiedForTeamByYear($year = null)
    {
        $this->load->model('gameRepository', '_game');
        
        return $this->_game->findAllUnverifiedForTeamByYear($this, $year);
    }
    
    public function hasSeasons()
    {
        return ($this->seasons) ? true : false;
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
        if ($this->seasons) {
            $temp = array();
            $this->load->model('seasonRepository', '_season');
            
            foreach ($this->seasons as $season) {
                $season = $this->_season->assign($season);
                $season->setTeam($this);
                $temp[] = $season;
            }
            
            usort($temp, array('Season', 'sortSeasons'));
            
            return $temp;
        }
        
        return array();
    }
    
    public function getSeason($year)
    {
        foreach ($this->seasons as $key => $season) {
            if ($season['year'] == $year) {
                $this->load->model('seasonRepository', '_season');
                $season = $this->_season->assign($season);
                $season->setTeam($this);
                return $season;
            }
        }
        
        return null;
    }
    
    public function addSeason($season, $overwrite = false)
    {        
        if ($overwrite) {
            $this->removeSeason($season->getYear(), false);
        } elseif ($this->hasSeason($season->getYear())) {
            return $this->seasons;
        }
        
        $season->unsetTeam();
        $season->unsetSource();
        
        $season = $season->toArray();
        
        $this->seasons = array_values($this->seasons);
        array_push($this->seasons, $season);
        
        return $this->seasons;
    }
    
    public function removeSeason($year, $includePlayers = false)
    {
        foreach ($this->seasons as $key => $temp) {
            if ($temp['year'] == $year) {
                if ($includePlayers) {
                    $this->getSeason($year)->removePlayers();
                }
                unset($this->seasons[$key]);
            }
        }
        
        return $this->seasons;
    }
    
    public function getMostRecentSeason()
    {
        $seasons = array_reverse($this->getSeasons());
        
        return array_pop($seasons);
    }
    
    public function getPlayers($year = null)
    {
        $players = array();
        
        foreach ($this->getSeasons() as $season) {
            if (!$year || $season->getYear() == $year) {
                foreach ($season->getPlayers() as $player) {
                    if (!array_key_exists((string) $player->getId(), $players)) {
                        $players[(string) $player->getId()] = $player;
                    }
                }
            }
        } 
        
        return $players;
    }
    
    public function getNumberOfGames()
    {
        $this->load->model('gamerepository', '_game');
        
        return $this->_game->getCountAllWithTeam($this);
    }
    
}