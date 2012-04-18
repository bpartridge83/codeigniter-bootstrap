<?php

/*
|--------------------------------------------------------------------------
| Combine / Minify / Less
|--------------------------------------------------------------------------
|
| Flags whether files should be combined or minified, and css parsed with less.
|
*/

$config['assets']['version'] = 12;

$config['assets']['env']             = 'production'; // Environment (if it's set to 'dev', no processing will be done)
$config['assets']['combine']         = true;
$config['assets']['minify_js']       = false;
$config['assets']['minify_css']      = true;
$config['assets']['less_css']        = true;
$config['assets']['process_imports'] = true;

/*
|--------------------------------------------------------------------------
| Cache
|--------------------------------------------------------------------------
|
| Define if the cache folder should be cleared when generating new cache files
| 
*/

$config['assets']['auto_clear_cache']     = true;
$config['assets']['auto_clear_css_cache'] = false;
$config['assets']['auto_clear_js_cache']  = false;

/*
|--------------------------------------------------------------------------
| Default paths and directories, tags
|--------------------------------------------------------------------------
|
| Default directories containing the assets
| Option to use HTML5 tags
|
*/

$config['assets']['assets_dir'] = 'web';
$config['assets']['js_dir']     = 'js';
$config['assets']['css_dir']    = 'css';
$config['assets']['cache_dir']  = 'cache';
$config['assets']['html5']      = true;


