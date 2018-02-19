<?php
$is_service = ( $action === "register_service" ) ? 1 : 0;

$vo_registration_enabled = isset($vo_registration_enabled) ? $vo_registration_enabled : 1;
echo "<div id=\"submitform\">\n";
if ( $member && ! $is_service ) {
	printf( "<p class=\"alldone\">%s</p>", $voms_register_allready); 
} else if ( ! $vo_registration_enabled ) {
	printf( "<p class=\"error\">%s</p>", $voms_register_disabled);
} else if ( ! checkDBSchema() ) {
	printf( "<p class=\"error\">%s</p>", $voms_dbschema_usernotify);
} else if (isset ($_SERVER['HTTPS'])) {
	if ( ( $USER_DN !== 0 ) && ( $USER_CA !== 0 ) ) {
    	$reghb = 0;
        $regmail  = _ispost("regmail");
        $regdn    = _ispost("regdn");
        $regca    = _ispost("regca", $USER_CA);
		$reginst  = _ispost("reginst");
		$regphone = _ispost("regphone");
		$regcomments = _ispost("regcomments");

        if ( $is_service ) {
            if ( empty($regdn) ) $req_exists = 0;
            else $req_exists = requestExists ($regdn, $regca);
        } else {
    	    $req_exists = requestExists ($USER_DN, $USER_CA);
        }

		if ( $req_exists == 1 ) {
		    printf( "<p class=\"error\">%s</p>", $voms_register_notyet_processed);
    		$reghb = 1;
	    } else if ( $req_exists == -1 ) {
		   	printf( "<p class=\"error\">%s</p>", $voms_register_confirmation_sent);
    	    $reghb = 1;
    	}

		// Proceed with form submission
		if ( ( $reghb == 0 ) && isset($_POST["regsub"] ) ) {
			$regagree = ( isset($_POST["agree"]) ) ? 1 : 0;
			// Check userinput
			if ( ! preg_match($regex_email, $regmail) ) printf("<p class=\"error\">%s</p>", $voms_register_invalid_mail); else
			if ( ! preg_match($regex_title, $reginst ) && ! $is_service ) printf("<p class=\"error\">%s</p>", $voms_register_invalid_inst); else
			if ( ! preg_match($regex_phone, $regphone) && ! $is_service ) printf("<p class=\"error\">%s</p>", $voms_register_invalid_phone); else
			if ( ! $regagree )  printf("<p class=\"error\">%s</p>", $voms_register_invalid_agree); else {
				// if we are still here -- accept register request
                if ( $is_service ) { 
                    $reginst = "Service record";
                    $regphone = "N/A";
                    if ( ! empty($regcomments) ) $regcomments .= ", ";
                    $regcomments .= "admin DN: $USER_DN";
                } else {
                    // do not allow members to register members via POST request fake :)
                    $regdn = $USER_DN;
                    $regca = $USER_CA;
                }
				if ( addNewRequest($vo, $regdn, $regca, $regmail, $reginst, $regphone, $regcomments, $is_service ) ) {
					printf("<p class=\"underborder bold\" >%s</p>\n",$voms_register_confirmation);
					printf("<p>%s</p>", sprintf($voms_register_complete, $vo));
					$reghb = 1;
				} else printf("<p class=\"error\">%s</p>", $voms_register_error);
			}
		}
		if ( $reghb == 0 ) {
            if ( $is_service ) {
                printf("<p class=\"underborder bold\" >$voms_register_caption</p>\n<a href=\"?vo=%s&amp;action=register\" >%s</a>\n",
                    $voms_register_service,$vo,$vo,$voms_register_switch_user);
            } else {
                printf("<p class=\"underborder bold\" >$voms_register_caption</p>\n<a href=\"?vo=%s&amp;action=register_service\" >%s</a>\n",
                    $voms_register_user,$vo,$vo,$voms_register_switch_service);
            }

			// Print explanations about registration
			printf("<p>".$voms_register_preamble."<p>",$vo_rules_link);
			foreach ( $voms_register_preamble_next as $vrptext )
				printf("<p>%s</p>", $vrptext);
			// Regisrtration request submittion form
			echo "<form method=\"post\" action=\"\">\n";
            if ( $is_service ) {
    			printf("<label>%s</label><input type=\"text\" name=\"admindn\" disabled class=\"greenselected noborder\" 
                    value=\"%s\" /><br/><br/>\n", $voms_register_yourdn, $USER_DN);
    			printf("<label>%s</label><input type=\"text\" name=\"regdn\" class=\"greenselected\" 
                    value=\"%s\" /><br/><br/>\n", $voms_register_servicedn, $regdn);
	    		printf("<label>%s</label><input type=\"text\" name=\"regca\" class=\"greenselected\" 
                    value=\"%s\" /><br/><br/>\n", $voms_register_serviceca, $regca);
			    printf("<label>%s</label><input type=\"text\" name=\"regmail\" value=\"%s\" /><br/><br/>\n",$voms_register_servicemail, $regmail);
            } else {
    			printf("<label>%s</label><input type=\"text\" name=\"regdn\" disabled class=\"greenselected noborder\" 
                    value=\"%s\" /><br/><br/>\n", $voms_register_yourdn, $USER_DN);
	    		printf("<label>%s</label><input type=\"text\" name=\"regca\" disabled class=\"greenselected noborder\" 
                    value=\"%s\" /><br/><br/>\n", $voms_register_yourca, $USER_CA);
			    printf("<label>%s</label><input type=\"text\" name=\"regmail\" value=\"%s\" /><br/><br/>\n",$voms_register_yourmail, $regmail);
    			printf("<label>%s</label><input type=\"text\" name=\"reginst\" value=\"%s\" /><br/><br/>\n",$voms_register_yourinstitute, $reginst);
	    		printf("<label>%s</label><input type=\"text\" name=\"regphone\" value=\"%s\" /><br/><br/>\n",$voms_register_yourphone, $regphone);
            }
			printf("<label>%s</label><textarea cols=\"60\" rows=\"4\" name=\"regcomments\">%s</textarea><br/><br/>\n",$voms_register_yourcomments, $regcomments);
			printf("<input type=\"checkbox\" style=\"width: auto;\" name=\"agree\"> %s</input>\n", $voms_register_agree);
			printf("<input class=\"crnewbt\" type=\"submit\" name=\"regsub\" value=\"%s\" />\n", $voms_register_submit );
			echo "</form>\n";
		}
	} else printf("<p class=\"error\">%s</p>", $voms_register_no_cert);
} else printf("<p class=\"error\">".$voms_register_non_https."</p>", "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
echo "<div class=\"mt20\"></div>\n";
echo "</div>\n";
?>
