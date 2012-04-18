<?php

/**
*
* Console
*
* An implementation of the firePHP serverside component. Print your php variables to the firebug console.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Attribution-ShareAlike 3.0 that is
* bundled with this package in the files license.txt.  It is
* also available through the world wide web at this URL:
* http://creativecommons.org/licenses/by-sa/3.0/nz/
*
* @package   Autoform
* @author    Toby Evans (@t0bz)
* @copyright Copyright (c) 2011, Toby Evans
* @license   http://creativecommons.org/licenses/by-sa/3.0/nz/
* @link      http://getsparks.org/packages/console/versions/HEAD/show
* @since     Version 3.0
* @filesource
*/

class Console {
	
	public $enabled = TRUE;
	protected $index = 1;
	public $log_path = '';
	public $log_file = '';
	private $CI;

	function Console() {
		$this->CI =& get_instance();
		$config =& get_config();
		$this->log_path = ($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';
		$this->log_file = 'console-'.date('Y-m-d').'.php';
		$this->enabled = config_item('console_active');
	}
	
	/**
	* Log data to the fireBug Console (via firePHP)
	* @param Mixed $type
	* @param Mixed $message
	* @param Bool $write_to_file [optional]
	*/
	public function log($message, $type='log', $write_to_file=FALSE) {
		$header_name = 'X-Wf-1-1-1-'.$this->index;
		
		if (!is_array($type) && !is_object($type)) {
			if (in_array(strtolower($type), array('log','info','warn','error'))) {
				// create header value
				$header_value = '[{"Type":"'.strtoupper($type).'"},'.json_encode($message).']';
			}
			else {
				// fallback if $type was incorrect
				$this->log('FirePHP: The log type: '.$type.' is Invalid', 'error', TRUE);
				// test the $message con be JSON encoded. if not, stringify it first
				try {
					$header_value = '[{"Type":"LOG"},'.@json_encode($message).']';
				}
				catch (Exception $e) {
					$header_value = '[{"Type":"LOG"},'.json_encode(print_r($message, TRUE)).']';
				}
			}
			// write to log file
			if ($write_to_file==TRUE) {
				$this->write($message, $type);
			}
		}
		else {
			$meta;
			// create meta Object
			foreach ($type as $key=>$value) {
				$key = ucfirst($key);
				$meta->$key = $value;
			}

			$body;
			// create body object
			foreach ($message as $key=>$value) {
				$key = ucfirst($key);
				$body->$key = $value;
			}
			// create header value
			$header_value = '['.json_encode($meta).','.json_encode($body).']';
			
			if ($write_to_file==TRUE) {
				$this->write($meta->Type, $body->Trace.': '.json_decode($body->Trace));
			}
		}
		
		if ($this->enabled) {
			if ($this->index==1) {
				// set base firePHP headers
				$this->CI->output->set_header('X-Wf-Protocol-1: http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
				$this->CI->output->set_header('X-Wf-1-Plugin-1: http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3');
				$this->CI->output->set_header('X-Wf-1-Structure-1: http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
			}
			
			// set output header
			$this->CI->output->set_header($header_name.': '.strlen($header_value).'|'.$header_value.'|');
			
			// increase log index
			$this->index++;
		}
	}
	
	/**
	* Write Log to a file
	* 
	* @param Mixed $message
	* @param String $type
	* @return Bool
	*/
	public function write($message, $type='LOG') {

		// check if log files are active
		if ( ! config_item('console_logs')) return;

		$log = '';
		if ( ! file_exists($this->log_path.$this->log_file)) {
			$log .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
		$log .= strtoupper($type).' - '.date('Y-m-d h:i:s').' --> '.print_r($message, TRUE)."\n";
		if (@file_put_contents($this->log_path.$this->log_file, $log, FILE_APPEND)) {
			return TRUE;
		}
		else {
			log_message('error', 'Failed to write console log file');
		}
	}
}

if (!function_exists('console_log')) {
	function console_log($message, $type="LOG", $write_to_file=FALSE) {
		$CI =& get_instance();
		$CI->console->log($message, $type, $write_to_file);
	}
}
if (!function_exists('console_write')) {
	function console_write($message, $type="LOG") {
		$CI =& get_instance();
		$CI->console->write($message, $type);
	}
}

/* End of file console.php */
/* Location: sparks/console/.../libraries/console.php */