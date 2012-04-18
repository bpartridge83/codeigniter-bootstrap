<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GamePlayer extends Document {

    protected $player;
    protected $team;
    protected $order;
    protected $number;
    protected $started;
    protected $hits;
    protected $runs;
    protected $walks;
    protected $strikeouts;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPlayer()
    {
        if (is_array($this->player)) {
            $results = $this->mongo
                ->get_dbref($this->player);
                
            $this->load->model('PlayerRepository', '_player');
                
            return $this->_player->assign($results);
        }
        
        return $this->player;
    }
    
    public function setPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        $this->player = $player;
    }

    public function getTeam()
    {
        if (is_object($this->team)) {
            $results = $this->mongo
                ->get_dbref($this->team);
                
            $this->load->model('TeamRepository', '_team');
                
            return $this->_team->assign($results);
        }
        
        return $this->team;
    }
    
    public function setTeam($team)
    {
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->team = $team;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }
    
    public function getStarted()
    {
        return (bool) $this->started;
    }

    public function setStarted($started)
    {
        $this->started = (bool) $started;
    }
    
    public function getHits()
    {
        return $this->hits;
    }

    public function setHits($hits)
    {
        $this->hits = (int) $hits;
    }
    
    public function getWalks()
    {
        return $this->walks;
    }

    public function setWalks($walks)
    {
        $this->walks = (int) $walks;
    }
    
    public function getRuns()
    {
        return $this->runs;
    }

    public function setRuns($runs)
    {
        $this->runs = (int) $runs;
    }
    
    public function getStrikeouts()
    {
        return $this->strikeouts;
    }

    public function setStrikeouts($strikeouts)
    {
        $this->strikeouts = (int) $strikeouts;
    }
    
}