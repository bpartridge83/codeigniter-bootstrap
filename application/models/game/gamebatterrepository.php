<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class GameBatterRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'gamebatter';
    }
    
    public function assign($document)
    {
        return parent::assign($document, new GameBatter());
    }
    
}