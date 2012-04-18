<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Inning extends Document {

    protected $level;
    protected $league;
    protected $division;
    protected $conference;
    protected $game;
    protected $datetime;
    protected $team;
    protected $number;
    protected $side;
    protected $raw;
    protected $runs;
    protected $hits;
    protected $errors;
    protected $leftOnBase;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('teamRepository', '_team');
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
        return (string) $this->league;
    }
    
    public function setLeague($league)
    {
        $this->league = (string) $league;
    }
    
    public function getDivision()
    {
        return (string) $this->division;
    }
    
    public function setDivision($division)
    {
        $this->division = (string) $division;
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
        
    public function getTeam()
    {
        $results = $this->mongo
            ->get_dbref($this->team);
            
        $this->load->model('teamRepository', '_team');
            
        return $this->_team->assign($results);
    }
    
    public function setTeam($team)
    {
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->team = $team;
    }
    
    public function getNumber()
    {
        return $this->number;
    }
    
    public function setNumber($number)
    {
        $this->number = $number;
    }
    
    public function getSide()
    {
        return $this->side;
    }
    
    public function setSide($side)
    {
        $this->side = $side;
    }
    
    public function getHalf()
    {
        return ($this->getSide() == 'away') ? 'top' : 'bottom';
    }
    
    public function getRaw()
    {
        return $this->raw;
    }
    
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }
    
    public function getRuns()
    {
        return $this->runs;
    }
    
    public function setRuns($runs)
    {
        $this->runs = $runs;
    }
    
    public function getHits()
    {
        return $this->hits;
    }
    
    public function setHits($hits)
    {
        $this->hits = $hits;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
    
    public function getLeftOnBase()
    {
        return $this->leftOnBase;
    }
    
    public function setLeftOnBase($leftOnBase)
    {
        $this->leftOnBase = $leftOnBase;
    }

}