<?php
$smarty->assign("vo", $vo);
$smarty->assign("isfailed", $isfailed);
if ( $vo !== 0 && $isfailed == 0 ) {
	$smarty->assign("voms_str_forvo", $voms_str_forvo);
	$smarty->assign("voms_str_user", $voms_str_user);
	$smarty->assign("user", $user);
	// If not registred -- propose to register
	$vo_registration_enabled = isset($vo_registration_enabled) ? $vo_registration_enabled : 1;
    $register_url = sprintf("?vo=%s&amp;action=register%s", $vo, ( _isdef($defaultreg) == "service" )?"_service":"" );
	if ( preg_match("/CN=/", $USER_DN) && $member == 0 && $vo_registration_enabled == 1 )
		#print_r($register_url);
		$smarty->assign("register_url", $register_url);
		$smarty->assign("voms_register", $voms_register);
}
$smarty->display('templates/interface_header.tpl');
?>
