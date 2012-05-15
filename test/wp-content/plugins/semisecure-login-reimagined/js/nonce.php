<?php

// set WordPress' default error reporting level (for now, protects against E_STRICT)
if ( defined('E_RECOVERABLE_ERROR') )
	error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
else
	error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);

// start output buffering
ob_start();

if (isset($_GET['disable']))
	$nonce = '';
else {
	@session_start();
	require_once(dirname(dirname(__FILE__)).'/classes/SemisecureLoginReimagined.php');
	$nonce_session_key = SemisecureLoginReimagined::nonce_session_key();
	$nonce = $_SESSION[$nonce_session_key];
}

// make sure this is served as UTF-8
header('Content-Type: text/html; charset=UTF-8');

// load the nonce into a JavaScript variable if "js" is set on the query-string
if (isset($_GET['js'])) :
?>
var SemisecureLoginReimagined_nonce = '<?php echo addslashes($nonce); ?>';
<?php
else :

// otherwise just return the nonce directly
echo $nonce;

endif;
?>