<?php
$enabling_replication = 0;
$repl_dn   = _ispost("repl_dn");
$repl_ca   = _ispost("repl_ca");
$repl_endp = _ispost("repl_endpoint");
$adj_code  = _ispost("adj_code");
$repl_code = _ispost("repl_code");
$repl_int  = _ispost("r_int");
$adj_id    = _ispost("adj_id");

if ( isset($_POST["r_int"]) ) if ( isACLallow($pvap,"preferences","w")) {
	set_transactions_cron($vo, $repl_int);
}

if ( isset($_POST["new_adj"]) ) if ( isACLallow($pvap,"preferences","w")) {
	$enabling_replication = 1;
	if ( create_id2uuid_data() ) {
		if ( add_new_replicant($repl_dn,$repl_endp,$adj_code,$repl_code,$repl_ca) ) {
			set_transactions_cron($vo, 30);
			printf("<p class=\"alldone\">%s</p>",$voms_preferences_repl_added);
			$enabling_replication = 0;
		}
	} else printf("<p class=\"error\">%s</p>",$voms_preferences_repl_id2uuid_failed);
}

if ( isset($_POST["edit_repl_code"])) if ( isACLallow($pvap,"preferences","w")) {
	if ( update_replicant($uid, $repl_code) ){
		printf("<p class=\"alldone\">%s</p>",$voms_preferences_repl_changed);
	}
}

if ( isset($_POST["adjconfirm"]) ) if ( isACLallow($pvap,"preferences","w")) {
	confirm_replicant($uid);
}

if ( isset($_POST["repliation_en"]) ) if ( isACLallow($pvap,"preferences","w")) {
	$enabling_replication = 1;
}

if ( isset($_POST["adj_delete"]) ) if ( isACLallow($pvap,"preferences","w")) {
	if ( del_authorized_updator($adj_id) ) {
		printf("<p class=\"alldone\"></p>",$voms_preferences_repl_adj_remove_ok);
		$uid = 0;
	} else printf("<p class=\"error\">%s</p>",$voms_preferences_repl_adj_remove_fail);
}

printf("<p class=\"underborder bold\" >%s</p>\n",sprintf($voms_preferences_repl_caption,$vo));
echo "<div id=\"attrsform\">\n";
if ( $enabling_replication ) {
	if ( ! isset($enable_transactions_log) ) $enable_transactions_log = false;
	if ( $enable_transactions_log ) {
		show_add_new_replicant_wizard($repl_dn,$repl_endp,$adj_code,$repl_code,$repl_ca);
	} else printf("<p class=\"error\">%s</p>",$voms_preferences_repl_trans_failed);
} else if ( $uid ) {
	show_replication_adj_details($uid,$pvap);
} else if ( isACLallow($pvap,"preferences","r") ) {
	echo "<form name=\"rst\" method=\"post\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"repliation_en\" value='1' />\n";
	echo "</form>\n";

	printf("<p>%s",$voms_preferences_repl_status);
	if ( get_replication_status() ) {
		printf("%s.</p>\n",$voms_preferences_repl_status_enabled);
		// replication cron interval
		if ( ! isset($replication_interval) ) $replication_interval = 30;
		echo "<form name=\"rint\" method=\"post\" action=\"\">\n";

		if ( isACLallow($pvap,"preferences","w")) {
			if ( isset($_POST["change_r_int"]) ) {
				$intervals_dropdown = "<select name=\"r_int\">\n";
			} else {
				$intervals_dropdown = "<select name=\"r_int\" disabled=\"disabled\">\n";
				echo "<input type=\"hidden\" name=\"change_r_int\" value='1' />\n";
			}

			foreach ( $voms_preferences_repl_intervals as $rint => $capt ) {
				$isselected = ($replication_interval == $rint)?"selected=\"selected\"":"";
				$intervals_dropdown .= sprintf("<option value=\"%s\" %s>%s</option>\n",
					$rint, $isselected, $capt);
			}
			$intervals_dropdown .= "</select>\n";
		} else $intervals_dropdown = $voms_preferences_repl_intervals[$replication_interval] . ".";

		if ( isACLallow($pvap,"preferences","w") ) {
			$submit_link = sprintf("<a href=\"javascript:document.rint.submit();\">%s</a>",
				(isset($_POST["change_r_int"]))?$voms_preferences_repl_submit:$voms_preferences_repl_edit);
			printf("<p>%s%s  (%s)</p>\n", $voms_preferences_repl_interval_caption, $intervals_dropdown, $submit_link);
		} else  printf("<p>%s%s</p>\n", $voms_preferences_repl_interval_caption, $intervals_dropdown);

		echo "</form>\n";
		show_replicants_table();
		if ( isACLallow($pvap,"preferences","w") ) {
			printf("<br/><a href=\"javascript:document.rst.submit();\">%s</a>\n",$voms_preferences_repl_new_adj);
		}
	} else if ( isACLallow($pvap,"preferences","w") ) {
		printf("%s. (<a href=\"javascript:document.rst.submit();\">%s</a>)</p>\n",
			$voms_preferences_repl_status_disabled, $voms_preferences_repl_status_enable);
	} else printf("%s. </p>\n", $voms_preferences_repl_status_disabled);
}
echo "</div>\n";
?>
