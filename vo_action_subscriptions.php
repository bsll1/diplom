<?php
echo "<div id=\"manage\">\n";
printf("<p>%s</p>\n",$voms_subscriptions_manage_title);
echo "<ul>";
foreach ( $voms_subscriptions_manage as $vmk => $vmv )
	printf("<li><a href=\"?vo=%s&amp;action=subscriptions%s\">%s</a></li>",$vo,$vmv,$vmk);
echo "</ul>";
echo "</div>\n";
// workarea is container to direct information specified by $operation
echo "<div id=\"workarea\">\n";
if ($operation === "reject" ) {
	if ( isACLallow($pvap,"requests","w")) rejectSubscription ( $vo, $uid);
	$operation = "list";
} else if ($operation === "approve" ) {
	if ( isACLallow($pvap,"requests","w")) approveSubscription ( $vo, $uid );
	$operation = "list";
}

if ( isACLallow($pvap,"requests","r") ) {
		if ( $operation === "list" ) {
		showPendingRequestsmanagement ( $vo, isACLallow($pvap,"requests","w") );
	} else if ( $operation === "processed" ) {
		showProcessedRequestsmanagement( $vo );
	} else if ($operation === "pendinginfo" ) {
		if ( isACLallow($pvap,"requests","w") )
			showSubscriptionInfo ( $vo, $uid );
		else showSubscriptionInfo ( $vo, $uid, 9 ); //disable apply/reject link
	} else if ($operation === "processedinfo" ) {
		showSubscriptionInfo ( $vo, $uid, 1 );
	} else hackexit(1);
}
echo "<div class=\"mt20\"></div>\n";
echo "</div>\n";
?>
