<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Twig
{
	private $CI;
	private $_twig;
	private $_template_dir;
	private $_cache_dir;
	private $_debug;

	/**
	 * Constructor
	 *
	 */
	function __construct($debug = false)
	{
		$this->CI =& get_instance();
		$this->CI->config->load('twig');
		 
		ini_set('include_path',
		ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'libraries/Twig');
		require_once (string) "Autoloader" . EXT;

		log_message('debug', "Twig Autoloader Loaded");

		Twig_Autoloader::register();

		$this->_template_dir = $this->CI->config->item('template_dir');
		$this->_cache_dir = $this->CI->config->item('cache_dir');
		$this->_debug = $this->CI->config->item('debug');

		$loader = new Twig_Loader_Filesystem($this->_template_dir);

		$this->_twig = new Twig_Environment($loader, array(
                'cache' => $this->_cache_dir,
                'debug' => $this->_debug,
		));
	}

	public function add_function($name, $options = array()) 
	{
		$this->_twig->addFunction($name, new Twig_Function_Function($name, $options));
	}

	public function render($template, $data = array()) 
	{
        $this->CI->benchmark->add('Twig Render Template');
		$template = $this->_twig->loadTemplate($template);
		$this->CI->benchmark->add('Twig Render Template');
		return $template->render($data);
	}

	public function display($template, $data = array()) 
	{
        $name = $template;
        $this->CI->benchmark->add('Twig Display Template: '.$name);
		$template = $this->_twig->loadTemplate($template);
		/* elapsed_time and memory_usage */
		//$data['elapsed_time'] = $this->CI->benchmark->elapsed_time('twig_display_time_start', 'twig_display_time_end');
		$memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2) . 'MB';
		$data['memory_usage'] = $memory;
        $this->CI->benchmark->add('Twig Display Template: '.$name);
		$template->display($data);
	}
}