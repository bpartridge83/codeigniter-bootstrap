<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Form Generation Class
 * 
 * This class will help you to save
 * time in building forms with CodeIgniter
 * If you have code improvements please 
 * contact me and I will update the code!
 * 
 * @license		MIT License
 * @author		Frank Michel
 * @link		http://frankmichel.com
 * @docu		http://frankmichel.com/formgenlib
 * @email		info@frankmichel.com
 * 
 * @file		Form.php
 * @version		2.0.0
 * @date		11/25/2011
 * 
 * Copyright (c) 2009 Frank Michel
 *
 * ---------------------------------------------------------------------------
 * Below are copyright notices for code used in this library:
 * ---------------------------------------------------------------------------
 *
 * reCAPTCHA helper and validation function
 * http://codeigniter.com/forums/viewthread/94299/
 *
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          http://recaptcha.net/api/getkey
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */ 
class El {

	var $globals = array();
	var $defaults = array();

	var $unique = array();
	
	var $nameasid = FALSE;
	
	var $error_inline = FALSE;
	var $error_inline_open = '<span>';
	var $error_inline_close = '</span>';
	var $error_flag;

	var $label_pos = '';
	var $label_class = '';
	var $label_req_class = '';
	var $label_req_flag = '';

	var $group = '';					// status of the current group element (first, last or single)
	
	var $group_prefix = '';				// for checkgroup|radiogroup
	var $group_suffix = '';
	var $element_prefix = '';			// for ALL elements
	var $element_suffix = '';
	var $label_prefix = '';				// for label
	var $label_suffix = '';
	var $field_prefix = '';				// for fieldset|text|password|upload|checkbox|radiobutton|image|submit|reset|textarea|button|captcha|recaptcha
	var $field_suffix = '';		
	
	// --------------------------------------------------------------------------	

	var $atts = array();

	var $_valid_atts = array(
		'label' 	=> 'accesskey|onblur|onfocus|class|style|title|dir|lang', // 'for' not included, supplied with form helper function
		'form' 		=> 'action|accept|accept-charset|enctype|method|name|onreset|onsubmit|target',
		'input' 	=> 'accept|accesskey|align|alt|checked|disabled|ismap|maxlength|name|onblur|onchange|onfocus|onselect|readonly|size|src|tabindex|type|usemap|value',
		'select' 	=> 'disabled|multiple|onblur|onchange|onfocus|size|tabindex', // 'name' not included, supplied with form helper function
		'textarea'	=> 'cols|rows|accesskey|disabled|name|onblur|onchange|onfocus|onselect|readonly|tabindex',
		'button' 	=> 'accesskey|disabled|name|onblur|onfocus|tabindex|type|value|content', // 'content' added, supplied with form helper function
		'*' 		=> 'class|id|style|title|dir|lang|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|rel|data-value'
	);

	var $error;	// full error with open/close	
	var $error_message; // error message only
	
	var $upload_path;
	var $allowed_types;
	var $file_name;
	var $overwrite = FALSE;
	var $max_size = 0;
	var $max_width = 0;
	var $max_height = 0;
	var $max_filename = 0;
	var $encrypt_name = FALSE;
	var $remove_spaces = TRUE;
	
	// --------------------------------------------------------------------------	
	
	function El($info, $config=array()) 
	{
		// set config vars
		foreach ($config as $key=>$value)
		{
			if ($value) $this->{$key} = $value;
		}
		
		if ($info['type'] == 'fieldset_close')
		{
			$fieldset_close = TRUE;
			$info['type'] = 'fieldset';
		}

		// load globals
		$merged = $this->_make_info($this->globals);

		// merge defaults (by type)
		if (array_key_exists($info['type'], $this->defaults)) $merged = $this->_make_info($this->defaults[$info['type']], $merged);
		
		// merge defaults (by element)
		if (array_key_exists('name', $info) && array_key_exists($info['name'], $this->defaults)) $merged = $this->_make_info($this->defaults[$info['name']], $merged);

		// merge element info
		$merged = $this->_make_info($info, $merged, TRUE); // last pass of _make_info()

		if (isset($fieldset_close)) $merged['type'] = 'fieldset_close';

		$this->_make_properties($merged);
	}

	/**
	 * Make Info
	 * 
	 * Merge (combine or replace) element info
	 */
	function _make_info($source=NULL, $base=array(), $last_pass=FALSE) // $source = NAME or TYPE of element
	{
		if (is_array($source))
		{
			$replace = explode('|', $this->replace);
			if (count($replace) != 3) show_error(FGL_ERR.'Wrong parameter count for $replace in config file.');

			foreach ($source as $att=>$value)
			{
				switch ($att)
				{
					// classes
					case 'class':
						if ($replace[0] == 'TRUE') // replace
						{
							$base['class'] = $value;
						}
						else // combine
						{	
							$old = (array_key_exists('class', $base)) ? explode(' ', $base['class']) : array();
							$this->_trim_array($old);
	
							$new = explode(' ', $value);
							$this->_trim_array($new);
	
							$merged = array_unique(array_merge($old, $new));
							$base['class'] = implode(' ', $merged);
						}
					break;
					
					// styles
					case 'style':
						$old = array();
						$pair = array();
						$old_vals = (array_key_exists('style', $base)) ? explode(';', $base['style']) : array();
						$this->_trim_array($old_vals);
						foreach ($old_vals as $k=>$v) $pair[$k] = explode(':', $v);
						if (isset($pair)) foreach ($pair as $line) $old[trim($line[0])] = trim($line[1]);

						$new = array();
						$pair = array();
						$new_vals = explode(';', $value);
						$this->_trim_array($new_vals);
						foreach ($new_vals as $k=>$v) $pair[$k] = explode(':', $v);
						if (isset($pair)) foreach ($pair as $line) $new[trim($line[0])] = trim($line[1]);

						if ($replace[1] == 'TRUE') // replace
						{
							foreach ($new as $key=>$value)
							{
								$old[$key] = $value;
							}
							foreach ($old as $key=>$value)
							{
								$style[] = $key.':'.$value;
							}
						}
						else // preserve
						{						
							$style = array();
							$merged = array_merge($new, $old); // preserve previously set values
							foreach ($merged as $key=>$value)
							{
								if ($key && $value) $style[] = $key.':'.$value;
							}
						}
						$base['style'] = implode(';', $style);						
					break;
					
					// javascript event handlers
					case (substr($att, 0, 2) == 'on'):
						if ($replace[2] == 'TRUE') // replace
						{
							$base[$att] = $value;
						}
						else // combine
						{					
							$old = (array_key_exists($att, $base)) ? explode(';', $base[$att]) : array();
							$this->_trim_array($old);
												
							$new = explode(';', $value);
							$this->_trim_array($new);
	
							$merged = array_unique(array_merge($old, $new));
							$base[$att] = implode(';', $merged);
						}
					break;
					
					case 'rules':
						$old = (array_key_exists('rules', $base)) ? $base['rules'] : '';

						if (strstr($old, '^'))
						{
							$new = str_replace('^', '|'.$value.'|', $old);
						}
						else
						{
							$new = $old.'|'.$value;
						}
						
						if ($last_pass)
						{
							$new = str_replace('^', '|', $new);
						}

						$new = explode('|', $new);				
						$new = implode('|', array_unique(array_filter($new)));

						$base['rules'] = $new;
					break;
					
					default:
					// replace all other attributes by default
					$base[$att] = $source[$att];
				}
			}
		}

		return $base;
	}	

	/**
	 * Make Properties
	 * 
	 * Convert element info to properties and attribute array
	 */
	function _make_properties($info) 
	{
		// add maxlength attribute to element, if max_length[] rule was set
		if (array_key_exists('rules', $info) && !empty($info['rules']) && strstr($info['rules'], 'max_length'))
		{
			$rules = explode('max_length[', $info['rules']);
			$rules = explode(']', $rules[1]);
			$info['maxlength'] = $rules[0];
		}

		// add maxlength attribute to element, if exact_length[] rule was set
		if (array_key_exists('rules', $info) && !empty($info['rules']) && strstr($info['rules'], 'exact_length'))
		{
			$rules = explode('exact_length[', $info['rules']);
			$rules = explode(']', $rules[1]);
			$info['maxlength'] = $rules[0];
		}
		
		// set unique id for internal element access
		if (array_key_exists('name', $info) && $info['name'])
		{ 
			$info['name'] = preg_replace('/ /', '_', strtolower($info['name'])); // convert spaces to underscores
	
			// checkboxes and radio buttons
			$types = array('checkbox', 'radio');			
			if (in_array($info['type'], $types))
			{
				if (empty($info['id']))
				{
					$unique = $this->_get_unique();
					$info['id'] = $unique;		// checkboxes and radio buttons always have labels, therefore need an id
					$info['unique'] = $unique;	// unique id also becomes internal element access id
				}
				else
				{
					$info['unique'] = $info['id']; // use given id for internal element access id, if provided
				}
			}
			// other elements
			else
			{
				if ($this->nameasid && empty($info['id'])) $info['id'] = $info['name'];	// set 'id' as 'name'
				
				if ( ! empty($info['label_text']) && empty($info['id']))
				{	
					$unique = $this->_get_unique();
					$info['id'] = $unique;		// id is required if a label was specified
					$info['unique'] = $unique;	// unique id also becomes internal element access id
				}
				elseif ( ! empty($info['id']))
				{
					$info['unique'] = $info['id']; // use given id for internal element access id, if provided
				}
				else
				{
					$info['unique'] = $info['name']; // if no label or id was provided, use the elements name as the internal element id
				}
			}
			
			// set checkbox, radio button and select names as arrays
			$types = array('checkbox', 'radio', 'select');
			if (in_array($info['type'], $types)) 
			{
				$info['name'] .= '[]';
			}
		}
		elseif ($info['type'] == 'image' || $info['type'] == 'submit' || $info['type'] == 'reset')
		{
			$info['unique'] = $info['name'];
			if ($this->nameasid && empty($info['id'])) $info['id'] = $info['name'];	// set 'id' as 'name'
		}
		else
		{
			// elements without a name attribute
			$info['unique'] = $this->_get_unique(); // assign unique internal element access id
		}
		
		foreach ($info as $key=>$value) 
		{
			$this->atts[$key] = $value; // save attributes in atts[] array
			$this->$key = $value; // make property->value pair
		}
	}

	/**
	 * Get Unique
	 * 
	 * Returns a unique id for an element
	 */	
	function _get_unique($name='')
	{
		$unique = substr(md5(mt_rand(0,2147483647).mt_rand(0,2147483647)), 0, 5);
		while (in_array($unique, $this->unique) || is_numeric(substr($unique, 0, 1)))
		{
			$unique = substr(md5(mt_rand(0,2147483647).mt_rand(0,2147483647)), 0, 5);
		}
		return $unique;
	}
	
	/**
	 * Trim Array
	 * 
	 * Trims all keys and values of an array
	 */		
	function _trim_array(&$array)
	{
		$arr = array();
		foreach ($array as $k=>$v)
		{
			if (trim($v)) $arr[trim($k)] = trim($v);
		}
		$array = $arr;
	}	

	/**
	 * Filter Attributes
	 * 
	 * Filters only valid attributes used with CodeIgniter form_helper function
	 */
	function _filter_atts($tag, $atts=NULL)
	{
		$atts = (isset($atts)) ? $atts : $this->atts;

		$filtered = array();
		foreach ($atts as $key=>$value) 
		{
			if ((array_key_exists($tag, $this->_valid_atts) && strstr($this->_valid_atts[$tag], $key) ) || in_array($key, explode('|', $this->_valid_atts['*'])))
			{
				$filtered[$key] = $value;
			}
		}
		if (count($atts)) return $filtered;
	}

	/**
	 * Attributes String
	 * 
	 * Returns attributes as string
	 */
	function _att_string($atts=array())
	{
		$array = array();

		foreach ($atts as $att=>$val) 
		{
			$array[] = $att.'="'.$val.'"';
		}
		
		if (count($array)) 
		{
			$str = implode(' ', $array);
			return $str;
		}
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Add Margin
	 * 
	 * Adds 'margin' to the element
	 */	
	function add_margin($margin, $pos='')
	{
		$old_vals = (array_key_exists('style', $this->atts)) ? explode(';', $this->atts['style']) : array();
		$this->_trim_array($old_vals);
		foreach ($old_vals as $k=>$v) $pair[$k] = explode(':', $v);
		
		if (isset($pair)) 
		{
			foreach ($pair as $line) $old[trim($line[0])] = trim($line[1]);
		}
		else
		{
			$old = array();
		}

		if (!$pos) $pos = 'left';
		$new['margin-'.$pos] = $margin.'px';

		$style = array();
		$merged = array_merge($old, $new);
		foreach ($merged as $key=>$value) 
		{
			if ($key && isset($value)) $style[] = $key.':'.$value;
		}
		$this->atts['style'] = implode(';', $style);
		
		return $this;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Make Label
	 * 
	 * Adds label to the label position: before|after the element
	 */
	function _make_label($pos='')
	{
		if ($this->label_text)
		{
			$req = (isset($this->rules)) ? strstr($this->rules, 'required') : FALSE;
			
			$for = (empty($this->for) && ! empty($this->id)) ? $this->id : $this->for;

			$type = $this->atts['type'];

			if ($type == 'label')
			{
				$atts = $this->atts;
			}
			else if (array_key_exists('label', $this->atts) && is_array($this->atts['label']))
			{
				$atts = $this->atts['label'];
			}
			else
			{
				$atts = array();
			}

			// load globals
			$merged = $this->_make_info($this->globals);

			// merge defaults for 'label'
			if (array_key_exists('label', $this->defaults)) $merged = $this->_make_info($this->defaults['label'], $merged);
			
			// merge defaults for element['label']
			if (array_key_exists($type, $this->defaults) && array_key_exists('label', $this->defaults[$type])) 
			{
				$merged = $this->_make_info($this->defaults[$type]['label'], $merged);
			}

			// merge filtered attributes
			$atts = $this->_make_info($atts, $merged);
			
			if ($req && $type != 'checkbox' && $type != 'radio' && array_key_exists('required_class', $atts)) 
			{
				// this construct prevents to flag a notice if 'class' key is not set
				if (array_key_exists('class', $atts))
				{
					$atts['class'] .= ' ' . $atts['required_class'];
				}
				else
				{
					$atts['class'] = ' ' . $atts['required_class'];
				}
			}

			// add error class
			if ($this->error_message) $atts['class'] .= ' ' . $this->error_class;
			
			if ($req && $type != 'checkbox' && $type != 'radio' && array_key_exists('required_flag', $atts)) $this->label_text .= $atts['required_flag'];

			// get label position
			$position = (isset($atts['position'])) ? $atts['position'] : 'before';

			$atts = $this->_filter_atts('label', $atts); // strip invalid attributes
			$label = $this->label_prefix.form_label($this->label_text, $for, $atts).$this->label_suffix;
			
			if ( ! $pos || $position == $pos) return $label;
		}
	}

	/**
	 * Make Error
	 * 
	 * Flags an error message
	 */		
	function _make_error($name = '') 
	{
		if ($this->error_inline && $this->error_message) 
		{
			if ($this->error_flag)
			{
				$flag = str_replace('{error}', $this->error_message, $this->error_flag);
				return $flag;
			}
			else
			{
				return str_replace('%s', $name, $this->error_inline_open) . $this->error_message . $this->error_inline_close;
			}
		}
	}	

	/**
	 * Elements
	 * 
	 * Handles the element creation
	 */	
	function fieldset()
	{
		$atts = $this->_filter_atts('fieldset');
		$el = form_fieldset($this->legend, $atts);
		return $this->element_prefix.$el.$this->field_prefix;
	}

	function fieldset_close() 
	{
		$el = form_fieldset_close();
		return $this->field_suffix.$el.$this->element_suffix;
	}
	
	function hidden()
	{
		$el = form_hidden($this->name, $this->value);
		return $el;
	}

	function label() 
	{
		return $this->_make_label();
	}

    function alterElementPrefixError(&$el)
    {
        if (!$el || is_null($this->_make_error($el))) {
            return $this->element_prefix;
        }
        
        return ($this->_make_error($el)) ? str_replace('">',' error">',$this->element_prefix) : $this->element->prefix;
    }

	function text() 
	{
		$atts = $this->_filter_atts('input');
		$el = $this->_make_label('before');
		$el .= $this->field_prefix.form_input($atts);
		$el .= ($this->error_inline) ? $this->_make_error($this->name) : '';
		$el .= $this->field_suffix;
		$el .= $this->_make_label('after');
		$el .= ($this->error_inline) ? '' : $this->_make_error($this->name);
		return $this->alterElementPrefixError($this->name).$el.$this->element_suffix;
	}
	
	function password() 
	{
		$atts = $this->_filter_atts('input');
		$el = $this->_make_label('before');
		$el .= $this->field_prefix.form_password($atts);
		$el .= ($this->error_inline) ? $this->_make_error($this->name) : '';
		$el .= $this->_make_label('after');
		$el .= ($this->error_inline) ? '' : $this->_make_error($this->name);
		return $this->alterElementPrefixError($this->name).$el.$this->element_suffix;
	}	
	
	function textarea() 
	{
		$atts = $this->_filter_atts('textarea');
		$el = $this->_make_label('before');
		$el .= $this->field_prefix.form_textarea($atts, $this->value).$this->field_suffix;
		$el .= $this->_make_label('after');
		$el .= $this->_make_error($this->name);
		return $this->element_prefix.$el.$this->element_suffix;
	}

	function upload() 
	{
		$atts = $this->_filter_atts('input');	
		$el = $this->_make_label('before');
		$el .= $this->field_prefix.form_upload($atts).$this->field_suffix;
		$el .= $this->_make_label('after');
		$el .= $this->_make_error($this->name);
		return $this->element_prefix.$el.$this->element_suffix;
	}
	
	function select() 
	{
		$atts = $this->_filter_atts('select');
		if ( ! empty($atts['multiple'])) $atts['multiple'] = 'multiple';		
		$atts = $this->_att_string($atts);
		$el = $this->_make_label('before');
		$el .= $this->field_prefix.form_dropdown($this->name, $this->options, $this->selected, $atts).$this->field_suffix;
		$el .= $this->_make_label('after');
		$el .= $this->_make_error($this->name);
		return $this->element_prefix.$el.$this->element_suffix;
	}

	function checkbox() 
	{
		$atts = $this->_filter_atts('input');
		$el = $this->_make_label('before');	
		$el .= $this->field_prefix.form_checkbox($atts).$this->field_suffix;
		$el .= $this->_make_label('after');
		$el .= $this->_make_error($this->name);
		$el = $this->element_prefix.$el.$this->element_suffix;

		switch ($this->group)
		{
			case 'first': return $this->group_prefix.$el;
			case 'last': return $el.$this->group_suffix;
			case 'single': return $this->group_prefix.$el.$this->group_suffix;
			default: return $el;
		}
	}	
		
	function radio() 
	{
		$atts = $this->_filter_atts('input');
		$el = $this->_make_label('before');		
		$el .= $this->field_prefix.form_radio($atts).$this->field_suffix;		
		$el .= $this->_make_label('after');
		$el .= $this->_make_error($this->name);
		$el = $this->element_prefix.$el.$this->element_suffix;

		switch ($this->group)
		{
			case 'first': return $this->group_prefix.$el;
			case 'last': return $el.$this->group_suffix;
			case 'single': return $this->group_prefix.$el.$this->group_suffix;
			default: return $el;
		}
	}

    function recaptcha()
    {
    	if ($this->recaptcha_key_public == '')
		{
			return "Error loading security image. Please try again later";
    	}

    	if ($this->recaptcha_ssl == 'TRUE')
    	{
			$server = $this->recaptcha_api_secure_server;
    	}
    	else
    	{
    	    $server = $this->recaptcha_api_server;
    	}

		$errorpart = "&amp;error=";
        if (isset($this->recaptcha_error)) $errorpart .= $this->recaptcha_error;

        $el = $this->_make_label('before');
       	$el .= $this->_make_error();
        $el .= '<script type="text/javascript">
                      var RecaptchaOptions = {
                        theme:"'.$this->recaptcha_theme.'",
                        lang:"'.$this->recaptcha_lang.'"
                      };
                </script>
                <script type="text/javascript" src="'.$server.'/challenge?k='.$this->recaptcha_key_public.$errorpart.'"></script>
                <noscript>
                		<iframe src="'.$server.'/noscript?lang='.$this->recaptcha_lang.'&amp;k='.$this->recaptcha_key_public.$errorpart.'" height="300" width="500" frameborder="0"></iframe>
                		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                		<input type="hidden" name="recaptcha_response_field" id="recaptcha_response_field" value="manual_challenge" />
                </noscript>';
        return $this->element_prefix.$el.$this->element_suffix;
	}

	function button() 
	{
		$atts = $this->_filter_atts('button');
		$atts['type'] = $this->kind;
		$el = form_button($atts);
		//return $this->element_prefix.$el.$this->element_suffix;
		return $el;
	}

	function image() 
	{
		$atts = $this->_filter_atts('input');
		$el = $this->field_prefix.form_input($atts).$this->field_suffix;
		return $this->element_prefix.$el.$this->element_suffix;
	}

	function submit() 
	{
		$atts = $this->_filter_atts('input');
		$el = form_submit($atts);
		//return $this->element_prefix.$el.$this->element_suffix;
		return $el;
	}

	function reset() 
	{
		$atts = $this->_filter_atts('input');
		$el = form_reset($atts);
		//return $this->element_prefix.$el.$this->element_suffix;
		return $el;
	}
	
	/* SPECIAL ELEMENTS */

	function span() 
	{
		$atts = $this->_filter_atts('span');
		$atts = ( ! empty($atts)) ? ' ' . $this->_att_string($atts) : '';
		$el = '<span'.$atts.'>'.$this->content."</span>";
		return $el;
	}

	function html() 
	{
		return $this->content;
	}
	
	function br() 
	{
		$el = ( ! empty($this->clear)) ? '<br style="clear:both" />' : '<br />';
		return $el;
	}
	
	function hr() 
	{
		$atts = $this->_filter_atts('*');
		$atts = $this->_att_string($atts);
		$el = ( ! empty($this->clear)) ? '<hr style="clear:both" '.$atts.' />' : '<hr '.$atts.' />';
		return $el;
	}		

	function space() 
	{
		$el = ( ! empty($this->clear)) ? '<br style="clear:both" /><br />' : '<br /><br />';
		return $el;
	}		

	/**
	 * Get
	 * 
	 * Gets the element
	 */		
	function get()
	{
		$func = $this->type;
		return $this->$func();
	}	
}

// --------------------------------------------------------------------------

class Form {

	var $CI;
	var $config = array();

	var $lang;
	var $fieldsets = 0;
	var $indented = 0;
	var $columns = 0;
	
	var $action = FALSE;
	var $method = 'post';
	var $multipart = FALSE;
	var $atts = array();

	var $_elements = array();
	var $_labels = array();
	var $_aliases = array();
	var $_names = array();

	var $_last_accessed;
	var $_last_accessed_group = FALSE;
	var $_output;

	var $_submit_name = array();
	var $_posted = FALSE;
		
	var $_files = array();
	var $_data = array();

	var $_onsuccess = array();
	var $_postprocess = array();
		
	var $error = array();			// array holding all errors in the order errors were added
	var $errors;					// errors wrapped with error_string_open & error_string_close (accessible via $this->errors)
	var $error_string; 				// all errors (unwrapped) as string
	var $error_array = array();		// all errors in array(name => message)

	var $error_open = '<div>'; 		// must be provided, will be replaced
	var $error_close = '</div>';

	var $model = array();
	
	var $valid = TRUE;
	var $validated = FALSE;

	var $captcha = FALSE;	
	var $recaptcha = FALSE;
	var $recaptcha_error = '';
	
	var $retrieved = FALSE;	// used to check whether the form was retrieved via get() function
	
	// --------------------------------------------------------------------------
	
	function Form() 
	{
		// set CI instance
		$this->CI =& get_instance();
		$this->CI->load->helper('form');

		// define error message header
		if ( ! defined('FGL_ERR')) define('FGL_ERR', '<b>Form Generation Library</b><br />');

		// load config data
		$this->config(NULL, TRUE);
	}
	
	function clear() 
	{
		// reset all instance variables except $CI and $config
		foreach (get_class_vars(get_class($this)) as $name => $default)
		{ 
			$exclude = array('CI', 'config', 'error_open', 'error_close');
			if ( ! in_array($name, $exclude)) $this->$name = $default;
		}
	}
	
	/**
	 * Config
	 * 
	 * Writes configuration values into config array
	 */	
	function config($index=NULL, $init=FALSE)
	{
		// load config file
		$this->CI->load->config('form', TRUE);
		$config = $this->CI->config->item('form');
		
		// check if config file is associative or numeric
		$is_indexed = (is_numeric(array_shift(array_keys($config)))) ? TRUE : FALSE;
		
		if ($init)	// no key provided
		{
			// if config file has indexes
			if ($is_indexed) 
			{
				// use default index (1) at initialization
				$index = 1;

				if ( ! array_key_exists($index, $config)) show_error(FGL_ERR.'Config file could not be loaded with key ['.$index.'].');
			
				$config = $config[$index];
			}
		}
		else
		{
			// flag error if config() is called empty
			if ($is_indexed && ! isset($index)) show_error(FGL_ERR.'Config file is indexed. Please call <b>config(</b><i>key</i><b>)</b> method to load desired config data.');
			
			// flag error if wrong index is provided
			if ( ! $is_indexed || (isset($index) && ! array_key_exists($index, $config))) show_error(FGL_ERR.'Config file could not be loaded with key ['.$index.'].');
			
			// fill with default values from $config[1]
			if ($index > 1)
			{
				foreach ($config[1] as $key => $value)
				{
					if ( ! isset($config[$index][$key])) $config[$index][$key] = $value;
				}		
			}
			
			$config = $config[$index];
		}

		// process config items used in this class
		if ($config['error_open']) $this->error_open = $config['error_open'];
		if ($config['error_close']) $this->error_close = $config['error_close'];
		
		$this->config = $config;
		
		return $this;
	}

	/**
	 * Make Array
	 * 
	 * Converts comma separated list to array
	 */
	function _make_array($list) 
	{
		if (!is_array($list)) 
		{
			$list = explode(',', $list);
			foreach ($list as $key=>$value)
			{
				$list[$key] = trim($value);
			}
		}
		return $list;
	}		

	/**
	 * Attributes
	 * 
	 * Converts attribute string or array into array
	 */	
	function _make_info($atts, $type='el', $divider1=',', $divider2='=') 
	{
		// 'label' atts MUST be supplied via an array (either with or without 'element' atts)
		if ($type == 'label' && ! is_array($atts)) $atts = '';
			
		if (is_array($atts))
		{
			// atts are supplied as indexed array (for 'label' attribute only [count=1] or both 'element' and 'label' attributes [count=2])
			if ( ! empty($atts) && array_values($atts) === $atts)
			{
				if (count($atts) > 2) show_error(FGL_ERR.'Attribute array is not well formed. It must be either a <u>string</u> or an <u>array</u> like array(\'element_atts\', \'label_atts\').');

				switch ($type)
				{
					case 'el':
					case 'element':
					$atts = (count($atts) == 1) ? '' : $atts[0];
					break;
					
					case 'label':
					$atts = (count($atts) == 1) ? $atts[0] : $atts[1];
					break;
				}
				
				// atts within indexed array are supplied as associative array
				if (is_array($atts)) return $atts;
			}
			else 
			{
				// atts are supplied as associative array (for either 'element' or 'label')
				return $atts;
			}
		}
		
		// convert attribute string to attribute array (or simply return empty array)
		$array = array();
		if ($atts) 
		{
			$explodes = explode($divider1, $atts);
			foreach ($explodes as $val)
			{
				if ( ! strstr($val, $divider2)) show_error(FGL_ERR.'Attribute not well formed: \''.$val.'\' (\'attribute=\' missing)');
				
				$explode = explode($divider2, $val, 2);
				$array[trim($explode[0])] = trim(str_replace('"', '', $explode[1])); // strip double quotes
			}
		}
		
		return $array;
	}
	
	/**
	 * Make Name/ID
	 * 
	 * Explodes piped name|id values and adds them to $info
	 */		
	function _make_nameid($nameid, &$info)
	{
		if (strstr($nameid, '|'))
		{
			$nameid = explode('|', $nameid);
			$info['name'] = $nameid[0];
			$info['id'] = $nameid[1];
		}
		else
		{
			$info['name'] = $nameid;
			if ($this->config['nameasid']) $info['id'] = $nameid; // for form opening tag
		}
	}

	/**
	 * Make Value/ID
	 * 
	 * Explodes piped value|id values and adds them to $info
	 */			
	function _make_valueid($valid, &$info)
	{
		if (strstr($valid, '|'))
		{
			$valid = explode('|', $valid);
			$info['value'] = $valid[0];
			$info['id'] = $valid[1];
		}
		else
		{
			$info['value'] = $valid;
		}
	}		

	/**
	 * Check Name
	 * 
	 * Checks if element name was already used
	 */		
	function _check_name($name, $type='') {
		if ($type && !$name) show_error(FGL_ERR.'No name specified for '.$type.' element.');
		if (in_array($name, $this->_names)) show_error(FGL_ERR.'Element name "'.$name.'" already exists.');
		if ($name) $this->_names[] = $name;
	}

	/**
	 * Add (Element)
	 * 
	 * Instantiates element object
	 */
	function add($info) 
	{
		$el = new El($info, $this->config);
		$this->_add_element_to_form($el);
		return $this;
	}

	/**
	 * Elements
	 * 
	 * Prepares elements
	 */	
	function open($action = FALSE, $nameid='', $atts=array()) 
	{
		$this->action = $action;
		$this->atts = $this->_make_info($atts);
		if ($nameid) $this->_make_nameid($nameid, $this->atts);
		
		return $this;
	}
	
	function label($label, $for='', $atts=array(), $rules=array())
	{
		$info = $this->_make_info($atts);
		$info['type'] = 'label';
		$info['label_text'] = $label;
		$info['for'] = $for;
		$info['rules'] = $rules;
		$this->add($info);
		return $this;
	}	
			
	function fieldset($legend='', $atts=array())
	{
		if ($this->fieldsets || $legend === FALSE) 
		{
			$info['type'] = 'fieldset_close';
			$el = new El($info, $this->config);
			$this->_add_element_to_form($el);
			$this->fieldsets--;
		}
		if ($legend !== FALSE)
		{
			$info = $this->_make_info($atts);
			$info['type'] = 'fieldset';
			$info['legend'] = $legend;
			$this->add($info);
			$this->fieldsets++;
		}
		return $this;
	}

	function hidden($nameid, $value='', $rules='')
	{
		$info = array();
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'hidden');
		$info['type'] = 'hidden';
		$info['rules'] = $rules;
		$info['value'] = $value;
		$this->add($info);
		return $this;
	}
	
	function text($nameid, $label='', $rules='', $value='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'text');
		$info['type'] = 'text';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$info['value'] = $value;
		$this->add($info);
		return $this;
	}
	
	function password($nameid, $label='', $rules='', $value='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'password');
		$info['type'] = 'password';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$info['value'] = $value;
		$this->add($info);
		return $this;
	}	
	
	function pass($nameid, $label='', $rules='', $value='', $atts=array()) 
	{
		$this->password($nameid, $label, $rules, $value, $atts);
		return $this;
	}
	
	function textarea($nameid, $label='', $rules='', $value='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'textarea');
		$info['type'] = 'textarea';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$info['value'] = $value;
		if (empty($info['cols'])) $info['cols'] = 40; // get this from config file
		if (empty($info['rows'])) $info['rows'] = 10;		
		$this->add($info);
		return $this;
	}	
	
	function upload($nameid, $label='', $required=FALSE, $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'upload');
		$info['type'] = 'upload';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['required'] = $required;
		$this->add($info);
		$this->_files[] = $this->_last_accessed;
		$this->multipart = TRUE;
		return $this;
	}
	
	function iupload($nameid, $label='', $required=FALSE, $atts=array()) 
	{
		$info = $this->_make_info($atts);
		if (empty($info['allowed_types'])) $info['allowed_types'] = 'jpg|png|gif';
		$this->upload($nameid, $label, $required, $info);
		return $this;
	}	
	
	function select($nameid, $options=array(), $label='', $selected='', $rules='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'select');
		if (!count($options)) show_error(FGL_ERR.'You did not supply an array with the select options for element: '.$info['name']);				
		$info['type'] = 'select';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['options'] = $options;
		$selected = $this->_make_array($selected);
		$info['selected'] = $selected;
		if (count($selected) > 1) $info['multiple'] = TRUE;
		$info['rules'] = $rules;
		$this->add($info);
		return $this;
	}	

	function checkbox($nameid, $value, $label='', $checked=FALSE, $rules='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'checkbox');
		$info['type'] = 'checkbox';
		$info['value'] = $value;
		$info['checked'] = $checked; // string or array
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$this->add($info);
		return $this;
	}
	
	function bool($nameid, $label='', $checked=FALSE, $rules='', $atts=array()) 
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'bool');
		$info['type'] = 'checkbox';
		$info['value'] = 1;
		$info['checked'] = $checked; // string or array
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$this->add($info);		
		return $this;
	}
	
	function checkgroup($name, $checks=array(), $label='', $checked=array(), $rules='', $atts=array()) 
	{
		$checked = $this->_make_array($checked);
		$atts = $this->_make_info($atts);
		$this->_check_name($name, 'checkgroup');
		if ( ! count($checks)) show_error(FGL_ERR.'You did not supply an array with the checkboxes for element: '.$name);

		$i = 0;
		$this->_last_accessed_group = TRUE;

		$label_pos = (isset($this->config['defaults']['label']['position'])) ? $this->config['defaults']['label']['position'] : 'before';
		$label_pos = (isset($this->config['defaults']['checkgroup']['label']['position'])) ? $this->config['defaults']['checkgroup']['label']['position'] : $label_pos;

		$checks_count = count($checks);
		if ($label && $label_pos == 'before') $this->label($label, '', $atts, $rules);
		foreach ($checks as $check)
		{
			$info = array();
			
			// get attributes
			if (array_key_exists(2, $check)) 
			{
				// get element attributes
				$info = $this->_make_info($check[2]);

				// get label attributes
				$info['label'] = $this->_make_info($check[2], 'label');
			}

			$i++;
			$this->_make_valueid($check[0], $info);
			
			$info['type'] = 'checkbox';
			$info['name'] = $name;
			$info['checked'] = (in_array($info['value'], $checked)) ? TRUE : FALSE;
			$info['label_text'] = $check[1];
			$info['rules'] = $rules;
			$info['group_label'] = $label;
			
			if ($checks_count == 1) 
			{
				$info['group'] = 'single';
			} 
			else 
			{
				if ($i == 1) $info['group'] = 'first';
				if ($i == $checks_count) $info['group'] = 'last';			
			}
			
			$this->add($info);
		}
		if ($label && $label_pos == 'after') $this->label($label, '', $atts, $rules);
		$this->_last_accessed_group = FALSE;

		return $this;
	}
	
	function radiogroup($name, $radios=array(), $label='', $checked=array(), $rules='', $atts=array()) 
	{
		$checked = $this->_make_array($checked);
		$atts = $this->_make_info($atts);
		$this->_check_name($name, 'radiogroup');
		if ( ! count($radios)) show_error(FGL_ERR.'You did not supply an array with the radio buttons for element: '.$name);		

		$i = 0;
		$this->_last_accessed_group = TRUE;		
		
		$label_pos = (isset($this->config['defaults']['label']['position'])) ? $this->config['defaults']['label']['position'] : 'before';
		$label_pos = (isset($this->config['defaults']['radiogroup']['label']['position'])) ? $this->config['defaults']['radiogroup']['label']['position'] : $label_pos;

		$radios_count = count($radios);
		if ($label && $label_pos == 'before') $this->label($label, '', $atts, $rules);
		foreach ($radios as $radio)
		{
			$info = array();
			
			// get attributes
			if (array_key_exists(2, $radio)) 
			{
				// get element attributes
				$info = $this->_make_info($radio[2]);

				// get label attributes
				$info['label'] = $this->_make_info($radio[2], 'label');
			}
					
			$i++;
			$this->_make_valueid($radio[0], $info);	

			$info['type'] = 'radio';
			$info['name'] = $name;
			$info['checked'] = (in_array($info['value'], $checked)) ? TRUE : FALSE;
			$info['label_text'] = $radio[1];
			$info['rules'] = $rules;
			$info['group_label'] = $label;
			
			if ($radios_count == 1) 
			{
				$info['group'] = 'single';
			} 
			else 
			{
				if ($i == 1) $info['group'] = 'first';
				if ($i == $radios_count) $info['group'] = 'last';			
			}

			$this->add($info);
		}
		if ($label && $label_pos == 'after') $this->label($label, '', $atts, $rules);
		$this->_last_accessed_group = FALSE;
				
		return $this;
	}	
	
	function captcha($label='')
	{
		if ( ! isset($this->config['captcha']))
		{
			show_error(FGL_ERR."To use CAPTCHA you must configure it first.");
		}
		else
		{
			$this->config['captcha']['img_url'] = base_url().$this->config['captcha']['img_path'];
		}
		
		$this->CI->load->plugin('captcha');		
		$this->captcha = create_captcha($this->config['captcha']);
		$this->html($this->captcha['image']);
		
		$this->CI->session->set_flashdata(array(
				'captcha_word' => $this->captcha['word'],
				'captcha_time' => $this->captcha['time'],
		));
		
		$this->text('captcha', $label, 'required|max_length[128]|xss_clean', '', array());
		
		return $this;
	}	
	
	function recaptcha($label='')
	{
		$info = array();
		$this->_make_nameid('recaptcha_response_field', $info);
		$this->_check_name($info['name']);
		$info['type'] = 'recaptcha';
		$info['label_text'] = $label;
		
		if (!$this->config['recaptcha_key_public']) show_error(FGL_ERR."To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
		
		// assemble the configuration-array
		$params = array(
			  'recaptcha_api_server' => $this->config['recaptcha_api_server'],
			  'recaptcha_api_secure_server' => $this->config['recaptcha_api_secure_server'],
			  'recaptcha_verify_server' => $this->config['recaptcha_verify_server'],
			  'recaptcha_key_public' => $this->config['recaptcha_key_public'],
			  'recaptcha_key_private' => $this->config['recaptcha_key_private'],
			  'recaptcha_theme' => $this->config['recaptcha_theme'],
			  'recaptcha_lang' => $this->config['recaptcha_lang'],
			  'recaptcha_ssl' => $this->config['recaptcha_ssl'],
		);
	
		$this->recaptcha = base64_encode(serialize($params));     // Serialize the whole thing and make it "bullet proof"

		$info['rules'] = 'required|max_length[128]|xss_clean';
		$this->add($info);
		return $this;
	}
	
	function button($content, $nameid='', $kind='button', $atts=array())
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name']);	
		$info['content'] = $content;
		$info['type'] = 'button';
		$info['kind'] = $kind;
		$this->add($info);
		if ($kind == 'submit') $this->_submit_name[] = $this->_last_accessed;
		if ($kind == 'image') $this->_submit_name[] = $this->_last_accessed.'_x';		
		return $this;
	}
	
	function image($src, $nameid='submitimg', $atts=array())
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name']);
		$info['type'] = 'image';
		$info['src'] = $src;
		$this->add($info);
		$this->_submit_name[] = $this->_last_accessed.'_x';
		return $this;
	}	
		
	function submit($value='Submit', $nameid='submit', $atts=array())
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name']);		
		$info['type'] = 'submit';
		$info['value'] = $value;
		$this->add($info);
		$this->_submit_name[] = $this->_last_accessed;
		return $this;
	}
		
	function reset($value='Reset', $nameid='reset', $atts=array())
	{
		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name']);
		$info['type'] = 'reset';
		$info['value'] = $value;
		$this->add($info);
		return $this;
	}
	
	function span($content, $atts=array())
	{
		$info = $this->_make_info($atts);
		$info['type'] = 'span';
		$info['content'] = $content;
		$this->add($info);
		return $this;
	}
	
	function html($content)
	{
		$info = array();
		$info['type'] = 'html';
		$info['content'] = $content;
		$this->add($info);
		return $this;
	}

	function br($clear = FALSE)
	{
		$info = array();
		$info['type'] = 'br';
		$info['clear'] = $clear;
		$this->add($info);
		return $this;
	}

	function hr($clear = FALSE, $atts=array())
	{
		$info = $this->_make_info($atts);
		$info['type'] = 'hr';
		$info['clear'] = $clear;
		$this->add($info);
		return $this;
	}

	function space($clear = FALSE)
	{
		$info = array();
		$info['type'] = 'space';
		$info['clear'] = $clear;		
		$this->add($info);
		return $this;
	}

	/**
	 * Add Element To Form
	 * 
	 * Adds an element to the form
	 */
	function _add_element_to_form($el) 
	{
		$unique = $el->unique;
		$type = $el->type;
		$name = (isset($el->name)) ? $el->name : NULL;
		
		$this->config['unique'][] = $unique;
		$this->{$unique} = $el;		
		$this->_elements[] = array('unique'=>$unique, 'name'=>$name, 'type'=>$type);

		// create array of last accessed elements if it was a group of elements
		if ($this->_last_accessed_group)
		{
			if ( ! is_array($this->_last_accessed)) $this->_last_accessed = array();
			$this->_last_accessed[] = $unique;
		}
		else
		{
			$this->_last_accessed = $unique;		
		}
		
		if ($type == 'label') $this->_labels[$el->for] = $unique;

		return $this;
	}

	/**
	 * Get
	 * 
	 * Returns the form as string
	 */
	function get() 
	{
		if ($this->retrieved) show_error(FGL_ERR.'You cannot call $this->form->get() twice without using $this->form->clear() in between.');
		$this->_output='';
		$this->_close_tags();
		if ( ! $this->validated) $this->validate();
		if ($this->error_string) $this->errors = $this->config['error_string_open'].$this->error_string.$this->config['error_string_close'];
		$this->_do_onsuccess();
		$this->_elements_to_string();
		$form = $this->_get_form_open();

		$form .= $this->_output;
		$form .= form_close();
		$this->retrieved = TRUE;
		return $form;
	}

	/**
	 * Get Array
	 * 
	 * Returns the form as array
	 */
	function get_array() 
	{
		if ($this->retrieved) show_error(FGL_ERR.'You cannot call $this->form->get() twice without using $this->form->clear() in between.');	
		$this->_output='';
		$this->_close_tags();
		if ( ! $this->validated) $this->validate();
		if ($this->error_string) $this->errors = $this->config['error_string_open'].$this->error_string.$this->config['error_string_close'];
		$this->_do_onsuccess();
		$form = array();
		$form = $this->_elements_to_string(TRUE);
		foreach ($this->_aliases as $old=>$new) 
		{
			$form[$new] = $form[$old];
			$form[$new.'_error'] = $form[$old.'_error'];
			unset($form[$old]);
			unset($form[$old.'_error']);
		}
		$form['form_open'] = $this->_get_form_open();
		$form['form_close'] = form_close();
		$form['action'] = $this->action;
		$form['method'] = $this->method;
		$this->retrieved = TRUE;		
		return $form;
	}
	
	/**
     * Get Post Data
     *
     * Returns the submitted form post values as array
     */
    function get_post($xss_clean = FALSE)
    {
		if ($this->valid)
		{
	        $post_values = array();

	        foreach ($this->_elements as $el)
	        {
	            if ($el['name'])
	            {
	            	if (array_key_exists($el['name'], $this->_data))
	            	{
	            		// data is a file
	            		$value = $this->_data[$el['name']];
	            		
		                $post_values[$el['name']] = $value;	            		
	            	}
	            	else
	            	{
	            		// check if xss_clean is set in form config file
		                if ($this->config['xss_clean_post']) $xss_clean = TRUE;
		                
	            		// data is something else
		                $name = str_replace("[]", "", $el['name']);
		                $value = $this->CI->input->post($name, $xss_clean);

						// resolve array if only one value
		                if ($el['type'] == 'radio' && is_array($value))
		                {
		                    $value = $value[0];
		                }
		                
		                $post_values[$name] = $value;	            	
	            	}
	            }
	            
	        }

	        return $post_values;
		}
		
		return NULL;
    	} 	

	/**
	 * Get form_open()
	 * 
	 * Creates form opening tag
	 */
	function _get_form_open()
	{
		if (empty($this->action)) $this->action = $this->CI->uri->uri_string() . '/';
		$this->atts['method'] = $this->method;
		if ($this->multipart) return form_open_multipart($this->action, $this->atts);
		return form_open($this->action, $this->atts);
	}

	/**
	 * Close Tags
	 * 
	 * Closes previously opened columns and fieldsets
	 */	
	function _close_tags() 
	{
		if ($this->indented)
		{
			$this->html('</div><div style="clear:both"></div>');
		}
		
		if ($this->columns)
		{
			$this->html('</div><div style="clear:both"></div>');
		}
			
		if ($this->fieldsets) 
		{
			$info['type'] = 'fieldset_close';
			$el = new El($info, $this->config);
			$this->_add_element_to_form($el);
		}
	}

	/**
	 * Check Post
	 * 
	 * Checks if form was submitted
	 */				
	function _check_post() 
	{
		foreach ($this->_elements as $el)
        {
            if ($el['name']) 
            {
                $name	= str_replace('[]', '', $el['name']);

                if (isset($_POST[$name]))
                {
                    $this->_posted = TRUE;
                    break;
                }
            }
        }
	}

	/**
	 * Validate
	 * 
	 * Validates the form
	 */
	function validate() 
	{	
		$this->_check_post();
	
		if ($this->_posted) 
		{
			$this->CI->load->library('form_validation');

			// set validation rules for elements
			foreach ($this->_elements as $el)
			{
				if ($el['name'])
				{
					$name 		= $el['name'];
					$element 	= $el['unique'];
					$type 		= $el['type'];
					
					$label = ( ! empty($this->$element->label_text)) ? $this->$element->label_text : ucfirst($element);
					
					if ( ! empty($this->$element->rules))
					{
						if ( ! empty($this->$element->group_label)) $label = $this->$element->group_label;
						if ( ! empty($this->$element->err_label)) $label = $this->$element->err_label;
						$this->CI->form_validation->set_rules($name, $label, $this->$element->rules);					
					}
					else
					{
						$this->CI->form_validation->set_rules($name, $label);
					}
				}		
			}

			// run validation
			if ($this->CI->form_validation->run() == FALSE) $this->valid = FALSE;

			// re-populated form even though the validation might have passed above, we
			// need to re-populate the data if captchas, uploads or models fail below
			$errors = array();
			foreach ($this->_elements as $el)
			{
				if ($el['name'])
				{
					$name 		= $el['name'];
					$element 	= $el['unique'];
					$type 		= $el['type'];
					
					switch ($type)
					{
						case 'select':
						$selected = array();
						// loop through all options to get selected value(s)
						foreach ($this->$element->options as $key => $val)
						{
							if (is_array($val))
							{
								// loop through optgroup
								foreach ($val as $option_key => $val)
								{
									if (set_select($name, $option_key)) $selected[] = $option_key;
								}
							}
							else
							{
								if (set_select($name, $key)) $selected[] = $key;
							}
						}
						$this->$element->selected = $selected;
						break;
						
						case 'checkbox':
						$checked = set_checkbox($name, $this->$element->value);
						$this->$element->atts['checked'] = ($checked) ? TRUE : FALSE;
						//old: $this->$element->atts['checked'] = (is_array($checked)) ? in_array($this->$element->value, $checked) : $this->$element->value == $checked;
						break;
						
						case 'radio':
						$checked = set_radio($name, $this->$element->value);
						$this->$element->atts['checked'] = ($checked) ? TRUE : FALSE;						
						break;
						
						case 'submit':
						case 'reset':
						$this->$element->atts['value'] = $this->$element->value;
						break;
						
						case 'password':
						// clear password field
						$this->$element->atts['value'] = '';
						break;
						
						case 'hidden':
						case 'textarea':
						$this->$element->value = set_value($name);
						break;
						
						default:
						$this->$element->atts['value'] = set_value($name);
					}

					$error = form_error($name, $this->error_open, $this->error_close);
					if ($error) $errors[$name] = array($element, $error);
				}
			}
				
			// display element errors if $this->form_validation->run() failed
			if ($this->valid == FALSE) 
			{
				foreach ($errors as $element => $error)
				{
					$element = $error[0];
					$error = $error[1];
					$type = $this->$element->type;
					
					// replace RULE specific error message with ELEMENT specific error message
					if ( ! empty($this->$element->err_message)) $error = $this->error_open.$this->$element->err_message.$this->error_close;
	
					$this->$element->error = $error; // this adds the full error string (including error_open and error_close) to the element
					$message = str_replace($this->error_open, '', $error);
					$message = str_replace($this->error_close, '', $message);
					$this->$element->error_message = $message; // this adds the inline error to the element
					if ($type != 'checkbox' && $type != 'radio') $this->$element->atts['class'] = (isset($this->$element->atts['class'])) ? $this->$element->atts['class'].' '.$this->config['error_class'] : $this->config['error_class'];
					$this->error[] = $message;
					
					$this->error_array[$element] = $message;					
					$this->error_string .= $error;
				}				
			}			

			// validate captcha
			if ($this->captcha && ! $this->_valid_captcha($this->CI->input->post('captcha')))
			{
				$this->CI->lang->load('form', $this->lang);
				$this->add_error('captcha', $this->CI->lang->line('incorrect-captcha-sol'));
				$this->valid = FALSE;				
			}
			
			// validate recaptcha
			if ($this->recaptcha) 
			{
				if (!$this->_valid_recaptcha($this->recaptcha))
				{
					$this->CI->lang->load('form', $this->lang);
					
					if ($this->recaptcha_error) 
					{
						$this->recaptcha_response_field->recaptcha_error = $this->recaptcha_error;
						$this->add_error('recaptcha_response_field', $this->CI->lang->line($this->recaptcha_error));
						$this->valid = FALSE;
					}
				}
			}
			
			// upload file data
			$this->_upload_files();

			// only load models if all validation above passed
			if ($this->valid) $this->_load_model();

			// is form still valid after models?
			if ( ! $this->valid)
			{
				// unlink uploaded files if validation failed
				foreach ($this->_data as $el) 
				{
					unlink($el['full_path']);
				}
			}

			// validation complete			
			$this->validated = TRUE;
		}
		else
		{
			$this->valid = FALSE;
		}

		return $this;
	}

	/**
	 * Validate Captcha
	 * 
	 * Validates a captcha
	 */	
	function _valid_captcha($code)
	{
		// $code = $this->CI->input->post('captcha', TRUE);
		$time = $this->CI->session->flashdata('captcha_time');
		$word = $this->CI->session->flashdata('captcha_word');

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float) $usec + (float) $sec);

		if ($now - $time > $this->config['captcha']['expiration']) 
		{
			log_message ("debug", "Validation for Captcha expired.");
			return FALSE;
		}
		
		if ($code != $word) 
		{
			log_message ("debug", "Validation for Captcha differs.");
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Validate Recaptcha
	 * 
	 * Validates a recaptcha
	 */		
	function _valid_recaptcha($params)
	{
		$config = unserialize(base64_decode($params));

		$remoteip  	= $this->CI->input->ip_address();
		$response 	= $this->CI->input->post('recaptcha_response_field');
		$challenge 	= $this->CI->input->post('recaptcha_challenge_field');
		
		// discard spam submissions
		if ($challenge == NULL || strlen($challenge) == 0 || $response == NULL || strlen($response) == 0)
		{
			return FALSE;
		}

		$response = $this->_recaptcha_http_post(
			$config['recaptcha_verify_server'],
			"/verify",
			array (
			'privatekey' => $config['recaptcha_key_private'],
			'remoteip' => $remoteip,
			'challenge' => $challenge,
			'response' => $response
			)
		);

		$answers = explode ("\n", $response[1]);

		if (trim($answers[0]) == 'true')
		{
			return TRUE;
		}
		else
		{
			$this->recaptcha_error = $answers[1];
			return FALSE;
		}
	}
	
	/**
     * Submits an HTTP POST to a reCAPTCHA server
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * @return array response
     */
	function _recaptcha_http_post($host, $path, $data, $port = 80)
	{
        $req = $this->_recaptcha_qsencode ($data);

        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;
		
		$response = '';
		$fs = @fsockopen($host, $port, $errno, $errstr, 10);

		fwrite($fs, $http_request);
		while (!feof($fs))
		{
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}	
	
	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	function _recaptcha_qsencode($data) 
	{
        $req = "";
        foreach ($data as $key => $value) $req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

        // Cut the last '&'
        $req = substr($req, 0, strlen($req)-1);
        return $req;
	}

	/**
	 * Upload Files
	 * 
	 * Uploads files
	 */		
	function _upload_files() 
	{
		foreach ($this->_files as $el)
		{
			$config = array(
				'upload_path' => $this->$el->upload_path,
				'allowed_types' => $this->$el->allowed_types,
				'file_name' => $this->$el->file_name,
				'overwrite' => $this->$el->overwrite,
				'max_size' => $this->$el->max_size,
				'max_width' => $this->$el->max_width,
				'max_height' => $this->$el->max_height,
				'max_filename' => $this->$el->max_filename,
				'encrypt_name' => $this->$el->encrypt_name,
				'remove_spaces' => $this->$el->remove_spaces
			);

			$this->CI->load->library('upload');
			$this->CI->upload->initialize($config);
			$this->CI->lang->load('upload', $this->lang);
			
			if ( ! $this->CI->upload->do_upload($this->$el->name))
			{
				$error = $this->CI->upload->display_errors('','');

				if ( ! $this->$el->required && $error == $this->CI->lang->line('upload_no_file_selected')) 
				{
					// file not required and no file selected
				} 
				else 
				{
					// add error
					$this->add_error($el, $error);
					$this->valid = FALSE;
				}
			} 
			else 
			{
				$this->_data[$this->$el->name] = $this->CI->upload->data();
			}		
		}
	}

	/**
	 * Load Model
	 * 
	 * Loads the model(s)
	 */
	function _load_model() 
	{
		if ($this->_posted && count($this->model))
		{
			foreach ($this->model as $model)
			{
				// combine data provided with POST data (validated at this time)
				$model_data = array_merge($model['data'], $_POST);

				// add file upload data
				$model_data = array_merge($model_data, $this->_data);
				
				$this->CI->load->model($model['name']);
				
				// check if the model is in a subfolder
	            if (($last_slash = strrpos($model['name'], '/')) !== FALSE)
    	        {
        	        $model['name'] = substr($model['name'], $last_slash + 1);
            	}
            	
				$this->CI->{$model['name']}->{$model['method']}($this, $model_data); // $this = form library handle
			}
		}
	}
	
	/**
	 * Do on_success
	 * 
	 * Calls user function upon success
	 */	
	function _do_onsuccess() 
	{
		if ($this->_posted && $this->valid && $this->_onsuccess) 
		{
			foreach ($this->_onsuccess as $item)
			{
				if (is_array($item['values'])) 
				{
					foreach ($item['values'] as $key => $value)
					{
						$this->_do_onsuccess_matches($value, $item['values'][$key], $item['rules']);
					}
				} 
				else 
				{
					call_user_func_array($item['function'], array($item['values']));
				}
			}
		}
	}

	function _do_onsuccess_matches($string, &$fullstring, $rules) 
	{
		preg_match_all('/{([a-z_0-9]+)}/', $string, $matches);
		
		if ($matches[1]) 
		{
			foreach ($matches[1] as $match) 
			{
				$str = '{'.$match.'}';
				$value = $this->$match->value;
				
				if (array_key_exists($str, $rules) && $rules[$str]) 
				{
					foreach (explode(',', $rules[$str]) as $rule) 
					{
						$values = $this->_postprocess[$rule]['values'];
						$function = $this->_postprocess[$rule]['function'];
						if (!is_array($values)) $values = array($values);
						$key = array_search('$var', $values);
						$values[$key] = $this->$match->value;
						
						$value = call_user_func_array($function, $values);
						$fullstring = str_replace($str, $value, $fullstring);
					}
				} 
				else 
				{
					$fullstring = str_replace($str, $value, $fullstring);
				}
			}
		}
	}

	/**
	 * Elements To String
	 * 
	 * Utilized by get() and get_array() to generate output
	 */
	function _elements_to_string($strip=FALSE) 
	{
		$array = array();
		foreach ($this->_elements as $el)
		{
			$element = $el['unique'];
			$name = str_replace('[]', '', $el['name']);
						
			if ($strip && $name)
			{
				$this->$element->label_text = '';
				$this->$element->error = '';
				
				// checkgroups and radiogroups share the same 'name' attribute
				if (isset($array[$name])) 
				{
					if (!is_array($array[$name]) )
					{
						$old = $array[$name];
						$old_error = $array[$name.'_error'];

						unset($array[$name]);
						unset($array[$name.'_error']);

						$array[$name][] = $old;
						$array[$name.'_error'][] = $old_error;
					}

					$array[$name][] = $this->$element->get();
					$array[$name.'_error'][] = $this->$element->error_message;				
				}
				else
				{
					$array[$name] = $this->$element->get();
					$array[$name.'_error'] = $this->$element->error_message;				
				}
			}

			$this->_output .= $this->$element->get()."\n";
		}
		
		return $array;
	}	
	
	// --------------------------------------------------------------------------
	
	/**
	 * Action
	 * 
	 * Sets the form's action
	 */		
	function action($action) 
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * Add Class
	 * 
	 * Adds a class to the element
	 */	
	function add_class($name, $class='') 
	{
		if ($this->_last_accessed && !$class) 
		{
			$class = $name;
			$el = $this->_last_accessed;
		}
		else
		{
			$el = $this->_el_get_unique($name);
		}
		
		if ($el)
		{
			$this->$el->atts['class'] = (isset($this->$el->atts['class'])) ? ' '.$class : $class;
			$this->_last_accessed = $el;
		}
		else
		{
			show_error(FGL_ERR.'add_error: Element name "'.$name.'" does not exist');
		}
		
		return $this;
	}	

	/**
	 * Add Error
	 * 
	 * Adds an error to the form
	 */	
	function add_error($name, $message='')
	{		
		if ($this->_last_accessed && !$message)
		{
			$message = $name;
			$el = $this->_last_accessed;
		}
		else 
		{
			$el = $this->_el_get_unique($name);
		}

		if ($el)
		{
			$this->$el->error = $this->error_open.$message.$this->error_close;
			$this->$el->error_message = $message;
			if ($this->$el->type != 'checkbox' && $this->$el->type != 'radio') $this->$el->atts['class'] = (isset($this->$el->atts['class'])) ? $this->$el->atts['class'].' '.$this->config['error_class'] : $this->config['error_class'];
			$this->error[] = $message;
			$this->error_string .= $this->error_open.$message.$this->error_close;		
			$this->valid = FALSE;
		}
		else
		{
			show_error(FGL_ERR.'add_error: Element name "'.$name.'" does not exist');
		}
		
		return $this;
	}	

	/**
	 * Alias
	 * 
	 * Creates an alias for the variable name in get_array() output
	 */		
	function alias($name='', $alias='')
	{
		if (!$name && !$alias) show_error(FGL_ERR.'alias: No alias specified');
		
		$group = (!$alias && (is_array($name) || preg_match('/=/', $name))) ? TRUE : FALSE;
		 
		if ($group)
		{
			$groups = $this->_make_info($name);
			foreach ($groups as $name=>$alias)
			{
				$el = $this->_el_get_unique($name);
				if ($el)
				{
					$this->_build_alias($el, $alias);
				}
				else
				{
					show_error(FGL_ERR.'alias: Alias cannot be assigned. Element "'.$name.'" does not exist.');
				}			
			}
			
			return $this;
		}
		elseif ($this->_last_accessed && ! $alias)
		{
			$alias = $name;
			$el = $this->_last_accessed;
		}		
		else 
		{
			$key = $this->_el_search($name);
			$el = ($key) ? $this->_el_unique($key) : $name;
		}
		
		if (isset($this->$el->name))
		{
			$this->_build_alias($el, $alias);
		}
		else
		{
			show_error(FGL_ERR.'alias: Alias "' . $name . '" cannot be assigned to last accessed element.');
		}
		
		return $this;
	}	
	
	function _build_alias($el, $alias) 
	{
		if ( ! array_key_exists($alias, $this->_aliases) && ! $this->_el_name_exists($alias)) 
		{
			$name = str_replace("[]", "", $this->$el->name);
			$this->_aliases[$name] = $alias;
		}
		else
		{
			show_error(FGL_ERR.'alias: Alias cannot be assigned. An element with this name ("'.$alias.'") already exists.');
		}
	}

	/**
	 * Columns
	 * 
	 * Creates columns for display of fields
	 *
	 * ->col(150) creates a column with 150 pixel width
	 * ->col(0) resets columns
	 *
	 * if width is not set, it will be set to 'auto'
	 */
	function col($width=NULL, $float='left', $ta='')
	{
		if ($width || $width === NULL)
		{
			if ($this->columns) 
			{
				// close previous column
				$this->html('</div>');
				$this->columns--;
			}

			$width = ($width) ? $width.'px' : 'auto';
			$html = '<div style="float:'.$float.'; width:'.$width;
			if ($ta) $html .= '; text-align:'.$ta;
			$html .= '">';
			$this->html($html);
			$this->columns++;
		}
		elseif ($this->columns)
		{
			$this->html('</div><div style="clear:both"></div>');
			$this->columns--;
		}
		
		return $this;
	}

	/**
	 * Error Label
	 * 
	 * DEPRECATED!!! please use set_error_label() instead
	 */			
	function error_label($name='', $label='')
	{
		return $this->set_error_label($name, $label);
	}
	
	/**
	 * Error Message
	 * 
	 * DEPRECATED!!! please use set_error() instead
	 */			
	function error_message($name='', $message='')
	{
		return $this->set_error($name, $message);
	}

	/**
	 * Indent
	 * 
	 * Indents the following form fields with a floating div
	 *
	 * ->indent(40) sets indentation
	 * ->indent(0) resets indentation
	 */	
	function indent($width)
	{
		if (!is_numeric($width)) show_error(FGL_ERR.'indent: Value must be numeric');
		
		if ($this->indented) 
		{
			$this->html('</div><div style="clear:both"></div>');
			$this->indented--;
		}
		
		if ($width)
		{
			$this->html('<div style="float:left;width:'.$width.'px">&nbsp;</div><div style="float:left">');
			$this->indented++;
		}
		
		return $this;
	}

	/**
	 * Lang
	 * 
	 * Sets the language for the form validation class
	 */	
	function lang($lang) 
	{
		$this->lang = $lang;
		return $this;
	}

	/**
	 * Margin
	 * 
	 * Sets 'margin' for an element
	 */		
	function margin($name='', $margin='', $pos='')
	{
		if ($this->_last_accessed && (!$margin || ($margin && !$pos)))
		{
			if (!$name) show_error(FGL_ERR.'margin: No margin value specified');
		
			$pos = $margin;
			$margin = $name;
			$el = $this->_last_accessed;
		}
		elseif (!is_numeric($name))
		{
			if (!$name) show_error(FGL_ERR.'margin: No element name specified');
			if (!$margin) show_error(FGL_ERR.' margin: No margin value specified');		
		
			$el = $this->_el_get_unique($name);
		}

		// if name attribute exists
		if ($el)
		{
			if (!is_numeric($margin)) show_error(FGL_ERR.'margin: Value must be numeric.');
			$this->$el->add_margin($margin, $pos);
		}
		else
		{
			show_error(FGL_ERR.'margin: Element "'.$name.'" could not be accessed');
		}
		
		return $this;
	}	
	
	/**
	 * Method
	 * 
	 * Sets the form's send method
	 */			
	function method($method='')
	{
		if ($method != 'post' && $method != 'get') show_error("method: Method must be either 'post' or 'get'");
		$this->method = $method;
		return $this;
	}

	/**
	 * Model
	 * 
	 * Passes the validated form data to a model
	 */		
	function model($model='', $method='index', $data=array())
	{
		if ( ! $model) show_error("model: No model specified");

		$data = $this->_make_array($data);
		
		$this->model[] = array(
			'name' => $model,
			'method' => $method,
			'data' => $data
		);
		
		return $this;
	}
	
	function onsuccess($func, $vals, $rules=array()) 
	{
		$this->_onsuccess[] = array(
			'function'	=> $func,
			'values'	=> $vals,
			'rules'		=> $rules
		);
		return $this;
	}		
	
	function postprocess($array) 
	{
		$this->_postprocess = array_merge($this->_postprocess, $array);
		return $this;
	}

	/**
	 * Remove
	 * 
	 * Removes an element from the form
	 */		
	function remove($name='') 
	{
		if (!$name) show_error(FGL_ERR.'remove: No element name specified');

		$key = $this->_el_search($name);
		$el = ($key) ? $this->_el_unique($key) : $name;		
		
		unset($this->$el);
		unset($this->_elements[$key]);
		
		$this->_last_accessed = NULL;
		
		return $this;
	}
	
	/**
	 * Remove Attribute
	 * 
	 * Removes an attribute from the element
	 */		
	function rem_att($name='', $att='')
	{
		if ($this->_last_accessed && !$att)
		{
			if (!$name) show_error(FGL_ERR.'rem_att: No attribute specified');

			$att = $name;
			$el = $this->_last_accessed;
		}
		else
		{
			if (!$name) show_error(FGL_ERR.'rem_att: No element name specified');
			if (!$att) show_error(FGL_ERR.'rem_att: No attriute specified');		
		
			$el = $this->_el_get_unique($name);
		}
		
		if ($el) 
		{
			if (isset($this->$el->atts[$att])) unset($this->$el->atts[$att]);
			if (isset($this->$el->$att)) unset($this->$el->$att);
		}
		else
		{
			show_error(FGL_ERR.'rem_att: Element "'.$name.'" could not be accessed');
		}
		
		return $this;
	}

	/**
	 * Remove Class
	 * 
	 * Removes a class from the element
	 */		
	function rem_class($name='', $class='')
	{
		if ($this->_last_accessed && !$class) 
		{
			if (!$name) show_error(FGL_ERR.'rem_class: No class specified');
		
			$class = $name;
			$el = $this->_last_accessed;
		}
		else
		{
			if (!$name) show_error(FGL_ERR.'rem_class: No element name specified');
			if (!$class) show_error(FGL_ERR.'rem_class: No class specified');		
		
			$el = $this->_el_get_unique($name);
		}
		
		if ($el)
		{
			$this->$el->atts['class'] = preg_replace('/ *'.$class.'/', '', $this->$el->atts['class']);
			if (empty($this->$el->atts['class'])) unset($this->$el->atts['class']);		
		}
		
		return $this;
	}

	/**
	 * Set Attribute
	 * 
	 * Sets an attribute for the element
	 */		
	function set_att($name='', $att='', $value='')
	{
		if ($this->_last_accessed && !$value) 
		{
			if (!$name) show_error(FGL_ERR.'set_att: No attribute specified');
			if (!$att) show_error(FGL_ERR.'set_att: No value specified');		
		
			$value = $att;
			$att = $name;
			$el = $this->_last_accessed;
		}
		else
		{
			if (!$name) show_error(FGL_ERR.'set_att: No element name specified');
			if (!$att) show_error(FGL_ERR.'set_att: No attribute specified');
			if (!$value) show_error(FGL_ERR.'set_att: No value specified');		

			$el = $this->_el_get_unique($name);
		}
		
		if ($el)
		{
			$this->$el->atts[$att] = $value;		
		}
		
		return $this;
	}
	
	/**
	 * Set Error Message
	 * 
	 * Sets a custom error message for the element
	 *
	 * {label} can be used as placeholder for the element's label
	 */			
	function set_error($name='', $message='')
	{
		if ($this->_last_accessed && !$message) 
		{
			if (!$name) show_error(FGL_ERR.'error_message: No message specified');
			
			$message = $name;
			$el = $this->_last_accessed;
		}
		else 
		{
			if (!$name) show_error(FGL_ERR.'error_message: No element name specified');
			if (!$message) show_error(FGL_ERR.'error_message: No message specified');
						
			$el = $this->_el_get_unique($name);
		}

		if ($el)
		{
			$label = ( ! empty($this->$el->label_text)) ? $this->$el->label_text : ucfirst($this->$el->name);
			$message = str_replace('{element}', $this->$el->label_text, $message); // for downwards compatibility
			$this->$el->err_message = str_replace('{label}', $this->$el->label_text, $message);
		}
		else
		{
			show_error(FGL_ERR.'error_message: Element "'.$name.'" could not be accessed');
		}
		
		return $this;
	}	
	
	/**
	 * Set Error Label
	 * 
	 * Sets an error label for an element (in case no element label was specified)
	 */			
	function set_error_label($name='', $label='')
	{
		if ($this->_last_accessed && !$label) 
		{
			if (!$name) show_error(FGL_ERR.'error_label: No label specified');
		
			$label = $name;
			$el = $this->_last_accessed;
		}
		else 
		{
			if (!$name) show_error(FGL_ERR.'error_label: No element name specified');
			if (!$label) show_error(FGL_ERR.'error_label: No label specified');
		
			$key = $this->_el_search($name);
			$el = ($key) ? $this->_el_unique($key) : $name;
		}

		if ($el)
		{
			$this->$el->err_label = $label;
		}
		else
		{
			show_error(FGL_ERR.'error_label: Element name "'.$name.'" does not exist');
		}
		
		return $this;
	}	

	/**
	 * Set Rule
	 * 
	 * Sets a rule for an element
	 */	
	function set_rule($name='', $rule='')
	{
		// TBD	
	}

	/**
	 * Set Value
	 * 
	 * Sets a value for an element.
	 * Value can be empty string, NULL, FALSE or 0 and still be valid
	 */	
	function set_value($name='', $value='')
	{
		$params = func_num_args();
		
		if ($this->_last_accessed && $params == 1)
		{
			if ( ! $name) show_error(FGL_ERR.'set_value: No value specified');
		
			$value = $name;
			$el = $this->_last_accessed;
		}
		else
		{
			if ( ! $name) show_error(FGL_ERR.'set_value: No element name specified');

			$el = $this->_el_get_unique($name);		
		}
		
		if ($el) 
		{
			switch ($this->$el->atts['type'])
			{
                case 'hidden':
				case 'textarea':
                $this->$el->value = $value;
				break;
				
                case 'select':
                    $this->$el->selected = $this->_make_array($value);
                    break;

                case 'checkbox':
                case 'radio':
                	// get all unique elements with this name
                	$keys = $this->_el_search($name, TRUE);
            	
                	foreach ($keys as $key) 
                	{
                		$el = $this->_el_unique($key);
                		$this->$el->atts['checked'] = (in_array($this->$el->value, $this->_make_array($value))) ? TRUE : FALSE;
                	}
                    break;

                default:
                    $this->$el->atts['value'] = $value;
            } 
		}
		
		return $this;
	}	
	
	/**
	 * Trigger
	 * 
	 * Set a submit trigger if no submit button is
	 * included in the form. Whenever this trigger
	 * element is posted, the form is considered submit
	 */	
	function trigger($name='')
	{
		if ( ! $name) show_error(FGL_ERR.'trigger: No element name specified');
		$this->_submit_name[] = $name;
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Element Name Exists
	 * 
	 * Checks if the element has a 'name' attribute
	 */
	function _el_name_exists($unique) 
	{
		foreach ($this->_elements as $v) 
		{
			if ($v['unique'] == $unique && !empty($v['name'])) return TRUE;
		}
	}

	/**
	 * Element Search
	 * 
	 * Searches all elements by 'name' and returns the key of the last occurrence 
	 * of an element with this name (important for checkgroups etc.)
	 * 
	 * If $all is set to TRUE the function returns an array with all elements found
	 */
	function _el_search($name, $all=FALSE) 
	{
		$i = 0;
		$keys = array();
		
		foreach ($this->_elements as $el) 
		{
			if ($el['name'] == $name || $el['name'] == $name.'[]') 
			{
				$key = $i;
				$keys[] = $i;
			}
			
			$i++;
		}

		if (isset($key))
		{
			if ( ! $all) return $key;
			return $keys;
		}
		
		return FALSE;
	}	

	/**
	 * Element Unique
	 * 
	 * Returns the 'unique' identifier by element key
	 */	
	function _el_unique($key) 
	{
		return $this->_elements[$key]['unique'];
	}

	/**
	 * Element Get Unique
	 * 
	 * Returns the 'unique' identifier by element 'name'
	 */	
	function _el_get_unique($name) 
	{
		$key = $this->_el_search($name);
		if ($key!==FALSE) return $this->_elements[$key]['unique'];
		return FALSE;
	}
	
}

/* End of file Form.php */
/* Location: ./application/libraries/Form.php */