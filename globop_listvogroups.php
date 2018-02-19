<?php
$vomses_dir = $pva_install_path . "/conf/vomses/";

$vogroups_file = $vomses_dir . "/vogroups";
if (file_exists($vogroups_file)) include($vogroups_file);
if ( ! isset($vogroups_l10n) ) $vogroups_l10n = array();

$external_vos_file = $vomses_dir ."/external";
if (file_exists($external_vos_file)) include($external_vos_file);

// VOs list link
printf("<a class=\"right mt20\" href=\"?globop=listvos\">%s</a>", $voms_vo_list_caption);

// get all VOs served and specified as external
$vonamearr = getServedVOs($vomses_dir);
if ( $vonamearr === -1 ) {
	printf("<p class=\"error\" >%s<p>\n",$voms_confdir_failed);
	$vonamearr = array ();
} 

if ( isset($external_vos) ) {
	foreach ( $external_vos as $voname => $voparm ) {
		if ( isset($vonamearr[$voname]) ) continue;
		$vonamearr[$voname] = $voparm;
	}
}

if ( empty($vonamearr) ) {
	printf("<p>%s</p>",$voms_no_vo_served);
} 

if ( ! isset($vogroups) ) {
	printf("<p class=\"error\" >%s<p>\n",$voms_vo_grouplist_nogroups);
} else {
	// loop over VO groups and list VOs
	foreach ( $vogroups as $groupname => $volist ) {
		$vos_arr = array ();
		foreach ( $volist as $voname ) {
			if ( ! isset($vonamearr[$voname]) ) continue;
			$vos_arr[$voname] = $vonamearr[$voname];
			$vonamearr[$voname]['ingroup'] = true;
		}
		if ( empty($vos_arr) ) continue;
		printf("<p class=\"headp\">%s</p>\n", getL10N($groupname, $lang, $vogroups_l10n));
		echo "<table>\n";
		$rows = f_listVOsTable ($vos_arr, $external_vos);
		$odd = 0;
		foreach($rows as $tr => $tds) {
			doOddtrtd($odd);
			printf("%s</td><td>%s</td>\n</tr>\n", $tds[0], $tds[1]);
		}
		echo "</table>\n";
		echo "<div style=\"height: 40px\"></div>\n";
	}

	// default group
	if ( isset($vogroups_default) ) {
		foreach ( $vonamearr as $voname => $voparm ) {
			if ( isset($vonamearr[$voname]['ingroup']) ) {
				unset($vonamearr[$voname]);
			}
		}
		if ( ! empty($vonamearr) ) {
			printf("<p class=\"headp\">%s</p>\n", getL10N($vogroups_default, $lang, $vogroups_l10n));
			echo "<table>\n";
			$rows = f_listVOsTable ($vonamearr, $external_vos);
			$odd = 0;
			foreach($rows as $tr => $tds) {
				doOddtrtd($odd);
				printf("%s</td><td>%s</td>\n</tr>\n", $tds[0], $tds[1]);
			}
			echo "</table>\n";
			echo "<div style=\"height: 40px\"></div>\n";
		}
	}
}

?>
