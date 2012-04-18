<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GamePlayerPitching extends Document {

    protected $player;
    protected $firstName;
    protected $lastName;
    
    protected $number;
    protected $started;
    
    protected $win;
    protected $loss;
    protected $save;
    
    protected $inningsPitched;
    protected $hits;
    protected $runs;
    protected $earnedRuns;
    protected $walks;
    protected $strikeouts;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPlayer()
    {
        if (!$this->player) {
            return null;
        }
        
        if (gettype($this->player) == 'string') {
            return $this->player;
        }
        
        $results = $this->mongo
            ->get_dbref($this->player);
            
        $this->load->model('playerRepository', '_player');
            
        return $this->_player->assign($results);
    }
    
    public function setPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        $this->player = $player;
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
    
    public function getWin()
    {
        return (bool) $this->win;
    }
    
    public function setWin($win)
    {
        $this->win = (bool) $win;
    }
    
    public function getLoss()
    {
        return (bool) $this->loss;
    }
    
    public function setLoss($loss)
    {
        $this->loss = (bool) $loss;
    }
    
    public function getSave()
    {
        return (bool) $this->save;
    }
    
    public function setSave($save)
    {
        $this->save = (bool) $save;
    }
    
    public function getNumber()
    {
        return (int) $this->number;
    }
    
    public function setNumber($number)
    {
        $number = (int) $number;
        
        if ($number) {
            $this->number = $number;
        }
    }
    
    public function getStarted()
    {
        return (bool) $this->started;
    }
    
    public function setStarted($started)
    {
        $this->started = (bool) $started;
    }
    
    public function getInningsPitched()
    {
        return $this->inningsPitched;
    }
    
    public function setInningsPitched($inningsPitched)
    {
        $this->inningsPitched = $inningsPitched;
    }
            
}