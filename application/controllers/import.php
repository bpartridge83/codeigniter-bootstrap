<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Parse/BoydsworldParse');

        //$this->load->library('Parse/LahmanParse');
    }
    
    public function updateTeamReferences()
    {
        $teams = $this->mongo
            ->where(array(
                'conference' => array(
                    '$exists' => true
                )
            ))
            ->select(array(
                '_id', 'conference'
            ))
            ->get('team');
        
        
        print_r(count($teams));
        
        foreach ($teams as $team) {
            if (is_array($team['conference'])) {
                $team['conference'] = (string) $team['conference']['$id'];
                
                $_id = $team['_id'];
                
                unset($team['_id']);
                
                $result = $this->mongo
                    ->where(array('_id'=>$_id))
                    ->set($team)
                    ->update('team');
            }
        }
        
        print_r("\n\n");
            
    }
    
    public function updateGameReferences()
    {
        $games = $this->mongo
            ->select(array(
                '_id', 'awayTeam', 'homeTeam', 'page'
            ))
            ->get('game');
        
        print_r(count($games));
        
        foreach ($games as $game) {
            if (is_array($game['awayTeam']) && is_array($game['homeTeam']) && is_array($game['page'])) {
                $game['awayTeam'] = (string) $game['awayTeam']['$id'];
                $game['homeTeam'] = (string) $game['homeTeam']['$id'];
                $game['page'] = (string) $game['page']['$id'];
                
                $_id = $game['_id'];
                
                unset($game['_id']);

                $result = $this->mongo
                    ->where(array('_id'=>$_id))
                    ->set($game)
                    ->update('game');
            }
        }
        
        print_r("\n\n");
            
    }
    
    public function updateSeasonReferences()
    {
        $seasons = $this->mongo
            ->select(array(
                '_id', 'player', 'players', 'team', 'conference'
            ))
            ->get('season');
        
        print_r(count($seasons));
        
        foreach ($seasons as $season) {
            //if (is_array($season['player']) && is_array($season['players']) && is_array($season['team'])) {
            
            if (isset($season['player']) && is_array($season['player'])) {
                $season['player'] = (string) $season['player']['$id'];
            }
            
            if (isset($season['team']) && is_array($season['team'])) {
                $season['team'] = (string) $season['team']['$id'];
            }
            
            if (isset($season['conference']) && is_array($season['conference'])) {
                $season['conference'] = (string) $season['conference']['$id'];
            }
            
            if (isset($season['players']) && is_array($season['players'])) {
                $players = $season['players'];
                $season['players'] = array();
            
                foreach ($players as $player) {
                    $season['players'][] = (string) $player['$id'];
                }
            }
            
            $_id = $season['_id'];
            
            unset($season['_id']);
            
            $result = $this->mongo
                ->where(array('_id'=>$_id))
                ->set($season)
                ->update('season');
        }
        
        print_r("\n\n");
            
    }
    
    public function updateSeasons()
    {
        $this->load->model('SeasonRepository', '_season');
        $seasons = $this->_season->findAllOfficial();
        
        foreach ($seasons as $season) {
            print_r(sprintf("Saving Season: %s, %s\n", $season->getTeam()->getName(), $season->getYear()));
            $season->save();
        }
    }
    
    public function getPlayerImage()
    {
        $this->load->model('playerRepository', '_player');
        
        $player = $this->_player->findOneBySlug('ben-thomas');
        
        print_r($player);
        
        $this->load->library('image');
        
        $this->image->get();
    }

    public function lahmanSchools()
    {
        return $this->lahmanparse->importSchools();
    }

    public function getBoydsworldPlayer($id)
    {
        $this->load->model('PlayerRepository', '_player');
    
        $player = $this->_player->findOneByNcaaId($id);
    
        return $this->boydsworldparse->getPlayer($player);
    }
    
    public function boydsworld($year = null, $official = false)
    {
        return $this->boydsworldparse->importAll($year, $official);
    }
    
    public function boydsworldHitters($year = null, $official = false)
    {
        return $this->boydsworldparse->importHitters($year, $official);
    }
    
    public function boydsworldTeamHitting($year = null, $official = false)
    {
        return $this->boydsworldparse->importTeamHitting($year, $official);
    }
    
    public function boydsworldPitchers($year = null, $official = false)
    {
        return $this->boydsworldparse->importPitchers($year, $official);
    }
    
    public function boydsworldTeamPitching($year = null, $official = false)
    {
        return $this->boydsworldparse->importTeamPitching($year, $official);
    }
    
    public function checkBoydsworldTeams()
    {
        return $this->boydsworldparse->checkTeams();
    }

}