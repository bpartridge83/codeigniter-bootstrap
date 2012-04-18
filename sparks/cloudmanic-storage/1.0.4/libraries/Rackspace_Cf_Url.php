<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//
// Company: Cloudmanic Labs, http://cloudmanic.com
// By: Spicer Matthews, spicer@cloudmanic.com
// Date: 9/17/2011
// Description: Example class / method for building custom 
//							authenticated urls for Rackspace. 
//

class Rackspace_Cf_Url
{
	function get_url($file)
	{
        $this->_CI =& get_instance();
		$this->_config = $this->_CI->config->item('storage');
		
		$this->_assets_config = $this->_CI->config->item('assets');
		$version = $this->_assets_config['version'];
		
		$env = $this->_config['env'][ENVIRONMENT];
		
		$file = preg_replace(sprintf('/%s[A-Za-z0-9]{10}./', date('Y')), '', $file);
		
        return sprintf('%s/smallball_%s.v%s.%s', $env['cdn_url'], ENVIRONMENT, $version, $file);
	}
}

/* End File */