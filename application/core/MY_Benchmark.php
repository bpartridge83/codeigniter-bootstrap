<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Benchmark extends CI_Benchmark {

    protected $list = array();
    protected $count = 0;

    function add($str)
    {
        if (in_array($str, $this->list)) {
            $this->mark($str.'_end');
            foreach ($this->list as $key=>&$value) {
                if (is_string($str)) {
                    unset($this->list[$key]);
                }
            }
            
            $this->count++;
        } else {
            $this->mark($str.'_start');
            array_push($this->list, $str);
        }
        
        return true;
    }

    function query($query)
    {
        $query = "MongoDB Query: ".$query;
        
        $this->add($query);
        
        return true;
    }
    
    public function getCount()
    {
        $count = $this->count;
        $this->count = 0;
        
        return $count;
    }
    
}