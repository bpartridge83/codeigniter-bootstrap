<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class ConferenceRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'conference';
    }
    
    public function findAll($sortBy = 'name')
    {
        $this->benchmark->query('FindAll: Conferences');
        $documents = parent::findAll($sortBy);
        $this->benchmark->query('FindAll: Conferences');
        
        return $documents;
    }
    
    public function findOneById($id)
    {
        $this->benchmark->query('FindOneById: Conference: '.$id);
        $document = parent::findOneById($id);
        $this->benchmark->query('FindOneById: Conference: '.$id);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneBySlug($slug)
    {
        $this->benchmark->query('FindOneBySlug: Conference: '.$slug);
        $document = parent::findOneBySlug($slug);
        $this->benchmark->query('FindOneBySlug: Conference: '.$slug);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function getOrCreateByName($name)
    {
        $slug = $this->slugify->simple($name);
        
        $document = $this->findOneBySlug($slug);
        
        if ($document) {
            return $document;
        }
                        
        $conference = new Conference();
        $conference->setName($name);
        $conference->setSlug($slug);
        $conference->save();
        
        return $conference;
    }
    
    public function findUniqueSeasonsWithConference($conference)
    {
        if (is_object($conference)) {
            $conference = $this->mongo
                ->create_dbref('conference', $conference->getId());
        }
        
        $results = $this->mongo
            ->command(array('distinct'=>'team', 'key'=>'seasons.year', 'query' => array('seasons.conference' => $conference)));
            
        return $results['values'];
    }
    
    public function findRankedPlayersWithConference($conference, $metric, $minimum = null, $limit = 10, $select = array())
    {
        if (is_object($conference)) {
            $conference = $this->mongo
                ->create_dbref('conference', $conference->getId());
        }

        $select = array_unique(array_merge($select, array('year', $metric, 'player')));
        
        $query = $this->mongo
            ->where(array(
                'source' => 'Smallball',
                'isPlayer' => true,
                'conference' => $conference,
            ))
            ->select($select);
        
        if ($minimum) {
            $query = $query
                ->where_gte($metric, $minimum);
        } else {
            $query = $query
                ->where_gte($metric, 1);
        }
            
        $results = $query
            ->order_by(array($metric=>-1))
            ->limit($limit)
            ->get('season');
            
        $seasons = array();
        
        $this->load->model('SeasonRepository', '_season');
            
        if ($results) {
            foreach ($results as $result) {
                $seasons[] = $this->_season->assign($result);
            }
            
            return $seasons;
        }
        
        return false;
    }
    
    public function findRankedPlayersByYearWithConference($conference, $year, $metric, $minimum = null, $limit = 10, $select = array())
    {
        if (is_object($conference)) {
            $conference = $this->mongo
                ->create_dbref('conference', $conference->getId());
        }

        $select = array_unique(array_merge($select, array('year', $metric, 'player')));
        
        $query = $this->mongo
            ->where(array(
                'source' => 'Smallball',
                'isPlayer' => true,
                'year' => $year,
                'conference' => $conference,
            ))
            ->select($select);
        
        if ($minimum) {
            $query = $query
                ->where_gte($metric, $minimum);
        } else {
            $query = $query
                ->where_gte($metric, 1);
        }
            
        $results = $query
            ->order_by(array($metric=>-1))
            ->limit($limit)
            ->get('season');
            
        $seasons = array();
        
        $this->load->model('SeasonRepository', '_season');
            
        if ($results) {
            foreach ($results as $result) {
                $seasons[] = $this->_season->assign($result);
            }
            
            return $seasons;
        }
        
        return false;
    }
        
    public function assign($document)
    {
        return parent::assign($document, new Conference());
    }
    
}