<?php
// Container ( Groups and Roles ) management arrays
$allroles = getRoles(-1);
$allgroups = getGroups(-1);
$groups_pvap = getGroupsPermissions(array_values($allgroups), $USER_DN, $USER_CA, $member);

// Update user information processing (POST)
// CN and MAIL update
$ucn = ( isset($_POST["ucn"])) ? trim($_POST["ucn"]) : "";
$umail = _ispost("umail");
if ( isset($_POST["uinfosub"]) ) if ( isACLallow($pvap,"membership","w") ) {
	if ( updUserInfo( $ucn, $umail, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_userinfo_usrdet_updok);
}
// Assign Group membership
if ( isset($_POST["asigngroup"]) ) {
	$ugid = _ispost("grunassigned");
	if ( isACLallow($groups_pvap[$ugid],"container","w") )
		if ( asignUserGroup( $ugid, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_assinggroup_usrdet_updok);
}
// Dissmiss Group membership
if ( isset($_POST["gidrm"]) ) {
	$ugid = _ispost("gid");
	if ( isACLallow($groups_pvap[$ugid],"container","w") )
		if ( dissmissUserGroup( $ugid, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_dissmissgroup_usrdet_updok);
}
// Add Role in Group
if ( isset($_POST["ridadd"]) ) {
	$ugid = _ispost("gid");
	$urid = _ispost("rid");
	if ( isACLallow($groups_pvap[$ugid],"container","w") )
		if ( addUserRole( $ugid, $urid, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_addrole_usrdet_updok);
}
// Del Role in Group
if ( isset($_POST["ridrm"]) ) {
	$ugid = _ispost("gid");
	$urid = _ispost("rid");
	if ( isACLallow($groups_pvap[$ugid],"container","w") )
		if ( delUserRole( $ugid, $urid, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_delrole_usrdet_updok);
}
// Add attribute
if ( isset($_POST["attraddsub"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("attr");
	$uattrv = _ispost("attrval");
	if ( addAttr($uattrid, $uid, $uattrv ) ) printf("<p class=\"alldone\">%s</p>",$voms_attribute_updok);
}
// Del attribute
if ( isset($_POST["rmattr"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("rmattrid");
	if ( delAttr($uattrid, $uid ) ) printf("<p class=\"alldone\">%s</p>",$voms_attribute_delok);
}
// --- member details
echo "<div id=\"usrdetcaption\">\n";
echo $voms_userinfo_usrdet_caption;
echo "<a href=\"javascript:showhide('usrdet');\"><img src=\"pics/minimize.png\" id=\"usrdetimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n";
echo "<div id=\"usrdet\">";
if ( isACLallow($pvap,"membership","r") ) {
	// Get user details
	list ( $udn, $ucn, $uca, $umail ) = getUserInfo($uid);
	// If user detailes unavaliable ( e.g. specified userid is wrong in GET ) - report and done
	if ( $udn == NULL ) {
		printf("<p>%s</p>",$voms_userdet_empty);
		echo "</div>";
	} else {
		// Delete user link for admins
		if ( isACLallow($pvap,"membership","w") ) {
			$dellink = "?vo=". $vo ."&amp;operation=delete&amp;id=". $uid;
			$deltext = sprintf ($voms_delete_confirm, $ucn );
			printf("<div class=\"topright\"><a href=\"javascript:ynalert('%s','%s')\" >%s</a></div>",
				$deltext,$dellink,$voms_userinfo_del);
		}
		printf("<form method=\"post\" action=\"?vo=%s&amp;operation=userinfo&amp;id=%d\" >\n", $vo, $uid);
		printf("<label>%s</label><p>%s</p>\n",$voms_userinfo_usrdet_dn, $udn);
		printf("<label>%s</label><p class=\"ca\">%s</p>\n",$voms_userinfo_usrdet_ca,$uca);
		printf("<label>%s</label>",$voms_userinfo_usrdet_cn);
		if ( isACLallow($pvap,"membership","w") ) {
			printf("<input type=\"text\" name=\"ucn\" value=\"%s\" /><br/><br/>", $ucn);
		} else printf("<p>%s</p>",$ucn);
		printf("<label>%s</label>",$voms_userinfo_usrdet_ml);
		if ( isACLallow($pvap,"membership","w") ) {
			printf("<input type=\"text\" name=\"umail\" value=\"%s\" /><br/>",$umail);
			printf("<input class=\"ubtn\" type=\"submit\" name=\"uinfosub\" value=\"%s\" />",$voms_userinfo_usrdet_upd);
		} else printf("<p>%s</p>", $umail);
	echo "</form>\n";
	}
}
echo "</div>\n";
// --- user roles in groups
echo "<div id=\"mmbdetcaption\">\n";
echo $voms_userinfo_mmbdet_caption;
echo "<a href=\"javascript:showhide('mmbdet');\"><img src=\"pics/minimize.png\" id=\"mmbdetimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n";
echo "<div id=\"mmbdet\">\n";
	$umembership = getUserMembership( $uid ); // hash of array[group][role] - if exists - specified Group/Role is assigned
	// create array of unassigned groups 
	$unasigned_grops = array ();
	foreach ( $allgroups as $gname => $gid ) {
		if ( ! isset ( $umembership[$gname] ) ) $unasigned_grops[$gname] = $gid;
	}
	// Print unassigned group "select" for admins to allow adding
	if ( ! empty ($unasigned_grops) ) {
		$unasigned_select_options = "";
		foreach ( $unasigned_grops as $gname => $gid ) {
			// if container rights for group allowed assigning operations to current user
			if ( isACLallow($groups_pvap[$gid],"container","w") )
				$unasigned_select_options .= sprintf("<option value=\"%d\">%s</option>\n",$gid,$gname);
		}
		if ( $unasigned_select_options !== "" ) {
			echo "<form method=\"post\" action=\"\">\n";
			echo "<div class=\"topright\"><select name=\"grunassigned\">\n";
			echo $unasigned_select_options;
			printf("</select>\n<input class=\"ubtn\" type=\"submit\" name=\"asigngroup\" value=\"%s\" /></div>\n",
				$voms_togroup_add);
			echo "</form>\n";
		}
	}
	// Print membership attributes table
	echo "<table>";
	printf("<tr><th>%s</th><th>%s</th><th></th><th></th></tr>",$voms_groups_catpion,$voms_roles_caption);
	$odd = 0;
	foreach ( $umembership as $ummbgroup => $ummb_roles ) {
		$ummbgid = $allgroups[$ummbgroup];
		if ( ! isACLallow($groups_pvap[$ummbgid],"container","r",1) ) {
			//echo "</td><td></td><td></td><td></td></tr>\n";
			continue;
		}
		doOddtrtd($odd);
		$remove_group = "";
		$unasigned_roles = array ();
		// Print group
		printf( "%s</td>\n<td>",$ummbgroup);
		// Group named as VO will never be removed
		if ( $ummbgroup == "/".$vo ) $remove_group .= "</td><td>";
		else if ( isACLallow($groups_pvap[$ummbgid],"container","w") ) {
			// Form with parameters for Group remomal; Submittion via link
			$remove_group .= "<td><form method=\"post\" name=\"rmgid".$ummbgid."\" action=\"\">\n";
			$remove_group .= "<input type=\"hidden\" name=\"gid\" value=\"".$ummbgid."\" />\n";
			$remove_group .= "<input type=\"hidden\" name=\"gidrm\" value=1 />\n";
			$remove_group .= "</form>\n";
			$remove_group .= "<a href=\"javascript:document.rmgid".$ummbgid.".submit();\">";
			$remove_group .= $voms_remove_group."</a></td>";
		} else $remove_group .= "</td><td>";
		// Begin printing Roles for Group
		$dissmiss_role = "";
		foreach ( $allroles as $rname => $rid ) {
			// If Role is set - print it and add delete link else add to unassigned roles array
			if ( isset ( $ummb_roles[$rname] )) {
				echo $rname . "<br/>";
				if ( isACLallow($groups_pvap[$ummbgid],"container","w") ) {
					// Form with parameters for Role removal; Submittion via link
					$dissmiss_role .= "<form name=\"rmrid".$ummbgid.$rid."\" method=\"post\" action=\"\">\n";
					$dissmiss_role .= "<input type=\"hidden\" name=\"gid\" value=\"".$ummbgid."\" />\n";
					$dissmiss_role .= "<input type=\"hidden\" name=\"rid\" value=\"".$rid."\" />\n";
					$dissmiss_role .= "<input type=\"hidden\" name=\"ridrm\" value=1 />\n";
					$dissmiss_role .= "</form>\n";
					$dissmiss_role .= "<a href=\"javascript:document.rmrid".$ummbgid.$rid.".submit();\">";
					$dissmiss_role .= $voms_dissmiss_role."</a><br/>\n";
				}
			} else $unasigned_roles[$rname] = $rid;
		}
		// Print unassigned Roles and "add" link if admin control
		if ( ( isACLallow($groups_pvap[$ummbgid],"container","w") ) && ! empty($unasigned_roles) ) {
			// Form with parameters for Role addnig
			echo "<form name=\"addrid".$ummbgid."\" method=\"post\" action=\"\">\n";
			echo "<select name=\"rid\">\n";
			foreach ( $unasigned_roles as $rname => $rid )
				printf("<option value=\"%s\">%s</option>\n",$rid,$rname);
			echo "</select>\n";
			echo "<input type=\"hidden\" name=\"gid\" value=\"".$ummbgid."\" />\n";
			echo "<input type=\"hidden\" name=\"ridadd\" value=1 />\n";
			echo "</form>\n";
			// Submittion via link
			$dissmiss_role .= "<a href=\"javascript:document.addrid".$ummbgid.".submit();\">";
			$dissmiss_role .= $voms_add_role."</a><br/>\n";
		}
		echo "</td>\n<td>".$dissmiss_role;
		echo $remove_group;
		echo "</td>\n</tr>\n";
	}
	echo "</table>";
echo "</div>\n";
// --- user attributes 
echo "<div id=\"attrmcaption\">\n";
echo $voms_userinfo_attrm_caption;
echo "<a href=\"javascript:showhide('attrm');\"><img src=\"pics/minimize.png\" id=\"attrmimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n";
echo "<div id=\"attrm\">";
if ( isACLallow($pvap,"attributes","r") ) {
	// Manage user attributes
	$vo_attributes = getAttributes();
	if ( $vo_attributes === 0 ) printf("<p>%s</p>",$voms_attributes_notexists); else {
		// Form for adding attributes
		if ( isACLallow($pvap,"attributes","w") ) ShowAddAtribute ( $vo_attributes );
		// Get User attributes
		$userattrs = getUserAttributes ($uid);
		showAttr ( $userattrs, isACLallow($pvap,"attributes","w"), $voms_user );
	}
}
echo "</div>";
?>
