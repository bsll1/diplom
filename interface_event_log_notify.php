<?php
$event_log_rec_cnt = getLogRecordsCount();
if ( $event_log_rec_cnt ) {
	printf("<div id=\"notify\">%s</div>", 
		sprintf($voms_log_notify_msg,$event_log_rec_cnt, "?vo=".$vo.$voms_preferences_eventlog_url)
	);
}
?>
