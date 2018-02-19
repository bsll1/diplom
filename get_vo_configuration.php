<?php
$isfailed = 0;
$vo = ( isset($_GET["vo"]) ) ? $_GET["vo"] : 0;
if ( $vo !== 0 ) {
	$conffile = $pva_install_path . "/conf/vomses/" . $vo . ".conf";
	if ( is_readable($conffile) ) {
		include($conffile);
		require("sql.php");
		getSettingFromDB();
	} else {
		$isfailed = 1;
	}
}
// VO default CA redefine
if (! isset($defaultca)) $defaultca = "";
?>
