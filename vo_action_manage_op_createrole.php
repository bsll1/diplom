<?php
if ( isACLallow($pvap,"container","w") ) {
	printf("<p class=\"underborder\">%s</p>\n",$voms_createrole_new_caption);
	printf("<form id=\"workareaf\" method=\"post\" action=\"?vo=%s\">\n", $vo);
	printf("<label>%s</label><input type=\"text\" name=\"crrole\" value=\"%s\" /><br/>\n", $voms_createrole_role, $crrole);
	printf("<input class=\"crnewbt\" type=\"submit\" name=\"crrsub\" value=\"%s\" />", $voms_create_new_submit );
	echo "</form>\n";
}
?>
