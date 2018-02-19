<?php
$smarty->assign("voms_manage_title", $voms_manage_title);
$smarty->assign("voms_manage", $voms_manage);
$smarty->assign("vo", $vo);
$smarty->display('templates/vo_action_manage_menu.tpl');
?>
