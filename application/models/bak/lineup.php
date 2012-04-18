<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Lineup extends Document {

    protected $players = array();
    protected $team;
    
    protected $level;
    protected $league;
    protected $division;
    protected $conference;

    public function __construct()
    {
        parent::__construct();
        
        /*
        $player = array(
            'ref' => new Player()
            'position' => DH, P, C, 1B, 2B, etc…
            'order' => 1-9+
        );
        */
    }
    
    public function getPlayers()
    {
        $response = array();
        
        if ($this->players) {
            $players = $this->players;
        
            $this->load->model('PlayerRepository', '_player');
            
            foreach ($players as $key => $player) {
                $player = $this->mongo
                    ->get_dbref($player['ref']);
                
                $players[$key]['player'] = $this->_player->assign($player);
                unset($players[$key]['ref']);
            }
            
            usort($players, array('Lineup', 'sortLineup'));
            
            return $players;
        }
        
        return null;
    }
    
    public function hasPlayer($player)
    {
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }

        foreach ($this->players as $existing) {
            if ($existing['ref'] == $player) {
                return true;
            }
        }
        
        return false;
    }
    
    public function addPlayer($player, $position, $order)
    {
        if ($this->hasPlayer($player)) {
            return true;
        }
    
        if (is_object($player) and $player->getId()) {
            $ref = $this->mongo
                ->create_dbref('player', $player->getId());
                
            $player = array(
                'ref' => $ref,
                'position' => $position,
                'order' => $order
            );
            
            array_push($this->players, $player);
        } else {
            return false;
        }
    
        return $this->getPlayers();
    }
    
    public function replacePlayer($out, $in)
    {
        if (!$this->hasPlayer($out)) {
            return false;
        }
        
        $out = $this->mongo
            ->create_dbref('player', $out->getId());
            
        foreach ($this->players as $key => $player) {
            if ($player['ref'] == $out) {
                $position = $player['position'];
                $order = $player['order'];
                unset($this->players[$key]);
                
                $this->addPlayer($in, $position, $order);
            }
        }
    }
    
    public function getFieldingPosition($position)
    {
        if (is_numeric($position)) {
            $position = $this->fieldingPositions[$position];
        }
        
        foreach ($this->players as $player) {
            if ($player['position'] == $position) {
                $player['player'] = $this->mongo
                    ->get_dbref($player['ref']);
                unset($player['ref']);
            
                return $player;
            }
        }
    }
    
    public function setFieldingPosition($player, $position)
    {
        $ref = $this->mongo
            ->create_dbref('player', $player->getId());
    
        foreach ($this->players as $key => $player) {
            if ($player['ref'] == $ref) {
                $this->players[$key]['position'] = $position;
            }
        }
    }
    
    public function getBattingOrderPosition($position)
    {
        foreach ($this->players as $player) {
            if ($player['order'] == $position) {
                $player['player'] = $this->mongo
                    ->get_dbref($player['ref']);
                unset($player['ref']);
            
                return $player;
            }
        }
    }
        
    public function sortLineup($m, $n) {
        if ($m['order'] == $n['order']) {
            return 0;
        }
    
        return ($m['order'] < $n['order']) ? -1 : 1;
    }
        
    private $fieldingPositions = array(
        '1' => 'P',
        '2' => 'C',
        '3' => '1B',
        '4' => '2B',
        '5' => '3B',
        '6' => 'SS',
        '7' => 'LF',
        '8' => 'CF',
        '9' => 'RF'
    );
}