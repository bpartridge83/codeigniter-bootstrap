<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Game {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('GameRepository', '_game');
        $this->CI->load->model('TeamRepository', '_team');
        $this->CI->load->model('PlayerRepository', '_player');
    }

    public function load()
    {
        //1055918
    
        $this->clear();
        
        $game = new Game();
        $game->setGametrackerId(1055918);
        
        $homeTeam = new GameTeam();
        
        $team = $this->CI->_team->findOneBySlug('miami-fl');
        $homeTeam->setTeam($team);
        
        $homeTeam->setScore(4);
        $homeTeam->setSide('home');

        $homeLineup = new Lineup();
        
        // <player name="Alfredo Rodriguez" shortname="Rodriguez, A" code="" uni="2" gp="1" gs="1" spot="1" pos="ss" atpos="ss" bats="R" throws="R" class="SR" bioid="395839" bioxml="http://grfx.cstv.com/bios/00/39/58/395839.xml">
        
        $player = $this->CI->_player->findOneBySlug('alfredo-rodriguez');
        
        $batter = new GameBatter();
        $batter->setPlayer($player);
        $batter->setNumber(2);
        $batter->setOrder(1);
        $batter->setPosition('SS');
        $batter->setAtBats(3);
        $batter->setRuns(0);
        $batter->setRunsBattedIn(1);
        $batter->setWalks(1);
        $batter->setHitByPitch(1);
        $batter->setGroundBalls(1);
        $batter->setFlyBalls(2);
        $batter->save();
        
        $homeLineup->addPlayer($batter);
        
        $homeTeam->setLineup($homeLineup);
        
        $game->setHome($homeTeam);
        
        print_r($game->getHome()->getLineup()->getPlayers());
        die();
                
        $division->save();
        
        print_r(sprintf("Created Division: %s (%s)\n", $division->getName(), $division->getId()));
    }

    public function clear()
    {
        //$this->CI->mongo->drop_collection('smallball', 'game');
        $this->CI->mongo->drop_collection('smallball', 'gamebatter');
    }

}