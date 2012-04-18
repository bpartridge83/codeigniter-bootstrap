<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

interface Sluggable
{
    public function getSlug();
    public function setSlug($slug = null, $auto = true);
}

class Document extends CI_Model {
    
    protected $_id;
    protected $name;
    
    public function __construct()
    {
        parent::__construct();
        //$this->load->library('elasticsearch');
    }
    
    public function __call($method, $args)
    {
        show_error(sprintf('Missing method <b>%s</b>', $method));
    }
    
    public function __clone()
    {
        $this->unsetId();
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setId($id)
    {
        $this->_id = $id;
    }
    
    public function unsetId()
    {
        unset($this->_id);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function save($set = false)
    {
        if ($set) {
            return $this->set();
        }
    
        if (method_exists($this, 'getSlug') && !$this->getSlug()) {
            $slug = $this->slugify->create($this);
            $this->setSlug($slug);
        }
    
        $documentArray = $this->toArray();
        
        if (array_key_exists('_id', $documentArray)) {
            $_id = $documentArray['_id'];
            unset($documentArray['_id']);
        }
        
        if (isset($_id)) {
            $results = $this->mongo
                ->where(array(
                    '_id' => $_id
                ))
                ->update($this->getCollection(), $documentArray);
            
            return $this->getRepository()->findOneById($this->getId());
        } else {
            $_id = $this->mongo
                ->insert($this->getCollection(), $documentArray);
                
            $document = $this->getRepository()->findOneById($_id);
            $this->setId($document->getId());
                
            return $this;
        }
        
        return null;
    }
    
    public function set($fields = null)
    {
        if (!$this->getId()) {
            return null;
        }
    
        if (!$fields) {
            $fields = $this->toArray();
            unset($fields['_id']);
        }
    
        $result = $this->mongo_db
            ->where(array('_id'=>$this->getId()))
            ->set($fields)
            ->update($this->getCollection());
        
        return $this->getRepository()->findOneById($this->getId());
    }
    
    public function remove()
    {
        $documentArray = $this->toArray();
        
        if (array_key_exists('_id', $documentArray)) {
            $_id = $documentArray['_id'];
        }
    
        if (isset($_id)) {
            $results = $this->mongo
                ->where(array(
                    '_id' => $_id
                ))
                ->delete($this->getCollection());
            
            return $results;
        }
        
        return null;
    }
    
    public function setProperty($property, $value = null)
    {
        return $this->{'set'.ucwords($property)}($value);
    }
    
    public function getProperty($property)
    {
        return $this->{'get'.ucwords($property)};
    }
    
    public function getRepository()
    {
        return $this->{'_'.strtolower(get_class($this))};
    }
    
    public function getCollection()
    {
        return $this->getRepository()->collection;
    }
    
    public function toArray()
    {
        $array = array();
        
        foreach ($this as $key => $value)
        {
            $array[$key] = $value;
        }
        
        $array = array_filter($array, 'removeEmptyItems');
        
        return $array; 
    }
    
    public function validate(&$form, $data)
    {
        $this->forms->validate($form, $data);
    }
    
}

function removeEmptyItems($item)
{
    if (is_array($item)) {
        return array_filter($item, 'removeEmptyItems');
    }
 
    if (!empty($item)) {
        return true;
    }
}