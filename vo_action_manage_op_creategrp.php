<?php
$allgroups = getGroups(-1);
$groups_pvap = getGroupsPermissions(array_values($allgroups), $USER_DN, $USER_CA, $member);

// Filter groups with "container:write" permissions
$cwgroups = array ();
foreach ( $allgroups as $grpname => $grpid ) {
	if ( isACLallow($groups_pvap[$grpid],"container","w") ) $cwgroups[$grpname] = $grpid;
}

// Check if allowed to create some groups
if ( ! empty($cwgroups) ) {
	printf("<p class=\"underborder\">%s</p>\n",$voms_creategrp_new_caption);
	printf("<form id=\"workareaf\" method=\"post\" action=\"?vo=%s\">\n", $vo);
	printf("<label>%s</label><select name=\"crpgrp\">\n", $voms_creategrp_pgrp);
	foreach ( $cwgroups as $grpname => $grpid ) {
		printf ("<option value=\"%d\"", $grpid );
		if ( $grpid === $crpgrp ) echo " selected=\"selected\" ";
		printf (">%s</option>\n",$grpname);
	}
	echo "</select><br/><br/>\n";
	printf("<label>%s</label><input type=\"text\" name=\"crgrp\" value=\"%s\" /><br/>\n", $voms_creategrp_grp, $crgrp);
	printf("<input class=\"crnewbt\" type=\"submit\" name=\"crgsub\" value=\"%s\" />", $voms_create_new_submit );
	echo "</form>\n";
} else printf("<p class=\"error\">%s</p>", $voms_creategrp_notallowed);
?>
