<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Load the Postmark config when the spark is loaded
$autoload['config'] = array('postmark');

// Load the Core CI_EMail library as it is extended
$autoload['libraries'] = array('email', 'postmark');

/* End of file autoload.php */
/* Location: ./application/config/autoload.php */