<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GamePlayerBatting extends Document {

    protected $player;
    protected $firstName;
    protected $lastName;
    
    protected $order;
    protected $number;
    protected $started;
    protected $position;
    
    protected $atBats;
    protected $runs;
    protected $hits;
    protected $runsBattedIn;
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
    
    public function getOrder()
    {
        return (int) $this->order;
    }
    
    public function setOrder($order)
    {
        $this->order = $order;
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
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    public function getAtBats()
    {
        return (int) $this->atBats;
    }
    
    public function setAtBats($atBats)
    {
        $this->atBats = (int) $atBats;
    }
    
    public function getRuns()
    {
        return (int) $this->runs;
    }
    
    public function setRuns($runs)
    {
        $this->runs = (int) $runs;
    }
    
    public function getRunsBattedIn()
    {
        return (int) $this->runsBattedIn;
    }
    
    public function setRunsBattedIn($runsBattedIn)
    {
        $this->runsBattedIn = (int) $runsBattedIn;
    }

    public function getHits()
    {
        return (int) $this->hits;
    }
    
    public function setHits($hits)
    {
        $this->hits = (int) $hits;
    }
    
    public function getWalks()
    {
        return (int) $this->walks;
    }
    
    public function setWalks($walks)
    {
        $this->walks = (int) $walks;
    }
    
    public function getStrikeouts()
    {
        return (int) $this->strikeouts;
    }
    
    public function setStrikeouts($strikeouts)
    {
        $this->strikeouts = (int) $strikeouts;
    }
        
    protected function getAvailablePositions()
    {
        return array(
            '1B' => 'First Base',
            '2B' => 'Second Base',
            '3B' => 'Third Base',
            'SS' => 'Shortstop',
            'IF' => 'Infield',
            'P' => 'Pitcher',
            'RP' => 'Relief Pitcher',
            'CL' => 'Closer',
            'C' => 'Catcher',
            'LF' => 'Left Field',
            'CF' => 'Center Field',
            'RF' => 'Right Field',
            'OF' => 'Outfield',
        );
    }
        
}