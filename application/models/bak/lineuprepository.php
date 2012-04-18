<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class LineupRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'lineup';
    }
    
    public function findAll($sortBy = 'name', $limit = null, $offset = null, $select = null)
    {
        $this->benchmark->query('FindAllLeagues:');
        $documents = parent::findAll($sortBy, $limit, $offset, $select);
        $this->benchmark->query('FindAllLeagues:');
        
        return $documents;
    }
    
    public function count()
    {
        $this->benchmark->query('CountAllLeagues:');
        $count = parent::count();
        $this->benchmark->query('CountAllLeagues:');
        
        return $count;
    }
    
    public function findOneById($id, $select = null)
    {
        $this->benchmark->query('FindOneById: Team: '.$id);
        $document = parent::findOneById($id, $select);
        $this->benchmark->query('FindOneById: Team: '.$id);
        
        if (!$document) {
            return null;
        }
        
        return $document;        
    }
    
    public function findOneBySlug($slug)
    {
        $this->benchmark->query('FindOneBySlug: Team: '.$slug);
        $document = parent::findOneBySlug($slug);
        $this->benchmark->query('FindOneBySlug: Team: '.$slug);
        
        if ($document) {
            return $document;
        }
        
        $results = $this->mongo
            ->where(array(
                'alternateSlugs' => $slug
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
    
    public function findOneByOfficialName($officialName)
    {        
        $results = $this->mongo
            ->where(array(
                'officialName' => $officialName
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;        
    }
            
    public function createByName($name)
    {
        print_r('['.$name.']');
        
        $document = new Team();
        $document->setName($name);
        $document->setSlug();
        $document->save();
        
        $document = $this->findOneBySlug($document->getSlug());
                
        return $document;
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Team());
    }
    
}