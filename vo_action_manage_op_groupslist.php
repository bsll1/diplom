<?php
$allgroups = getGroups(-1, $filter);
$parents_hash = getGroupsParents();
$groups_pvap = getGroupsPermissions(array_values($allgroups), $USER_DN, $USER_CA, $member);

// Filter groups with parent "container:read" permissions
$crgroups = array ();
foreach ( $allgroups as $grpname => $grpid ) {
	$pgid = $parents_hash[$grpid];
        if ( isACLallow($groups_pvap[$pgid],"container","r",1) ) $crgroups[$grpname] = $grpid;
}

// Create array to display using $limit/$items_per_page 
$vo_groups = array_slice($crgroups, $limit, $items_per_page);
$all_groups = count($crgroups);

// Create group link for VO admins
$creategrp_link = null;
if ( ! empty($crgroups) ) $creategrp_link = sprintf("<a class=\"right\" href=\"?vo=%s&amp;operation=creategrp\" >%s</a>",
	$vo,$voms_create_grp);

// Search functionality
$hrefbase = "?vo=".$vo."&operation=groupslist";
showSearchInput($voms_mng_group_search, $creategrp_link, $hrefbase );

// Printing groups table is exists
if ( $all_groups == 0 ) {
	if ( ! $filter ) printf("<p>%s</p>", $voms_no_groups); else
		printf ("<p>". $voms_no_groups_search . "</p>\n", htmlspecialchars($filter) );
} else {
	echo "<p>". $voms_mng_groups_caption . "</p>\n";
	echo "<table>\n";
	$odd = 0;
	foreach ( $vo_groups as $vgname => $vgl ) {
		doOddtrtd($odd);
		printf("<div><a href=\"?vo=%s&amp;operation=groupinfo&amp;id=%d\">%s</a><div></td>\n",$vo,$vgl,$vgname);
		// Delete group link for admins
		$pgid = $parents_hash[$vgl];
	        if ( isACLallow($groups_pvap[$pgid],"container","w") ) {
			if ( $vgname === "/".$vo ) { echo "<td></td>\n"; } else {
				$dellink = sprintf("?vo=%s&amp;operation=deletegrp&amp;id=%d",$vo,$vgl);
				$deltext = sprintf ($voms_deletegrp_confirm, $vgname);
				printf("<td class=\"adm\"><a href=\"javascript:ynalert('%s','%s')\" >%s</a></td>\n",
					$deltext,$dellink,$voms_grp_del);
			}
		} else echo "<td></td>\n";
		echo "</tr>";
	}
	echo "</table>\n";
	// Caption under table provide navigation between pages
	ShowLimitsCaption( $hrefbase, $all_groups );
}
?>
