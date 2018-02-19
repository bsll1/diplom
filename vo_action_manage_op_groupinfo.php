<?php
// Get access permissions for group (uid variable contain group id):
if ( $uid !== 1 ) {
	$g_perm = getProperUserACL($USER_DN, $USER_CA, $member, $uid);
	$pvap = decodeACLPermissions($g_perm);
}

$defacl = _ispost("defacl",0);
$acldeff = ( $defacl ) ? "d" : "w";
// This is ACL change processing
// -- edit --
if ( isset( $_POST["admid2"] )) if ( isACLallow($pvap,"acl",$acldeff) ) {
	$aclid = _ispost("aclid",0);
	$admid = _ispost("admid",0);
	$propagate = isset($_POST["propagate"] ) ? $uid : 0;

	$newperm = constructACLPermissions($_POST);

	if (! _invoke_transactional_sql(1,'updateACLPermissions',$aclid,$admid,$newperm,$propagate,0,0))
		printf("<p class=\"error\">%s</p>", $voms_acl_update_failed);
}
// -- delete --
if ( isset( $_POST["admid3"] )) if ( isACLallow($pvap,"acl",$acldeff) ) {
	$aclid = _ispost("aclid",0);
	$admid = _ispost("admid",0);
	$propagate = isset($_POST["propagate"] ) ? $uid : 0;

	if ( ! _invoke_transactional_sql(1,'deleteACLentry', $aclid, $admid, $propagate, $defacl) )
		printf("<p class=\"error\">%s</p>", $voms_acl_delete_failed);
}
// -- create new
if ( isset( $_POST["aclnewsub"] )) if ( isACLallow($pvap,"acl",$acldeff) ) {
	$entryname = _ispost("entryname",0);
	$propagate = isset($_POST["propagate"]) ? 1 : 0;
	$perm = constructACLPermissions($_POST);

	if ( $entryname === "vouser") {
		$userid = _ispost("vouserv",0);
		$admid = getAdminByUsr( $userid );
	} else if ( $entryname === "nonvouser") {
		$nonvouserdn = _ispost("nonvouserdn",0);
		$nonvouserca = _ispost("nonvouserca",0);
		$adminfo = array ( "dn" => $nonvouserdn, "caid" => $nonvouserca, "ca" => getCAName($nonvouserca) );
		$admid = getAdminId ( $adminfo );
	} else if ( $entryname === "role") {
		$rolev =  _ispost("rolev",0);
		$groupv = _ispost("rgroupv",0);
		$roledn = $groupv . "/Role=" . $rolev;
		$rolecaid = getCAId ( "/O=VOMS/O=System/CN=VOMS Role" );
		$adminfo = array ( "dn" => $roledn, "caid" => $rolecaid, "ca" => "/O=VOMS/O=System/CN=VOMS Role" );
		$admid = getAdminId ( $adminfo );
	} else if ( $entryname === "group") {
		$groupv = _ispost("groupv",0);
		$groupcaid = getCAId ( "/O=VOMS/O=System/CN=VOMS Group" );
		$adminfo = array ( "dn" => $groupv, "caid" => $groupcaid, "ca" => "/O=VOMS/O=System/CN=VOMS Group" );
		$admid = getAdminId ( $adminfo );
	} else if ( $entryname === "anyauth" ) {
		$admdn = "/O=VOMS/O=System/CN=Any Authenticated User";
		$admcaid = getCAId ( "/O=VOMS/O=System/CN=Dummy Certificate Authority" );
		$adminfo = array ( "dn" => $admdn, "caid" => $admcaid, "ca" => "/O=VOMS/O=System/CN=Dummy Certificate Authority" );
		$admid = getAdminId ( $adminfo );
	}
	if ( ! $admid ) printf("<p class=\"error\">%s</p>", $voms_acl_create_failed);
	else _invoke_transactional_sql(1,'updateACLPermissions',0,$admid,$perm,$propagate,$uid,$defacl);
}
// group name value is important all over the "groupinfo"
$group_name = getGroupById($uid);
if ( $group_name === 0 ) hackexit(1);

// Update group information processing (POST)
// Add attribute
if ( isset($_POST["attraddsub"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("attr");
	$uattrv = _ispost("attrval");
	if ( addAttr($uattrid, $uid, $uattrv, "g" ) ) printf("<p class=\"alldone\">%s</p>", $voms_attribute_updok);
}

// Del attribute
if ( isset($_POST["rmattr"]) ) if ( isACLallow($pvap,"attributes","w") ) {
	$uattrid = _ispost("rmattrid");
	if ( delAttr($uattrid, $uid, "g" ) ) printf("<p class=\"alldone\">%s</p>",$voms_attribute_delok);
}

// ACL management div
echo "<div id=\"acldetcaption\">\n";
printf($voms_acldet_caption, sprintf("%s %s",$voms_group,$group_name));
echo "<a href=\"javascript:showhide('acldet');\"><img src=\"pics/minimize.png\" id=\"acldetimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n<div id=\"acldet\">\n";
// ACL manage content
if ( isACLallow($pvap,"acl","r") ) showACLmanagement( $vo, $pvap, $uid );
echo "</div>\n";

// Userinfo for Group
echo "<div id=\"usrdetcaption\" class=\"mt20\" >\n";
printf($voms_usrdet_caption, sprintf("%s %s",$voms_group,$group_name));
echo "<a href=\"javascript:showhide('usrdet');\"><img src=\"pics/minimize.png\" id=\"usrdetimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n<div id=\"usrdet\">\n";
if ( isACLallow($pvap,"container","r") ) showUsermanagement ( $vo, $limit, $filter, 0, 0, $uid, "&amp;operation=groupinfo&amp;id=".$uid, "#usrdet" );
echo "</div>\n";

// Attribute management
echo "<div id=\"attrmcaption\" >\n";
printf($voms_attrm_caption, sprintf("%s %s",$voms_group,$group_name));
echo "<a href=\"javascript:showhide('attrm');\"><img src=\"pics/minimize.png\" id=\"attrmimg\" border=\"0\" alt=\"minimize\" /></a>\n";
echo "</div>\n<div id=\"attrm\">\n";
if ( isACLallow($pvap,"attributes","r") ) {
	$vo_attributes = getAttributes();
	if ( $vo_attributes === 0 ) printf("<p>%s</p>",$voms_attributes_notexists); else {
		// Form for adding attributes
		if ( isACLallow($pvap,"attributes","w") ) ShowAddAtribute ( $vo_attributes );
		// Get User attributes
		$groupattrs = getGroupAttributes ($uid);
		showAttr ( $groupattrs, isACLallow($pvap,"attributes","w"), $voms_group );
	}
}
echo "</div>\n";
?>
