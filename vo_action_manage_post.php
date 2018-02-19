<?php
// ---- check for POST operations and proceed ----
$crdn   = _ispost("crdn");
$crcn   = _ispost("crcn");
$crca   = _ispost("crca", $defaultca);
$crmail = _ispost("crmail");
$crpgrp = _ispost("crpgrp",0);
$crgrp  = _ispost("crgrp");
$crrole = _ispost("crrole");
if ( isset($_POST["crsub"]) ) if ( isACLallow($pvap,"membership","w") ) {
	// if creation failed for some reason -- return to creation 
	if ( newMember ($crdn, $crcn, $crca, $crmail, $vo) === 0 ) $operation = "create";
}
// Creation of a new group in VO
if ( isset($_POST["crgsub"]) ) if ( isACLallow($pvap,"container","w") ){
	if ( newGroup ($crgrp, $crpgrp, $vo ) === 0 ) $operation = "creategrp"; else
	$operation = "groupslist";
}
// Creation of a new Role in VO
if ( isset($_POST["crrsub"]) ) if ( isACLallow($pvap,"container","w") ){
	if ( newRole ($crrole, $vo ) === 0 ) $operation = "createrole"; else
	$operation = "roleslist";
}
?>
