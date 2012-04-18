<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Indexes extends MY_Controller {

    public function ensure()
    {
        print_r("Ensuring Indexes\n\n");
    
        foreach ($this->indexes as $collection => $indexes) {
        
            print_r(sprintf("Removing Existing Indexes for Collection: %s\n\n", $collection));
            $this->mongo
                ->remove_all_indexes($collection);
            
            foreach ($indexes as $index) {
                print_r(sprintf("Adding Index Set for Collection: %s\n", $collection));
                print_r($index);
                $this->mongo
                    ->add_index($collection, $index);
            }
            
            print_r("\n");
        }
    }
    
    protected $indexes = array(
        'collection' => array(
            array(
                'key' => 1,
                'key2' => -1
            )
        )
    );
    	
}


/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */