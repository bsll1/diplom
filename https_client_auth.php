<?php
require_once("acl.php");
require_once("operations.php");
// Default all to unauthenticated
$member = 0;
$permissions = 0;
$USER_DN = 0;
$USER_CA = 0;
// Set unauth user name (web access only, disable on SOAP)
if ( defined('NO_GET_PERMISSIONS') ) {
	$voms_str_unauth = "";
	$voms_str_nonhttps = "";
} else $user = $voms_str_unauth;

// Check https enabled and user authentication parameters
if (isset ($_SERVER['HTTPS'])) {
	// Certificate signature check
	if ($_SERVER['SSL_CLIENT_VERIFY'] == "SUCCESS") {
		// Get credentials
		$USER_DN = (isset ($_SERVER['SSL_CLIENT_S_DN'])) ? normalizeDN($_SERVER['SSL_CLIENT_S_DN']) : 0;
		$USER_CA = (isset ($_SERVER['SSL_CLIENT_I_DN'])) ? normalizeDN($_SERVER['SSL_CLIENT_I_DN']) : 0;
		// Get user common name
		if ( ! defined('NO_GET_PERMISSIONS') ) $user = CNfromDN($USER_DN);
	}
} else if ( ! defined('NO_GET_PERMISSIONS') ) {
	// Generate link to access same page via https to show instead of user name
	$user = sprintf("<a href=\"https://%s\">%s</a>", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], $voms_str_nonhttps);
}

// Get VO user permissions
if (( $vo !== 0 ) && ($isfailed == 0)) {
	$member = checkMember ( $USER_DN, $USER_CA );
	if ( ! defined('NO_GET_PERMISSIONS') ) $permissions = getProperUserACL ( $USER_DN, $USER_CA, $member );
}

// Decode permissions to format used by ACL checkers
if ( ! defined('NO_GET_PERMISSIONS') ) $pvap = decodeACLPermissions($permissions);

?>
