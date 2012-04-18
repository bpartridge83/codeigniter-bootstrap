<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Forms
{
    protected $CI;
    protected $name;
    protected $class;
    protected $form;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->spark('formgenlib/2.0.1');
    }
    
    public function get($name, $data)
    {
        $this->name = explode('/', $name);
        $this->class = implode('_', $this->name);
        
        $this->CI->load->library(sprintf('Forms/%s/%s', $this->name[0], $this->class));
        
        return $this->CI->{$this->class}->build($data);
    }
    
    public function validate(&$form, $data)
    {
        $this->form = $form;
    
        $this->CI->load->library(sprintf('Forms/%s/%s', $this->name[0], $this->class));
        
        return $this->CI->{$this->class}->validate($form, $data);
    }
    
    public function save($data = null)
    {
        $this->CI->load->library(sprintf('Forms/%s/%s', $this->name[0], $this->class));
        
        return $this->CI->{$this->class}->save($this->form, $data);
    }
    
    public function fetch($property, $obj)
    {
        if ($var = $this->CI->input->post(strtolower($property))) {
            if (is_array($var)) {
                $var = (string) $var[0];
            }
        
            return $obj->{'set'.ucwords($property)}($var);
        }
        
        return false;
    }
}