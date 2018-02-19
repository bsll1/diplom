<?php
if ( isset($_POST["trans_ed"]) ) if ( isACLallow($pvap,"preferences","w")) {
	$trans_ed = _ispost("trans_ed");
	if ( $trans_ed === '0' ) {
		if ( ! get_replication_status() ) $trans_ed = '';
		else {
			printf("<p class=\"error\">%s</p>",$voms_preferences_trans_repl_fail);
			$trans_ed = 'failed';
		}
	}
	if ( $trans_ed !== 'failed' ) {
		$enable_transactions_log = false;
		if ( _invoke_transactional_sql(1,'saveSettingsToDB','enable_transactions_log',$trans_ed) ) {
			$enable_transactions_log = $trans_ed;
		} else printf("<p class=\"error\">%s</p>",$voms_preferences_trans_updfail);
	}
}
echo "<div id=\"attrsform\">\n";
printf("<p class=\"underborder bold\" >%s</p>\n",sprintf($voms_preferences_trans_caption,$vo));
// enable/disable transaction log trigger
if ( ! isset($enable_transactions_log) ) $enable_transactions_log = false;
if ( isACLallow($pvap,"preferences","r") ) {
	if ( isACLallow($pvap,"preferences","w") ) {
		echo "<form name=\"ttf\" method=\"post\" action=\"\">\n";
		printf("<span>%s",$voms_preferences_trans_log);
		if ( $enable_transactions_log ) {
			printf("%s. (<a href=\"javascript:document.ttf.submit();\">%s</a>)</span>\n",
			$voms_preferences_trans_log_enabled, $voms_preferences_trans_log_disable);
			echo "<input type=\"hidden\" name=\"trans_ed\" value='0' />\n";
		} else {
			printf("%s. (<a href=\"javascript:document.ttf.submit();\">%s</a>)</span>\n",
			$voms_preferences_trans_log_disabled, $voms_preferences_trans_log_enable);
			echo "<input type=\"hidden\" name=\"trans_ed\" value='1' />\n";
		}
		echo "</form>\n";
	} else {
		printf("<span>%s%s.</span>\n",$voms_preferences_trans_log,
		($enable_transactions_log)?$voms_preferences_trans_log_enabled:$voms_preferences_trans_log_disabled);
	}

    // transaction log viewer
    if ($enable_transactions_log) {
	foreach ( $voms_preferences_trans_table_text as $text ) printf("<p>%s</p>\n",$text);
	$transactions_arr = get_transaction_log($limit);
	$transactions_count = count($transactions_arr);

	$limit_prev = ( ($limit - $items_per_page) >= 0 )  ? ($limit - $items_per_page) : 0;
	$limit_next = $limit + $items_per_page;

	if ( $transactions_count ) {
		echo "<table style=\"width: 660px;\">\n";
		printf("<tr style=\"text-align: center;\"><th>%s</th><th>%s</th><th>%s</th></tr>",
			$voms_preferences_trans_table_time, $voms_preferences_trans_table_who, $voms_preferences_trans_table_mesg);
		$odd = 0;
		foreach ( $transactions_arr as $tinfo ) {
			$source_caption = CNfromDN($tinfo['upddn']);
			if ( ! empty($tinfo['flavor']) ) $source_caption .= sprintf(' (%s)',$tinfo['flavor']);
			doOddtrtd($odd, 150);
			printf("%s</td><td style=\"width: 160px;\">%s</td><td style=\"width: 350px;\">%s</td></tr>\n",
				$tinfo['time'], 
				sprintf($voms_preferences_trans_table_who_format,CNfromDN($tinfo['admdn']),$source_caption),
				nl2br(get_function_description($tinfo['fname'],$tinfo['fargs']))
			);
		}
		echo "</table>\n";
		echo "<div class=\"tcaption\">\n";
			if ( $limit ) printf("<a href=\"?vo=%s%s&amp;limit=%d\">«&nbsp;%s</a>",
				$vo,$voms_preferences_transactions_url,$limit_prev,$voms_preferences_trans_table_newer);
			if ( $transactions_count == $items_per_page ) {
				if ( $limit ) echo " | ";
				printf("<a href=\"?vo=%s%s&amp;limit=%d\">%s&nbsp;»</a>", 
					$vo,$voms_preferences_transactions_url, $limit_next,$voms_preferences_trans_table_older);
			}
		echo "</div>\n<div class=\"mt20\"></div>\n";
	} else {
		if ( $limit ) $voms_preferences_trans_no_entries .= sprintf($voms_preferences_trans_no_entries_older,
				sprintf("?vo=%s%s&amp;limit=%d",$vo,$voms_preferences_transactions_url,$limit_prev));
		printf("<p>%s</p>",$voms_preferences_trans_no_entries);
	}
    }
}
echo "</div>\n";
?>
