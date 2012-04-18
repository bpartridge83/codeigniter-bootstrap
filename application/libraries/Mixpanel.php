<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mixpanel
{
    protected $CI;
    protected $token;
    protected $host = 'http://api.mixpanel.com/';
    protected $properties = array();
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('mixpanel');
        
        $this->token = $this->CI->config->item('token');
        
        $this->setDistinctId();
    }
    
    public function setProperty($key, $val)
    {
        $this->properties[$key] = $val;
    }
    
    public function getDistinctId()
    {
        return $this->CI->session->userdata('session_id');
    }
    
    public function setDistinctId()
    {
        $id = $this->getDistinctId();
    
        $this->setProperty('distinct_id', $id);
    }
    
    public function track($event, $properties = array())
    {
        $params = array(
            'event' => $event,
            'properties' => array_merge($this->properties, $properties) 
        );

        if (!isset($params['properties']['token'])){
            $params['properties']['token'] = $this->token;
        }
        
        $url = $this->host . 'track/?data=' . base64_encode(json_encode($params));
        
        //you still need to run as a background process
        exec("curl '" . $url . "' >/dev/null 2>&1 &"); 
    }
    
    public function getToken()
    {
        return $this->token;
    }

    // Example usage:
    // $metrics->track('purchase', 
    //      array('item'=>'candy', 'type'=>'snack', 'ip'=>'123.123.123.123'));
    
}