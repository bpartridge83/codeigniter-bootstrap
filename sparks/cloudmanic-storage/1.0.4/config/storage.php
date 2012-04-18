<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Amazon S3
$config['storage']['s3_access_key'] = '';
$config['storage']['s3_secret_key'] = '';

// Rackspace Cloud Files 
$config['storage']['cf_username'] = 'bpartridge';
$config['storage']['cf_api_key'] = '6004b17624cf52758587e75b428415cd';
$config['storage']['cf_auth_url'] = array('library' => 'Rackspace_Cf_Url', 'method' => 'get_url');

$config['storage']['env'] = array(
    'production' => array(
        'container' => 'assets',
        'cdn_url' => 'http://c4256203.r3.cf2.rackcdn.com',
        'ttl' => 60 * 60 * 24 * 7
    ),
    /*
    'development' => array(
        'container' => 'assets',
        'cdn_url' => 'http://c4256203.r3.cf2.rackcdn.com',
        'ttl' => 10
    )
    */
);

/* End File */