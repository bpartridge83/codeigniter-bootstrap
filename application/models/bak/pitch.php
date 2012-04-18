<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Pitch extends Document {

    protected $datetime;
    protected $game;
    protected $inning;
    protected $inningNum;
    protected $ball;
    protected $strike;
    protected $foul;
    
    public function __construct()
    {
        parent::__construct();
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
    
    public function getBall()
    {
        return (bool) $this->ball;
    }

    public function setBall($ball)
    {
        $this->ball = (bool) $ball;
    }
    
    public function getStrike()
    {
        return (bool) $this->strike;
    }

    public function setStrike($strike)
    {
        $this->strike = (bool) $strike;
    }
    
    public function getFoul()
    {
        return (bool) $this->foul;
    }

    public function setFoul($foul)
    {
        $this->foul = (bool) $foul;
    }

}