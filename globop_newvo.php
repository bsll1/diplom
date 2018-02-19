<?php
echo "<div id=\"submitform\">\n";
$dontshownewvoform_flag = false;
if ( isset($_POST["newvosub"]) ) {
	$nvo_voname  = _ispost("voname");
	$nvo_admmail = _ispost("admmail");
	$nvo_defca   = _ispost("defca");
	$nvo_descr   = _ispost("descr");
	$nvo_homepg  = _ispost("homepg");
	$nvo_rules   = _ispost("rules");
	//Check input
	if ( ! preg_match($regex_name,  $nvo_voname)  ) printf("<p class=\"error\">%s</p>", $voms_newvo_error_name); else
	if ( ! preg_match($regex_email, $nvo_admmail) ) printf("<p class=\"error\">%s</p>", $voms_newvo_error_email); else
	if ( ! preg_match($regex_url,   $nvo_rules)   ) printf("<p class=\"error\">%s</p>", $voms_newvo_error_rules); else {
		$nvo_message = construct_newvo_request($nvo_voname,$USER_DN,$USER_CA,$nvo_admmail,$nvo_defca,$nvo_descr,$nvo_homepg,$nvo_rules);
		add_techinfo($nvo_message, $user, $nvo_admmail, $USER_DN, "# ");
		if ( isset($mail_filecopies_path)) save_mail_filecopy (sprintf("new_VO_request_%s", date("d_M_Y_H_i_s")), $nvo_message);
		$dontshownewvoform_flag = send_request_for_newvo($user,$nvo_admmail,$nvo_message);
		if ( $dontshownewvoform_flag ) printf("<p class=\"alldone\">%s</p>", $voms_newvo_ok);
			else printf("<p class=\"error\">%s</p>", $voms_newvo_error);
	}
}
if ( ! $dontshownewvoform_flag ) shownewvoform($USER_DN, $USER_CA);
echo "</div>";
?>
