<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Alert
{
    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    public function add($id, $title = null, $summary = null, $class = null)
    {
        $alert = array(
            $id => array(
                'title'  => $title,
                'summary' => $summary,
                'class' => $class
            )
        );

        $this->CI->session->set_userdata($alert);
    }
    
    public function exists($id)
    {
        return ($this->CI->session->userdata($id)) ? true : false;
    }
    
    public function get($id)
    {
        $alert = $this->CI->session->userdata($id);
        $this->remove($id);
        return $alert;
    }
    
    public function remove($id)
    {
        $this->CI->session->unset_userdata($id);
    }
    
    public function all()
    {
        return $this->CI->session->all_userdata();
    }
    
}