<?php
$reread_cert_data = 0;
if ( isACLallow($pvap,"preferences","w")) $reread_cert_data = _ispost("reread_cert_data");

echo "<div id=\"configuration\" >\n";
	printf("<p class=\"underborder\">%s</p>\n",$voms_configuration_caption);
	// PHP VOMS-Admin URL
	printf("<p>%s</p>", $voms_configuration_pvaurl);
	$vo_url = $_SERVER["HTTP_HOST"] .
		preg_replace("/\/index.php/","",$_SERVER["SCRIPT_NAME"]) .
		"/" . $vo;
	printf("<div class=\"bgodd\"><pre>%s</pre></div>", "https://".$vo_url);
	// VOMSES String
	if ( isACLallow($pvap,"preferences","w")) {
		echo "<form name=\"rcr\" method=\"post\" action=\"\">\n";
		echo "<input type=\"hidden\" name=\"reread_cert_data\" value='1' />\n";
		echo "</form>\n";
		printf("<p>%s <span style=\"font-weight: normal;\">(<a href=\"javascript:document.rcr.submit();\">%s</a>)</span></p>", 
			$voms_configuration_vomses, $voms_configuration_reread_cert);
	} else printf("<p>%s</p>", $voms_configuration_vomses);
	echo "<div class=\"bgodd\"><pre>";
	foreach ( get_vomses($reread_cert_data) as $vomses_string ) echo $vomses_string . "\n";
	echo "</pre></div>";
    // .LSC configuration
	printf("<p>$voms_configuration_lsc</p>", $vo_host);
	echo "<div class=\"bgodd\"><pre>";
    echo get_lsc_chain();
    echo "</pre></div>";
	// mkdridmap configuration
	$updators_arr = get_authorized_updators(2);
	printf("<p>%s</p>", $voms_configuration_mkgridmap);
	printf("<div class=\"bgodd\"><pre>group %s .%s<br/>","voms://".$vo_url, $vo);
	if ($updators_arr)
	foreach ( $updators_arr as $upd ) {
		printf("group %s .%s<br/>","voms://".$upd['endpoint']."/voms/".$vo, $vo);
	}
	echo "</pre></div>";
	// ARC VO configuration block for the nordugridmap utility
	printf("<p>%s</p>", $voms_configuration_voarc);
	printf("<div class=\"bgodd\"><pre>[vo]\nvo=\"%s\"\nsource=\"%s\"\n",$vo,"vomss://".$vo_url);
	if ($updators_arr)
	foreach ( $updators_arr as $upd ) {
		printf("source=\"%s\"\n","vomss://".$upd['endpoint']."/voms/".$vo);
	}
	printf("mapped_unixid=\".%s\"\nfile=\"/etc/grid-security/grid-mapfile\"</pre></div>\n",$vo);
echo "<div class=\"mt20\"></div>\n";
echo "</div>\n";
?>
