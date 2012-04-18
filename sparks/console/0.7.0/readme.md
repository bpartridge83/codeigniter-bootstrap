The console library is a implemenation of the firephp class (http://www.firephp.org).
It allows you to send log message from the server that display in the firebug console (when the firephp plugin is installed)

### Usage

	// load the spark
	$this->load->spark('console/[version]');

	// ($type, $message, $write_to_file);
	$this->console->log('this will show an error in firebug', 'error', FALSE);

	// or use the alias console_log()
	console_log('This is my log message', 'log', FALSE);
	console_log('This is my error message, also written to a log file', 'error', TRUE);

	// set the log path or file name
	$this->console->log_path = APPATH.'logs/';
	$this->console->log_file = 'console-'.date('y-m-d h-i-s.php');

To disable the firebug headers(logs will still be written to file), change this setting in config/console.php

	$config['console_active'] = FALSE;


Supported log types are:

- log
- info
- warn
- error

There do seem to be limitations with how big the objects/arrays that you log out are - of your page is not refreshing properly, thats probably the problem.