<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions extends MY_Controller {

    public function set()
    {
        foreach ($this->permissions as $loc => $perm) {
            $command = sprintf("sudo chmod %s %s", $perm, $loc);
            print_r(sprintf("%s\n", $command));
            shell_exec($command);
        }
    }
    
    protected $permissions = array(
        'application/cache' => '-R 777',
        'web/cache' => '-R 777'
    );
    	
}


/* End of file permissions.php */
/* Location: ./application/controllers/utility/permissions.php */