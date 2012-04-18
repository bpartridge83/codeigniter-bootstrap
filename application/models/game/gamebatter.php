<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GameBatter extends GamePlayer {

    protected $atBats;
    protected $hitByPitch;
    protected $runsBattedIn;
    protected $groundBalls;
    protected $flyBalls;
    protected $position;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Game/GameBatterRepository', '_gamebatter');
    }
    
    public function getAtBats()
    {
        return $this->atBats;
    }

    public function setAtBats($atBats)
    {
        $this->atBats = (int) $atBats;
    }
    
    public function getHitByPitch()
    {
        return $this->hitByPitch;
    }

    public function setHitByPitch($hitByPitch)
    {
        $this->hitByPitch = $hitByPitch;
    }
    
    public function getRunsBattedIn()
    {
        return $this->runsBattedIn;
    }

    public function setRunsBattedIn($runsBattedIn)
    {
        $this->runsBattedIn = $runsBattedIn;
    }
    
    public function getGroundBalls()
    {
        return $this->groundBalls;
    }

    public function setGroundBalls($groundBalls)
    {
        $this->groundBalls = $groundBalls;
    }
    
    public function getFlyBalls()
    {
        return $this->flyBalls;
    }

    public function setFlyBalls($flyBalls)
    {
        $this->flyBalls = $flyBalls;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function setPosition($position)
    {
        $position = $this->cleanPosition($position);
        
        if (!array_key_exists($position, $this->getAvailablePositions())) {
            return false;
        }
        
        $player = $this->getPlayer();
        $player->addPosition($position);
        $player->save();
        
        print_r($player);
        
        $this->position = $position;
    }

    protected function cleanPosition($position)
    {
        if (!array_key_exists($position, $this->getAvailablePositions())) {
            foreach ($this->getAvailablePositions() as $key => $value) {
                if ($value == $position) {
                    $position = $key;
                }
            }
        }
        
        return $position;
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