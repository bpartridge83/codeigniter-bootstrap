<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class DM
{
    protected $queue;
    
    public function __construct()
    {
        $this->queue = array();
    }

    public function persist($obj)
    {
        return array_push($this->queue, $obj);
    }
    
    public function test()
    {
        die();
    }
    
    public function flush()
    {
        foreach ($this->queue as $obj) {
            $obj->save();
        }
        
        return true;
    }
    
    public function clear()
    {
        unset($this->queue);
    }
    
}