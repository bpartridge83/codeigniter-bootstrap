<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PlateAppearanceRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'plateappearance';
    }
    
    public function assign($document)
    {
        return parent::assign($document, new Pitch());
    }
    
}