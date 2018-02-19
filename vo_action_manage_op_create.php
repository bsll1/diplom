<?php
if ( isACLallow($pvap,"membership","w") ) {
	printf("<p class=\"underborder\">%s</p>\n",$voms_create_new_caption);
	// From for POSTing parameters about new user
	printf("<form id=\"workareaf\" method=\"post\" action=\"?vo=%s\">\n", $vo);
	printf("<label>DN:</label><input type=\"text\" name=\"crdn\" value=\"%s\"></input><br/>\n", $crdn);
	printf("<label>CN:</label><input type=\"text\" name=\"crcn\" value=\"%s\"></input><br/>\n", $crcn);
	echo "<label>CA:</label><select name=\"crca\">";
	foreach ( getCAList() as $caname ) {
		printf("<option value=\"%s\"",$caname);
		if ( $caname === $crca ) echo " selected=\"selected\"";
		printf(">%s</option>\n",$caname);
	}
	echo "</select><br/>\n";
	printf("<label>E-mail:</label><input type=\"text\" name=\"crmail\" value=\"%s\"></input><br/>\n", $crmail);
	printf("<input class=\"crnewbt\" type=\"submit\" name=\"crsub\" value=\"%s\" />", $voms_create_new_submit);
	echo "</form>\n";
}
?>
