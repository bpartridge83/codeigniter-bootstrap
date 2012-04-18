<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class LineupPlayer extends Document {

    protected $player;
    protected $position;
    protected $order;
    
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
    
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }
    
}