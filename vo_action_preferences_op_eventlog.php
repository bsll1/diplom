<?php
	// taking notice of the event
	if ( $uid ) if ( isACLallow($pvap,"preferences","w")) {
		if ( removeLogRecords(array($uid)) ) printf("<p class=\"alldone\">%s</p>",$voms_preferences_log_take_notice_ok);
		else printf("<p class=\"error\">%s</p>",$voms_preferences_log_take_notice_failed);
	}

	if ( isset($_GET['ids']) ) if ( isACLallow($pvap,"preferences","w")) {
		$ids_arr = array ();
		if ( preg_match($regex_cs_digits,$_GET['ids']) ) 
			$ids_arr = preg_split('/,/',$_GET['ids'], -1, PREG_SPLIT_NO_EMPTY);
		if ( removeLogRecords($ids_arr) ) printf("<p class=\"alldone\">%s</p>",$voms_preferences_log_take_notice_ok);
		else printf("<p class=\"error\">%s</p>",$voms_preferences_log_take_notice_failed);
	}

	echo "<div id=\"attrsform\">\n";
	printf("<p class=\"underborder bold\" >%s</p>\n",sprintf($voms_preferences_log_caption,$vo));
	if ( isACLallow($pvap,"preferences","r") ) {
		// get log records
		$logs_arr = getLogRecords();
		if ( $logs_arr ) {
			foreach ( $voms_preferences_log_info as $text ) printf("<p>%s</p>\n",$text);
			if ( isACLallow($pvap,"preferences","w"))
				foreach ( $voms_preferences_log_info_adm as $text ) printf("<p>%s</p>\n",$text);
			echo "<table style=\"width: 660px;\">\n";
			printf("<tr style=\"text-align: center;\"><th></th><th>%s</th><th>%s</th><th>%s</th><th></th></tr>",
				$voms_preferences_log_table_subsys, $voms_preferences_log_table_msg, $voms_preferences_log_table_time );
			$odd = 0;
			$allids = "0";
			foreach ( $logs_arr as $log_rec ) {
				$subsys = $log_rec['subsys'];
				$msg_code = $log_rec['msg_code'];
				$allids .= ',' . $log_rec['id'];
				
				if ( $log_rec['count'] === "1" ) {
					$occurence_msg = $log_rec['first_occured'];
				} else {
					$occurence_msg = sprintf($voms_preferences_log_occurence, $log_rec['count'],
						preg_replace('/:[0-9]{2}$/','',$log_rec['first_occured']), 
						preg_replace('/:[0-9]{2}$/','',$log_rec['last_occured']));
				}
				$tn_link = "";
				if ( isACLallow($pvap,"preferences","w") ) {
				$tn_link = sprintf("<a href=\"?vo=%s&amp;action=preferences&amp;operation=eventlog&amp;id=%d\">%s</a>",
					$vo, $log_rec['id'], $voms_preferences_log_take_notice);
				}
				if ( $msg_code ) $msg_text = vsprintf($voms_log_msg_codes[$msg_code],$log_rec['msg_parms']);
					else $msg_text = $log_rec['msg_parms'];
				doOddtrtd($odd, 10);
				printf("<b>%s</b></td><td style=\"width: 50px;\"><b>%s</b></td>
					<td style=\"width: 410px;\">%s</td><td style=\"width: 150px;\">%s</td>
					<td style=\"width: 50px;\">%s</td></tr>",
					$log_rec['level'], 
					$voms_log_subsys_codes[$subsys],
					$msg_text,
					$occurence_msg,
					$tn_link);
			}
			echo "</table>\n";
			if ( isACLallow($pvap,"preferences","w") ) {
				printf("<p><a href=\"?vo=%s&amp;action=preferences&amp;operation=eventlog&amp;ids=%s\">%s</a></p>",
					$vo,$allids,$voms_preferences_log_take_notice_all);
			}
		} else echo $voms_preferences_log_no_log;
	}
	echo "</div>";
?>
