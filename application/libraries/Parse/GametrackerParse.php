<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GametrackerParse {
        
    protected $CI;
    protected $id;
    protected $html;
    protected $game;
    protected $innings;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('GameRepository', '_game');
        $this->CI->load->model('PlayerRepository', '_player');
        
        $this->CI->load->model('gameplayerbatting');
        $this->CI->load->model('gameplayerpitching');
    }
 
    public function getSummaryUrl($id)
    {
        return sprintf('http://origin.livestats.www.cstv.com/livestats/data/m-basebl/%s/summary.xml', $id);
    }
    
    public function getPlayByPlayUrl($id)
    {
        return sprintf('http://origin.livestats.www.cstv.com/livestats/data/m-basebl/1058032/play_by_play.xml', $id);
    }
    
    public function getStatsUrl($id)
    {
        return sprintf('http://origin.livestats.www.cstv.com/livestats/data/m-basebl/1058032/player_stats.xml', $id);
    }
    
    public function findOrCreateGame($id, $html)
    {
        $this->id = $id;
    
        $game = $this->CI->_game->findOneByGametrackerId($id);
        
        if (!$game) {
            print_r("Didn't find a game\n");
        
            $this->summary = $html;
            $this->summary = preg_replace('/\n+/', '', $this->summary);
            $this->summary = preg_replace('/\r+/', '', $this->summary);
            $this->summary = preg_replace('/\s\s+/', '', $this->summary);
            
            $this->getVenueFromSummary();
            $this->getTeamsFromSummary();
            
            $this->getUmpires();
            $this->getTimes();
            $this->getLocation();
            $this->getAttendance();
            
            $this->getPlayByPlay();
            
            print_r($this->getStatsUrl($this->id));
        }
    }
    
    public function getPlayByPlay()
    {
        $url = $this->getPlayByPlayUrl($this->id);
        
        $curl = new CURL();
        $curl->addSession($url);
        $page = $curl->exec();
        $curl->clear();
        
        $this->playByPlay = new SimpleXMLElement($page);
        
        return $this->playByPlay;
    }
    
    protected function getTeamsFromSummary()
    {
        preg_match_all('/<team(.*)?>(.*)?<\/team>/', $this->summary, $teams);
          
        $teams = $teams[0][0];
        $split = strpos($teams, 'am><te') + 3;
        
        $this->teams = array();
        $this->teams[0] = substr($teams, 0, $split);
        $this->teams[1] = substr($teams, $split);
        
        $this->teams[0] = simplexml_load_string($this->teams[0]);
        $this->teams[1] = simplexml_load_string($this->teams[1]);
        
        return $this->teams;
    }
    
    protected function getVenueFromSummary()
    {
        preg_match_all('/<venue(.*)?>(.*)?<\/venue>/', $this->summary, $venue);
        
        $venue = $venue[0][0];
        $venue = simplexml_load_string($venue);
        
        $this->venue = $venue;
        
        return $this->venue;
    }
    
    protected function getUmpires()
    {
        $this->umpires = array();
        
        foreach ($this->venue->umpires->attributes() as $position => $umpire) {
            $this->umpires[$position] = (string) $umpire;
        } 
        
        return $this->umpires;
    }
    
    protected function getTimes()
    {
        $this->start = (string) $this->venue->attributes()->start;
        $this->duration = (string) $this->venue->attributes()->duration;
        $this->date = strtotime((string) $this->venue->attributes()->date);
    }
    
    protected function getLocation()
    {
        $this->location = (string) $this->venue->attributes()->location;
        $this->stadium = (string) $this->venue->attributes()->stadium;
    }
    
    protected function getAttendance()
    {
        $this->attendance = (string) $this->venue->attributes()->attend;
        
        if (!$this->attendance) {
            unset($this->attendance);
        }
    }
    
}