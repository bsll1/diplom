<?php
// echo "<div id=\"menu\">";

// if ( $vo !== 0 ) {
// 	// VO is specified -- show VO actions menu
// 	if ( $isfailed == 1 ) {
// 		// VO is not correct -- just print error
// 		printf("<p class=\"error\">%s</p>\n",$voms_notexist_vo);
// 	} else {
// 		echo "<div class=\"menulinks\">\n";
// 		// General menu
// 		foreach ( $voms_mlinks as $mlk => $mlv )
// 			printf("<a href=\"?vo=%s%s\">%s</a>\n",$vo,$mlv,$mlk);
// 		// Aditional admin menu
// 		if ( isACLallow($pvap,"requests","r", 1 ))
// 			foreach ( $voms_requests_mlinks as $mlk => $mlv )
// 				printf("<a href=\"?vo=%s%s\">%s</a>\n",$vo,$mlv,$mlk);
// 		if ( isACLallow($pvap,"preferences","r", 1 ))
// 			foreach ( $voms_preferences_mlinks as $mlk => $mlv )
// 				printf("<a href=\"?vo=%s%s\">%s</a>\n",$vo,$mlv,$mlk);
// 		// Show link to other VOs
// 		printf("<a class=\"othvo\" href=\".\">%s</a>\n",$voms_other_vo);
// 		echo "</div>\n";
// 	}
// } else {
// 	// Show global VOMS operation menu
// 	echo "<div class=\"menulinks\">\n";
// 	foreach ( $voms_mainpage_mlinks as $mlk => $mlv )
// 		printf("<a href=\"%s\">%s</a>", $mlv, $mlk);
// 	printf("<a class=\"othvo\" href=\"%s\">%s</a>\n", "?globop=newvo", $voms_request_vo_registration );
// 	echo "</div>\n";
// }
// echo "</div>\n";
?>
<?php 
	$smarty->assign("vo", $vo);
	$smarty->assign("isfailed", $isfailed);
	$smarty->assign("voms_notexist_vo", $voms_notexist_vo);
	$smarty->assign("voms_mlinks", $voms_mlinks);
	$smarty->assign("pvap", $pvap);
	$smarty->assign("voms_requests_mlinks", $voms_requests_mlinks);
	$smarty->assign("voms_preferences_mlinks", $voms_preferences_mlinks);
	$smarty->assign("voms_other_vo", $voms_other_vo);
	$smarty->assign("voms_mainpage_mlinks", $voms_mainpage_mlinks);
	$smarty->assign("voms_request_vo_registration", $voms_request_vo_registration);
	$smarty->display('templates/interface_menu_bar.tpl');
?>
