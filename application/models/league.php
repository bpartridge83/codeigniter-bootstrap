<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class League extends Document implements Sluggable {

    protected $slug;
    protected $alternateSlugs = array();
    protected $acronym;
    protected $level;

    public function __construct($name = null)
    {
        parent::__construct();
        
        if ($name) {
            $this->setName($name);
        }
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setSlug($slug = null, $auto = true)
    {
        if (!$slug && $auto) {
            $slug = $this->slugify->create($this);
        }
        
        if ($slug) {
            $this->slug = $slug;
        }
    }
    
    public function getAlternateSlugs()
    {
        return $this->alternateSlugs;
    }
    
    public function addAlternateSlug($slug)
    {
        if ($this->hasAlternateSlug($slug)) {
            return true;
        }
        
        $this->alternateSlugs[] = $slug;
        
        return $this->getAlternateSlugs();
    }
    
    public function removeAlternateSlug($slug)
    {
        foreach ($this->alternateSlugs as $key => $alternateSlug) {
            if ($alternateSlug == $slug) {
                unset($this->alternateSlugs[$key]);
                return true;
            }
        }
        
        return null;
    }
    
    public function hasAlternateSlug($slug)
    {
        if (in_array($slug, $this->alternateSlugs)) {
            return true;
        }
        
        return false;
    }
    
    public function setAlternateSlugs($slugs)
    {
        $this->alternateSlugs = $slugs;
    }
    
    public function getAcronym()
    {
        return $this->acronym;
    }
    
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;
    }
    
    public function hasLevel()
    {
        return (bool) $this->level;
    }
    
    public function getLevel()
    {
        if (is_object($this->level)) {
            $results = $this->mongo
                ->get_dbref($this->level);
                
            $this->load->model('LevelRepository', '_level');
                
            return $this->_level->assign($results);
        }
        
        return $this->level;
    }
    
    public function setLevel($level)
    {
        if (is_object($level)) {
            $level = $this->mongo
                ->create_dbref('level', $level->getId());
        }
    
        $this->level = $level;
    }
        
}