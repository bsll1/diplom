<?php
if ( isACLallow($pvap,"attributes","r") ) {
	// add/delete attribure class
	if ( isACLallow($pvap,"attributes","w") ) {
		if ( isset($_POST["attrrm"]) ) {
			$attrid = _ispost("attrid");
			if ( delVOAttr( $attrid ) ) printf("<p class=\"alldone\">%s</p>",$voms_attrdel_updok);
		}
		if ( isset($_POST["attrcrsub"] )) {
			$attrname = _ispost("attrname");
			$attrdescr = _ispost("attrdescr");
			$attruniq = ( isset($_POST["attruniq"])) ? 1 : 0;
			if ( addVOAttr( $attrname, $attrdescr, $attruniq )) printf("<p class=\"alldone\">%s</p>",$voms_attradd_updok);
		}
		// Create new attribute
		printf("<p class=\"underborder\">%s</p>\n",$voms_mattr_caption);
		echo "<form id=\"attrsform\" method=\"post\" action=\"\">\n";
		printf("<label>%s</label><input type=\"text\" name=\"attrname\" /><br/><br/>\n", $voms_createattr_name);
		printf("<label>%s</label><textarea name=\"attrdescr\" cols=\"40\" rows=\"5\" ></textarea><br/><br/>\n", $voms_createattr_descr);
		printf("<label>%s</label><input class=\"wa\" type=\"checkbox\" name=\"attruniq\" /><br/>\n", $voms_createattr_uniq);
		printf("<input class=\"crnewbt\" type=\"submit\" name=\"attrcrsub\" value=\"%s\" />", $voms_create_new_submit );
		echo "</form>\n";
	}
	// List attributes for manage
	$allattrs = getAttributes();
	if ( ! $allattrs ) printf("<p>%s</p>\n", $voms_attributes_notexists); else {
		echo "<table>\n";
		printf("<th>%s</th><th>%s</th><th>%s</th><th></th></tr>",
			key($voms_attributes),current($voms_attributes),$voms_attributes_uniq);
		$odd = 0;
		foreach ( $allattrs as $attrid => $attrv ) {
			doOddtrtd($odd);
			printf("%s</td>", $attrv["name"]);
			printf("<td>%s</td>", $attrv["descr"]);
			printf("<td>%s</td>", $attrv["uniq"] ? "true" : "false" );
			if ( isACLallow($pvap,"attributes","w") ) {
				printf("<td><form method=\"post\" name=\"rmattr%s\" action=\"\">\n",$attrid);
				printf("<input type=\"hidden\" name=\"attrid\" value=\"%s\" />\n",$attrid);
				echo "<input type=\"hidden\" name=\"attrrm\" value=1 />\n";
				echo "</form>\n";
				printf("<a href=\"javascript:document.rmattr%s.submit();\">%s</a></td>",$attrid,$voms_remove_attr);
			} else echo "<td></td>";
		}
		echo "</table>";
	}
}
?>
