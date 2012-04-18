<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Play extends Document {

    protected $raw;
    protected $game;
    protected $datetime;

    protected $batter;
    protected $pitcher;
    
    protected $pitches = array();
    
    protected $baserunners;
    
    protected $action;
    
    // baserunners
    // pitches
    // wild pitches
    // balks
    // stolen bases
    // pickoffs
    
    // action
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getRaw()
    {
        return $this->raw;
    }

    public function setRaw($raw)
    {
        $this->raw = $raw;
    }
    
    public function getGame()
    {
        $results = $this->mongo
            ->get_dbref($this->game);
            
        $this->load->model('gameRepository', '_game');
            
        return $this->_game->assign($results);
    }
    
    public function setGame($game)
    {
        if (is_object($game)) {
            $game = $this->mongo
                ->create_dbref('game', $game->getId());
        }
    
        $this->game = $game;
    }
    
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
    
    public function getBatter()
    {
        if ($this->batter) {
            $results = $this->mongo
                ->get_dbref($this->batter);
            
            $this->load->model('playerrepository', '_player');
            
            return $this->_player->assign($results);
        }
    
        return false;
    }

    public function setBatter($batter)
    {
        if (is_object($batter)) {
            $batter = $this->mongo
                ->create_dbref('player', $batter->getId());
        }
    
        $this->batter = $batter;
    }
    
    public function getPitches()
    {
        $this->load->model('PitchRepository', '_pitch');
    
        $response = array();
    
        foreach ($this->pitches as $pitch)
        {
            $pitch = $this->_pitch->assign($pitch);
            array_push($response, $pitch);
        }
    
        return $response;
    }
    
    public function setPitches($pitches)
    {
        $this->pitches = $pitches;
    }
    
    public function addPitch($pitch)
    {
        $pitch = $pitch->toArray();
    
        array_push($this->pitches, $pitch);
    }

}