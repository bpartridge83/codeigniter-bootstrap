<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Core {

    protected $CI;
    protected $fixtures;
    
    public function __construct()
    {
        $this->CI =& get_instance();
    
        $this->fixtures = array(
            'level' => array(),
            'league' => array(
                'dependencies' => array('level')
            ),
            'division' => array(
                'dependencies' => array('level', 'league')
            ),
            'conference' => array(
                'dependencies' => array('level', 'league', 'division')
            ),
            'team' => array(
                'dependencies' => array('level', 'league', 'division', 'conference')
            ),
            'player' => array(
                'dependencies' => array('')
            ),
            'season' => array(
                'dependencies' => array('team', 'player')
            ),
            'game' => array(
                'dependencies' => array('team')
            )
        );
    }

    public function load($type)
    {
        if (!array_key_exists($type, $this->fixtures)) {
            return false;
        }
        
        if (array_key_exists('loaded', $this->fixtures[$type])) {
            return false;
        }
    
        $this->dependencies($type);
    
        $this->CI->load->file(sprintf('application/fixtures/%s.php', $type));
        
        $class = sprintf('Fixtures_%s', $type);
        $fixture = new $class(); 
        $fixture->load();
        
        //call_user_func(sprintf('Fixtures_%s::load', $type));
        
        //array_push($this->fixtures[$type], array('loaded' => true));
        $this->fixtures[$type]['loaded'] = true;
    }

    protected function dependencies($type)
    {
        if (!array_key_exists('dependencies', $this->fixtures[$type])) {
            return false;
        }
        
        foreach ($this->fixtures[$type]['dependencies'] as $dependency) {
            $this->load($dependency);
        }
    }

}