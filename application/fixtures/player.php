<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixtures_Player {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->model('PlayerRepository', '_player');
    }

    public function load()
    {
        $this->clear();
        
        // Alfredo Rodriguez (Maryland)
        
        $player = new Player();
        
        $player->setFirstName('Alfredo');
        $player->setLastName('Rodriguez');
        
        $player->setHeight(72);
        $player->setWeight(180);
        
        $player->setBats('R');
        $player->setThrows('R');
        
        $player->setHometown('Oak Hill, Va.');
        
        $player->setCstvId(395839);
        $player->setNcaaId(993788);
        
        $player->save();
        
        print_r(sprintf("Created Player: %s (%s)\n", $player->getName(), $player->getId()));
        
    }

    public function clear()
    {
        $this->CI->mongo->drop_collection('smallball', 'player');
    }

}