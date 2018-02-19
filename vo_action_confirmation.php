<?php
$vo_registration_enabled = isset($vo_registration_enabled) ? $vo_registration_enabled : 1;
echo "<div id=\"submitform\">\n";
if ( ! $vo_registration_enabled ) {
        printf( "<p class=\"error\">%s</p>", $voms_register_disabled);
} else if ( ! checkDBSchema() ) {
	printf( "<p class=\"error\">%s</p>", $voms_dbschema_usernotify);
} else if ( isset($_GET["u"]) && isset($_GET["c"]) ) {
	$aord = $_GET["u"];
	$ccode = substr($_GET["c"], 0, 36);
	$reqid = substr($_GET["c"], 36);
    $is_service = _isdef($_GET["s"], 0);

	printf("<p class=\"underborder bold\" >%s</p>\n",$voms_confirmation_title);
	if ( $aord == 0 ) {
	// check already configmed request
	    if (  getReqInfo ( $reqid ) ) printf("<p class=\"error\">%s</p>", $voms_confirmation_already); else {
		// confirm request
		$cres = _invoke_transactional_sql(1,'confirmRegRequest',$reqid,$USER_DN,$USER_CA,$ccode,$is_service);
		if ( $cres ) {
			$adm_contacts = getAdminContacts( "requests", "w" );
			$vo_url = "https://" . $_SERVER["HTTP_HOST"] .
				preg_replace("/\/index.php/","",$_SERVER["SCRIPT_NAME"]) .
				"?vo=" . $vo;
				$reqinfo = getReqInfo($reqid);
			$base_url = "https://".$_SERVER["HTTP_HOST"].preg_replace("/\/index.php/","",$_SERVER["SCRIPT_NAME"])."?vo=".$vo;
			foreach ( $adm_contacts as $adminfo ) {
				send_adminnotify_mail($vo, $base_url, $adminfo["cn"], $adminfo["mail"], $reqinfo);
			}
			printf("<p>%s</p>", $voms_confirmation_ok);
		} else printf("<p class=\"error\">%s</p>", $voms_confirmation_error);
	    }
	} else if ( $aord == 1 ) {
		// delete request
		if (_invoke_transactional_sql(1,'deleteRegRequest',$reqid,$USER_DN,$USER_CA,$ccode))
			printf("<p>%s</p>", $voms_confirmation_delete);
		else printf("<p class=\"error\">%s</p>", $voms_confirmation_error);
	} else printf("<p class=\"error\">%s</p>", $voms_confirmation_badrequest);
} else hackexit(1);
echo "<div class=\"mt20\"></div>\n";
echo "</div>\n";
?>
