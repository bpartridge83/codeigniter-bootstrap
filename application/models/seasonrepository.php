<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class SeasonRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'season';
    }
    
    public function removeAllByTypeAndPlayerAndYear($source, $player, $year)
    {
        $name = $player->getName();
        
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }

        $this->benchmark->query('removeAllByTypeAndPlayerAndYear: '.$source.' '.$name.' '.$year);
        $results = $this->mongo
            ->where(array(
                'source' => (string) $source,
                'player' => $player,
                'year' => (int) $year,
            ))
            ->delete($this->collection);
        $this->benchmark->query('removeAllByTypeAndPlayerAndYear: '.$source.' '.$name.' '.$year);
        
        if ($results) {
            return $results;
        }
        
        return null;
    }
    
    public function removeAllByTypeAndTeamAndYear($source, $team, $year)
    {
        $name = $team->getName();
        
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->benchmark->query('removeAllByTypeAndTeamAndYear: '.$source.' '.$name.' '.$year);
        $results = $this->mongo
            ->where(array(
                'source' => (string) $source,
                'team' => $team,
                'player' => null,
                'year' => (int) $year,
            ))
            ->delete($this->collection);
        $this->benchmark->query('removeAllByTypeAndTeamAndYear: '.$source.' '.$name.' '.$year);
        
        if ($results) {
            return $results;
        }
        
        return null;
    }
    
    public function findOneBySourceAndTeamAndYear($source, $team, $year)
    {
        $name = $team->getName();
        
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->benchmark->query('findOneByTypeAndTeamAndYear: '.$source.' '.$name.' '.$year);
        $result = $this->mongo
            ->where(array(
                'source' => (string) $source,
                'isTeam' => true,
                'team' => $team,
                'year' => (int) $year,
            ))
            ->limit(1)
            ->get($this->collection);
        $this->benchmark->query('findOneByTypeAndTeamAndYear: '.$source.' '.$name.' '.$year);
        
        if ($result) {
            return $this->assign($result[0]);
        }
        
        return null;
    }

    public function findOneBySourceAndPlayerAndYear($source, $player, $year)
    {    
        $name = $player->getName();
    
        if (is_object($player)) {
            $player = $this->mongo
                ->create_dbref('player', $player->getId());
        }
    
        $this->benchmark->query('findOneBySourceAndPlayerAndYear: '.$source.' '.$name.' '.$year);
        $result = $this->mongo
            ->where(array(
                'source' => (string) 'Smallball',
                'isPlayer' => true,
                'player' => $player,
                'year' => (int) $year,
            ))
            ->limit(1)
            ->get($this->collection);
        $this->benchmark->query('findOneBySourceAndPlayerAndYear: '.$source.' '.$name.' '.$year);
        
        if ($result) {
            return $this->assign($result[0]);
        }
        
        return null;
    }
    
    public function findAllWithTeam($team)
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
    
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        $results = $this->mongo
            ->where(array(
                'isTeam' => true,
                'team' => $ref,
            ))
            ->get($this->collection);
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        
        $seasons = array();
        
        foreach ($results as $season) {
            $season = $this->assign($season);
            array_push($seasons, $season);
        }
    
        return $seasons;        
    }
    
    public function findAllWithAnyTeam()
    {
        $this->benchmark->query('FindAllWithAnyTeam');
        $results = $this->mongo
            ->where(array(
                'year' => array(
                    'exists'
                )
            ))
            ->get($this->collection);
        $this->benchmark->query('FindAllWithAnyTeam');
        
        $seasons = array();
        
        foreach ($results as $season) {
            $season = $this->assign($season);
            array_push($seasons, $season);
        }
    
        return $seasons;        
    }
    
    public function findAllWithoutConference($limit = 500)
    {
        $results = $this->mongo
            ->where(array(
                'conference' => null,
                'source' => 'Smallball'
            ))
            ->limit($limit)
            ->select(array('_id', 'team', 'player', 'players', 'source'))
            ->get($this->collection);
        
        $seasons = array();
        
        foreach ($results as $season) {
            $season = $this->assign($season);
            array_push($seasons, $season);
        }
    
        return $seasons;
    }

    public function findAllTeamsByDivision($division, $year = null, $limit = 50, $offset = 0)
    {
        $this->benchmark->query('FindAllTeamsByDivision: '.$division);
        
        $results = $this->mongo
            ->where(array(
                'division' => $division
            ))
            ->select(array(
                'team', 'year'
            ))
            ->limit($limit)
            ->offset($offset)
            ->get($this->collection);
        
        $this->benchmark->query('FindAllTeamsByDivision: '.$division);
        
        $seasons = array();
        
        foreach ($results as $season) {
            $season = $this->assign($season);
            array_push($seasons, $season);
        }
    
        return $seasons;
    }

    public function findOneByTeamAndYear($team, $year)
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
    
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        $result = $this->mongo
            ->where(array(
                'isTeam' => true,
                'team' => $ref,
                'year' => $year
            ))
            ->limit(1)
            ->get($this->collection);
        $this->benchmark->query('FindAllWithTeam: '.$team->getName());
        
        if ($result) {
            return $this->assign($result);
        }
    
        return null;        
    }
    
    public function findOneOfficialByTeamAndYear($team, $year)
    {
        $ref = $this->mongo
            ->create_dbref('team', $team->getId());
        
        $this->benchmark->query('FindOneOfficialByTeamAndYear: '.$team->getName());
        $result = $this->mongo
            ->where(array(
                'source' => 'Smallball',
                'team' => $ref,
                'isTeam' => true,
                'year' => (int) $year,
            ))
            ->limit(1)
            ->get($this->collection);
        $this->benchmark->query('FindOneOfficialByTeamAndYear: '.$team->getName());
        
        if ($result) {
            return $this->assign($result[0]);
        }
    
        return null;        
    }
    
    public function findOneOfficialByPlayerAndYear($player, $year)
    {
        $ref = $this->mongo
            ->create_dbref('player', $player->getId());
        
        $this->benchmark->query('FindOneOfficialByPlayerAndYear: '.$player->getName());
        $result = $this->mongo
            ->where(array(
                'source' => 'Smallball',
                'player' => $ref,
                'isPlayer' => true,
                'year' => (int) $year,
            ))
            ->limit(1)
            ->get($this->collection);
        $this->benchmark->query('FindOneOfficialByPlayerAndYear: '.$player->getName());
        
        if ($result) {
            return $this->assign($result[0]);
        }
    
        return null;        
    }
    
    public function findAllOfficial()
    {
        $results = $this->mongo
            ->where(array(
                'source' => 'Smallball',
            ))
            ->get($this->collection);
        
        $seasons = array();
        
        foreach ($results as $season) {
            $season = $this->assign($season);
            array_push($seasons, $season);
        }
    
        return $seasons;       
    }
    
    public function assign($document, $object = null)
    {
        if (!$object) {
            $object = new Season();
        }
        
        return parent::assign($document, $object);
    }
    
}