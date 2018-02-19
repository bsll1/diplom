<?php
$vo_description_t = isset($vo_description) ? $vo_description : "";
$vo_mainurl_t     = isset($vo_mainurl)     ? $vo_mainurl     : "";
$defaultca_t      = isset($defaultca)      ? $defaultca      : "";
$vo_rules_link_t  = isset($vo_rules_link)  ? $vo_rules_link  : "";
$vo_registration_enabled_t = isset($vo_registration_enabled) ? $vo_registration_enabled : 1;

// POST processing
if ( isset($_POST["pref_sub"]) ) if ( isACLallow($pvap,"preferences","w")) {
	$vo_description_t = _ispost("vo_descr");
	$vo_mainurl_t     = _ispost("vo_url");
	$vo_rules_link_t  = _ispost("vo_usageurl");
	$defaultca_t      = _ispost("vo_defca");
	$vo_registration_enabled_t = _ispost("vo_registration");
	if ( empty($vo_rules_link_t) )
		printf("<p class=\"error\">%s</p>",$voms_preferences_badrequired);
	else {
		if ( _invoke_transactional_sql(1,'update_vo_config', 
				$vo_description_t,$vo_mainurl_t,$vo_rules_link_t,$defaultca_t, $vo_registration_enabled_t) ) 
			printf("<p class=\"alldone\">%s</p>",$voms_preferences_updated);
		else printf("<p class=\"error\">%s</p>",$voms_preferences_updatefail);
	}
}

echo "<div id=\"attrsform\">\n";
	printf("<p class=\"underborder bold\" >%s</p>\n",sprintf($voms_preferences_caption,$vo));
	foreach ( $voms_preferences_display_info as $text ) printf("<p>%s</p>\n",$text);
	if ( isACLallow($pvap,"preferences","r") ) { 
		if ( isACLallow($pvap,"preferences","w") ) {
			echo "<form method=\"post\" action=\"\">\n";
			printf("<label>%s</label><input type=\"text\" name=\"vo_descr\" value='%s' /><br/><br/>\n",
				$voms_preferences_vodescr,$vo_description_t);
			printf("<label>%s</label><input type=\"text\" name=\"vo_url\" value='%s' /><br/><br/>\n",
				$voms_preferences_vourl,$vo_mainurl_t);
			printf("<label>%s</label><input type=\"text\" name=\"vo_usageurl\" value='%s' /><br/><br/>\n",
				$voms_preferences_ugaseurl,$vo_rules_link_t);
			printf("<label>%s</label><select style=\"width: 500px;\" name=\"vo_defca\">",$voms_preferences_defca);
			foreach ( getCAList() as $caname ) {
				printf("<option value=\"%s\"", $caname);
				if ( $caname === $defaultca_t ) echo " selected=\"selected\" ";
				printf(">%s</option>\n", $caname);
			}
			echo "</select><br/><br/>\n";
			printf("<label>%s</label><select name=\"vo_registration\" >", $voms_preferences_registration);
			if ( $vo_registration_enabled_t ) {
				printf("<option value=\"1\" selected=\"selected\" >%s</option>", $voms_preferences_registration_enabled);
				printf("<option value=\"0\" >%s</option>", $voms_preferences_registration_disabled);
			} else {
				printf("<option value=\"1\" >%s</option>", $voms_preferences_registration_enabled);
				printf("<option value=\"0\" selected=\"selected\" >%s</option>", $voms_preferences_registration_disabled);
			}
			echo "</select><br/><br/>\n";
			
			printf("<input class=\"crnewbt\" type=\"submit\" name=\"pref_sub\" value=\"%s\" />\n", $voms_preferences_submit);
			echo "</form>\n";
		} else {
			printf("<p><label>%s</label>%s<br/><br/></p>\n",$voms_preferences_vodescr,$vo_description_t);
			printf("<p><label>%s</label><a href=\"%s\">%s</a><br/><br/></p>\n",$voms_preferences_vourl,$vo_mainurl_t,$vo_mainurl_t);
			printf("<p><label>%s</label><a href=\"%s\">%s</a><br/><br/></p>\n",
				$voms_preferences_ugaseurl,$vo_rules_link_t,$vo_rules_link_t);
			printf("<p><label>%s</label>%s<br/><br/></p>\n",$voms_preferences_defca,$defaultca_t);
			printf("<p><label>%s</label>%s<br/><br/></p>\n",
				$voms_preferences_registration,
				($vo_registration_enabled_t)?$voms_preferences_registration_enabled:$voms_preferences_registration_disabled);
		}
	}
echo "</div>\n";
?>
