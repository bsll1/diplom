<?php
echo "<div id=\"submitform\">\n";
$dontshowmailadmform_flag = false;
if ( $USER_DN === 0 ) if (!isset($_SESSION)) session_start(); //for captcha antispam method
if ( isset($_POST["mailsub"]) ) {
	$sm_username = _ispost("username",sprintf("%s",($USER_DN !== 0)?$user:""));
	$sm_mailfrom = _ispost("mailfrom");
	$sm_mailtext = _ispost("mailtext");
	//Check input
	if ( ! preg_match($regex_title, $sm_username) ) printf("<p class=\"error\">%s</p>", $voms_sendmail_error_username); else
	if ( ! preg_match($regex_email, $sm_mailfrom) ) printf("<p class=\"error\">%s</p>", $voms_sendmail_error_email); else
	if ( empty($sm_mailtext) ) printf("<p class=\"error\">%s</p>", $voms_sendmail_error_empty); else {
		$checkok = 1;
		if ( $USER_DN === 0 ) {
			if ( !isset($_SESSION['captcha_keystring']) || !isset($_POST['captcha']) ||
			($_SESSION['captcha_keystring'] !== $_POST['captcha'])) $checkok = 0;
			if ( ! $checkok ) printf("<p class=\"error\">%s</p>", $voms_captcha_wrongvalue );
		}
		if ( $checkok ) {
			add_techinfo($sm_mailtext, $sm_username, $sm_mailfrom, $USER_DN);
			if ( isset($mail_filecopies_path)) 
				save_mail_filecopy (sprintf("mail_for_admin_%s", date("d_M_Y_H_i_s")), $sm_mailtext);
			$dontshowmailadmform_flag = send_message_for_vomsadmin($sm_username,$sm_mailfrom,$sm_mailtext);
			if ($dontshowmailadmform_flag) printf("<p class=\"alldone\">%s</p>", $voms_sendmail_ok);
				else printf("<p class=\"error\">%s</p>", $voms_sendmail_error);
		}
	}
}
if ( ! $dontshowmailadmform_flag ) showmailadmform($USER_DN,$user);
echo "</div>";
?>
