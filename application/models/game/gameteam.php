<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GameTeam extends Document {

    protected $team;
    protected $score;
    protected $side;
    protected $lineup;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('GameRepository', '_game');
    }
    
    public function __toString()
    {
        return $this->getTeam();
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
    
    public function getLineup()
    {
        $this->load->model('Game/LineupRepository', '_lineup');
        
        return $this->_lineup->assign($this->lineup);
    }
    
    public function setLineup($lineup)
    {
        if (is_object($lineup)) {
            $lineup = $lineup->toArray();
        }
    
        $this->lineup = $lineup;
    }
    
    public function getScore()
    {
        return $this->score;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }
    
    public function getSide()
    {
        return $this->side;
    }

    public function setSide($side)
    {
        $this->side = $side;
    }
    
}