<?php
$vomses_dir = $pva_install_path . "/conf/vomses/";

$vogroups_file = $vomses_dir . "/vogroups";
if (file_exists($vogroups_file)) include($vogroups_file);

$external_vos_file = $vomses_dir ."/external";
if (file_exists($external_vos_file)) include($external_vos_file);

$vonamearr = getServedVOs($vomses_dir);

$smarty->assign("voms_vo_grouplist_caption", $voms_vo_grouplist_caption);
$smarty->assign("voms_vo_list", $voms_vo_list);
$smarty->assign("vonamearr", $vonamearr);
$smarty->assign("voms_confdir_failed", $voms_confdir_failed);
if ( isset($vogroups) ) if ( ! empty($vogroups) ) 
	$smarty->assign("vogroups", $vogroups);

if ( $vonamearr === -1 ) {
} else if ( empty($vonamearr) ) {
	$smarty->assign("voms_no_vo_served", $voms_no_vo_servedd);
} else {
	$rows = f_listVOsTable ($vonamearr, $external_vos);
	$smarty->assign("rows", $rows);
	$odd = 0;
	foreach($rows as $tr => $tds) {
		doOddtrtd($odd);
	}
}

if ( ! empty($external_vos) ) {
	$smarty->assign("voms_extenal_vo_list", $voms_extenal_vo_list);
	$dummy = array();
	$rows = f_listVOsTable ($external_vos, $dummy);
	$smarty->assign("rows", $rows);
	$odd = 0;
	foreach($rows as $tr => $tds) {
		doOddtrtd($odd);
	}
}
$smarty->display('templates/globop_listvos.tpl');
?>
