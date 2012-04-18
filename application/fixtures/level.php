<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Level {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('LevelRepository', '_level');
    }

    public function load()
    {
        $this->clear();
        
        $level = new Level('College');
        $level->save();
        
        print_r(sprintf("Created Level: %s (%s)\n", $level->getName(), $level->getId()));
        
        $level = new Level('Summer');
        $level->save();
        
        print_r(sprintf("Created Level: %s (%s)\n", $level->getName(), $level->getId()));
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'level');
    }

}