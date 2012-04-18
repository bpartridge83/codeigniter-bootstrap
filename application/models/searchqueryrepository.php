<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class SearchQueryRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'search';
    }
    
    public function findAll()
    {
        $results = $this->mongo
            ->order_by(array('datetime' => -1))    
            ->get($this->collection);
            
        if ($results) {
            $documents = array();
            
            foreach ($results as $document) {
                $documents[$document['_id']->__toString()] = $this->assign($document);
            }
            
            return $documents;
        }
        
        return null;
    }
    
    public function findRecent($successful = true, $limit = 30)
    {
        $results = $this->mongo
            ->where(array(
                'success' => true
            ))
            ->order_by(array('datetime' => -1))    
            ->limit($limit)
            ->get($this->collection);
        
        $terms = array();
        $paths = array();
        
        foreach ($results as $key => $result) {
            $terms_serialized = serialize($result['terms']);
            if (in_array($terms_serialized, $terms)) {
                unset($results[$key]);
            } else {
                array_push($terms, $terms_serialized);
                
                if (in_array($result['path'], $paths)) {
                    unset($results[$key]);
                } else {
                    array_push($paths, $result['path']);
                }
            }
        }
            
        if ($results) {
            $documents = array();
            
            foreach ($results as $document) {
                $documents[$document['_id']->__toString()] = $this->assign($document);
            }
            
            return $documents;
        }
        
        return null;      
    }
            
    public function assign($document)
    {
        return parent::assign($document, new SearchQuery());
    }
    
}