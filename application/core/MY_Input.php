<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Input extends CI_Input
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function is_post()
    {
        return (bool) ($this->server('REQUEST_METHOD') == 'POST');
    }
    
    function is_get()
    {
        return (bool) ($this->server('REQUEST_METHOD') == 'GET');
    }    
    
    function is_ajax()
    {
        return (bool) $this->is_ajax_request();
    }  
    
    function is_cli()
    {
        return (bool) $this->is_cli_request();   
    }
    
}  