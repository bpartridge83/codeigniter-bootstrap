<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PlayerList extends Document {

    protected $players = array();
    protected $playerType;

    public function __construct()
    {
        parent::__construct();
        
        $this->playerType = 'GameBatter';
    }
    
    public function setPlayerType($type)
    {
        $this->playerType = $type;
    }
    
    public function getPlayers()
    {
        if (!$this->players) {
            return null;
        }
    
        $players = array();
        
        $repository = sprintf('_%s', strtolower($this->playerType));
        
        $this->load->model(sprintf('Game/%sRepository', $this->playerType), $repository);
    
        foreach ($this->players as $player) {
            $player = $this->{$repository}->assign($player);
            $players[] = $player;
        }
    
        return $players;
    }

    public function hasPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        foreach ($this->players as $existing) {
            if ($existing == $player) {
                return true;
            }
        }
        
        return false;
    }

    public function addPlayer($player)
    {
        if ($this->hasPlayer($player->getPlayer())) {
            return true;
        }
    
        $player = $player->toArray();
        
        array_push($this->players, $player);
    
        return $this->getPlayers();
    }

    
}