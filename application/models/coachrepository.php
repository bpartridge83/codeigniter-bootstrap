<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class CoachRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'coach';
    }
    
    public function findAll($sortBy = 'lastName')
    {
        $this->benchmark->query('FindAllCoaches:');
        $documents = parent::findAll($sortBy);
        $this->benchmark->query('FindAllCoaches:');
        
        return $documents;
    }
    
    public function findOneById($id)
    {
        $this->benchmark->query('FindOneById: Coach: '.$id);
        $document = parent::findOneById($id);
        $this->benchmark->query('FindOneById: Coach: '.$id);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneByNcaaId($id)
    {
        $this->benchmark->query('FindOneByNcaaId: Coach: '.$id);
        $results = $this->mongo
            ->where(array(
                'ncaaId' => $id,
            ))
            ->get($this->collection);
        $this->benchmark->query('FindOneByNcaaId: Coach: '.$id);
        
        if (!$results) {
            return false;
        }
        
        return $this->assign($results[0]);        
    }
    
    public function findOneBySlug($slug)
    {
        $this->benchmark->query('FindOneBySlug: Coach: '.$slug);
        $document = parent::findOneBySlug($slug);
        $this->benchmark->query('FindOneBySlug: Coach: '.$slug);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
        
    public function assign($document)
    {
        return parent::assign($document, new Coach());
    }
    
}