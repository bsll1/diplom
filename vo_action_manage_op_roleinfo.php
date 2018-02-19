<?php
// Groups and they ACLs
$allgroups = getGroups(-1);
$groups_pvap = getGroupsPermissions(array_values($allgroups), $USER_DN, $USER_CA, $member);
// Group to show membership details for role in
$sgrp = _ispost("sgrp",1);
$agrp = _ispost("agrp",1);

// Update role information processing (POST)
// Dissmiss role
if (  isset($_POST["ridrm"]) ) if ( isACLallow($groups_pvap[$sgrp],"container","w") ) {
	$suid = _ispost("uid",0);
	$srid = _ispost("rid",0);
	if ( delUserRole( $sgrp, $srid, $suid ) ) $smarty->assign("voms_delrole_usrdet_updok", $voms_delrole_usrdet_updok);
}
// Add attribute
if ( isset($_POST["attraddsub"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("attr");
	$uattrv = _ispost("attrval");
	if ( addAttr($uattrid, $uid, $uattrv, "r", $agrp ) ) $smarty->assign("voms_attribute_updok", $voms_attribute_updok);
}
// Del attribute
if ( isset($_POST["rmattr"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("rmattrid");
	$uattrgid = _ispost("rmattrgid");
	if ( delAttr($uattrid, $uid, "r", $uattrgid ) ) $smarty->assign("voms_attribute_delok", $voms_attribute_delok);
}

// Get Role name value
$role_name = getRoleName($uid);
if ( $role_name === 0 ) hackexit(1);

// User information per role per group
$smarty->assign("information_per_role_per_group", sprintf($voms_usrdet_caption, $voms_role . " " . $role_name));

// Determine group list allowed by ACL
$serch_roles_select_options = "";
$set_role_attribute_select_options = "";
foreach ( $allgroups as $grpname => $grpid ) {
	if ( isACLallow($groups_pvap[$grpid],"container","r", 1) ) {
		$serch_roles_select_options .= sprintf("<option value=\"%d\"", $grpid );
		if ( $grpid === $sgrp ) $serch_roles_select_options .= " selected ";
		$serch_roles_select_options .= sprintf(">%s</option>\n",$grpname);
	}
	if ( isACLallow($groups_pvap[$grpid],"attributes","w") ) {
		$set_role_attribute_select_options .= sprintf("<option value=\"%d\"", $grpid );
		if ( $grpid === $agrp ) $set_role_attribute_select_options .= " selected ";
		$set_role_attribute_select_options .= sprintf(">%s</option>\n",$grpname);
	}
}

if ( $serch_roles_select_options === "" ) {
	// if no one group allow reading container -- show standart deny message
	isACLallow($pvap,"container","r");
} else {
	$smarty->assign("voms_roleinfo_userdet", sprintf($voms_roleinfo_userdet,$role_name));
	$smarty->assign("vo", $vo);
	$smarty->assign("uid", $uid);
	$smarty->assign("serch_roles_select_options", $serch_roles_select_options);
	$smarty->assign("voms_mng_user_search", $voms_mng_user_search);
	// Show members
	if ( isACLallow($groups_pvap[$sgrp],"container","r") ) {
		$users = getVOMembersCA ($limit, $filter, $sgrp, $uid );
		$allusers = getVOMembersCount($filter, $sgrp, $uid);
		if ( $allusers == 0 ) {
			if ( ! $filter ) $smarty->assign("voms_no_users", $voms_no_users); else
			$smarty->assign("voms_no_search", sprintf($voms_no_search, htmlspecialchars($filter)));
		} else {
			$smarty->assign("table","table");
			$smarty->assign("groups_pvap",$groups_pvap);
			$smarty->assign("voms_userinfo_usrdet_dn",$voms_userinfo_usrdet_dn);
			$smarty->assign("sgrp",$sgrp);
			$smarty->assign("agrp",$agrp);
			$smarty->assign("uid",$uid);
			$smarty->assign("voms_dissmiss_role",$voms_dissmiss_role);
			$odd = 0;
			foreach ( $users as $vommbe ) {
				doOddtrtd($odd);
				// Dissmiss role link for admins
				// if ( isACLallow($groups_pvap[$sgrp],"container","w") ) {
				// 	// Form with parameters for Role removal; Submittion via link
				// 	echo "<td><form name=\"rmrid".$vommbe["id"]."\" method=\"post\" action=\"\">\n";
				// 	echo "<input type=\"hidden\" name=\"sgrp\" value=\"".$sgrp."\" />\n";
				// 	echo "<input type=\"hidden\" name=\"agrp\" value=\"".$agrp."\" />\n";
				// 	echo "<input type=\"hidden\" name=\"rid\" value=\"".$uid."\" />\n";
				// 	echo "<input type=\"hidden\" name=\"uid\" value=\"".$vommbe["id"]."\" />\n";
				// 	echo "<input type=\"hidden\" name=\"ridrm\" value=1 />\n";
				// 	echo "</form>\n";
				// 	echo "<a href=\"javascript:document.rmrid".$vommbe["id"].".submit();\">".$voms_dissmiss_role."</a></td>\n";
				// } else  echo "<td></td>\n";
			}
			//echo "</table>\n";
			// Caption under table provide navigation between pages
			$smarty->assign("showLimitsCaption",sprintf(ShowLimitsCaption( sprintf("?vo=%s&amp;operation=roleinfo&amp;id=%s",$vo,$uid), $allusers)));
		}
	}
}

// Attribute management
//echo "<div id=\"attrmcaption\" >\n";
$smarty->assign('voms_attrm_caption',sprintf($voms_attrm_caption, $voms_role . " " . $role_name));
//printf($voms_attrm_caption, $voms_role . " " . $role_name);
// echo "<a href=\"javascript:showhide('attrm');\"><img src=\"pics/minimize.png\" id=\"attrmimg\" border=\"0\" alt=\"minimize\" /></a>\n";
// echo "</div>\n<div id=\"attrm\">\n";
	if ( isACLallow($pvap,"attributes","r")) {
		$vo_attributes = getAttributes();
		if ( $vo_attributes === 0 ) $smarty->assign('voms_attributes_notexists',$voms_attributes_notexists); else {
			if ( $set_role_attribute_select_options !== "" ) {
				// Form for adding attributes 
				$smarty->assign('showaddatribute',ShowAddAtribute ( $vo_attributes, $set_role_attribute_select_options,
					sprintf("<input type=\"hidden\" name=\"sgrp\" value=\"%s\" />\n", $sgrp)));
			}
			// Get User attributes
			$roleattrs = getRoleAttributes ($uid);
			// Determine ACL access permissions for every attribute
			if ( ! empty($roleattrs) )
			foreach ( $roleattrs as $attrid => $attrparm ) {
				$roleattr_grp = $attrparm["groupid"];
				$roleattr_acl = 0;
				if ( isACLallow($groups_pvap[$roleattr_grp],"attributes","r", 1)) $roleattr_acl += 1;
				if ( isACLallow($groups_pvap[$roleattr_grp],"attributes","w")) $roleattr_acl += 2;
				$roleattrs[$attrid]["aclallowed"] = $roleattr_acl; 
			}
			showAttr ( $roleattrs, 1, $voms_role, $agrp );
		}
	}
#echo "</div>\n";
$smarty->display('templates/vo_action_manage_op_roleinfo.tpl');
?>
