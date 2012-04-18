<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PlayerRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'player';
    }
    
    public function findAll($sortBy = 'lastName', $limit = null, $offset = null, $select = null)
    {
        $this->benchmark->query('FindAllPlayers:');
        $documents = parent::findAll($sortBy, $limit, $offset, $select);
        $this->benchmark->query('FindAllPlayers:');
        
        return $documents;
    }
    
    public function findOneById($id, $select = null)
    {
        $this->benchmark->query('FindOneById: Player: '.$id);
        $document = parent::findOneById($id, $select);
        $this->benchmark->query('FindOneById: Player: '.$id);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneBySlug($slug)
    {
        $this->benchmark->query('FindOneBySlug: Player: '.$slug);
        $document = parent::findOneBySlug($slug);
        $this->benchmark->query('FindOneBySlug: Player: '.$slug);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneByNcaaId($id)
    {
        $this->benchmark->query('FindOneByNcaaId: Player: '.$id);
        $results = $this->mongo
            ->or_where(array(
                'ncaaId' => (int) $id,
                'alternateNcaaIds' => (int) $id,
            ))
            ->get($this->collection);
        $this->benchmark->query('FindOneByNcaaId: Player: '.$id);
        
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;
    }
    
    public function findAllWithTeam($team)
    {
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $this->benchmark->query('FindAllWithTeam: Player');
        $results = $this->mongo
            ->where(array(
                'seasons.team' => $team,
            ))
            ->order_by(array('lastName'=>1))
            ->get($this->collection);
        $this->benchmark->query('FindAllWithTeam: Player');
        
        $players = array();
        
        if ($results) {
            foreach ($results as $result) {
                $players[] = $this->assign($result);
            }
            
            return $players;
        }
        
        return false;
    }
    
    public function findOneWithNameAndTeam($firstName, $lastName, $team)
    {
        if (is_object($team)) {
            $team = $this->mongo
                ->create_dbref('team', $team->getId());
        }
    
        $results = $this->mongo
            ->where(array(
                'lastName' => $lastName,
                'seasons.team' => $team,
            ))
            ->limit(1)
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return false;
    }
        
    public function assign($document)
    {
        return parent::assign($document, new Player());
    }
    
}