<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class PageRepository extends DocumentRepository {
    
    public function __construct()
    {
        parent::__construct();
        $this->collection = 'page';
    }
    
    public function findOneByUrl($url)
    {
        $results = $this->mongo
            ->where(array(
                'url' => $url,
            ))
            ->get($this->collection);
            
        if (!$results) {
            return false;
        }
        
        return $this->assign($results[0]);  
    }
        
    public function assign($document)
    {
        $page = new Page();
    
        $page->setId($document['_id']);
        $page->setUrl($document['url']);
        $page->setSource($document['source']);

        return $page;
    }
    
}