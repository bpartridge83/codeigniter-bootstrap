<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Page extends Document {

    protected $url;
    protected $source;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function __toString()
    {
        return $this->getUrl();
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
    }

}