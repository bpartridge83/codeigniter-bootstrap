<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class LineupRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Lineup());
    }
    
}