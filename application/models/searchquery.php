<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class SearchQuery extends Document {

    protected $terms = array();
    protected $datetime;
    protected $success;
    protected $path;

    public function __construct()
    {
        parent::__construct();
        
        $this->datetime = time();
    }
        
    public function getTerms()
    {
        return $this->terms;
    }
    
    public function setTerms($terms)
    {
        $this->terms = $terms;
    }
    
    public function addTerm($term)
    {
        array_push($this->terms, $term);
    }
    
    public function getQuery()
    {
        return implode(' + ',$this->terms);
    }
    
    public function getDatetime()
    {
        return $this->datetime;
    }
    
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }
    
    public function getSuccess()
    {
        return (bool) $this->success;
    }
    
    public function setSuccess($success)
    {
        $this->success = (bool) $success;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setPath($path)
    {
        $this->path = $path;
    }
        
}