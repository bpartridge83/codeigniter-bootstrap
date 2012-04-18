<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class DocumentRepository extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function findAll($sortBy = 'name', $limit = null, $offset = null, $select = null)
    {
        if ($sortBy) {
            $query = $this->mongo
                ->order_by(array($sortBy => 1));
        } else {
            $query = $this->mongo;
        }
        
        if ($select) {
            $query = $query
                ->select($select);
        }
    
        $results = $query
            ->limit($limit)
            ->offset($offset)
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
    
    public function count()
    {
        return $this->mongo
            ->count($this->collection);
    }
    
    public function findOneById($id, $select = null)
    {
        if (!is_object($id)) {
            $id = new MongoId($id);
        }
        
        $query = $this->mongo
            ->where(array(
                '_id' => $id
            ));
        
        if ($select) {
            $query = $query
                ->select($select);
        }
    
        $results = $query
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;
    }
    
    public function findOneBySlug($slug)
    {
        $results = $this->mongo
            ->where(array(
                'slug' => $slug
            ))
            ->get($this->collection);
            
        if ($results) {
            return $this->assign($results[0]);
        }
        
        return null;
    }
    
    public function assign($document, $object)
    {
        $reflector = new ReflectionObject($object);
        
        foreach ($document as $name => $value) {
            if ($reflector->hasProperty($name)) {
                $attribute = $reflector->getProperty($name);
                $attribute->setAccessible(TRUE);
                $attribute->setValue($object, $value);
            } else {
                $object->{$name} = $value;
            }
        }
    
        return $object;
    }
}