<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PitchRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'pitch';
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Pitch());
    }
    
}