<?php

// http://stackoverflow.com/questions/255511/php-echo-line-breaks/255531#255531
if (! defined('PHP_EOL')) { // defined since PHP 4.3.10 and PHP 5.0.2 (i.e. this will most likely already be defined)
	switch (strtoupper(substr(PHP_OS, 0, 3))) {
		// Windows
		case 'WIN':
			define('PHP_EOL', "\r\n");
			break;
		// Mac
		case 'DAR':
			define('PHP_EOL', "\r");
			break;
		// Unix
		default:
			define('PHP_EOL', "\n");
	}
}

?>