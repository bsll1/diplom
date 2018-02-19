<?php
echo "<div id=\"manage\">\n";
printf("<p>%s</p>\n",$voms_preferences_options_title);
echo "<ul>";
foreach ( $voms_preferences_options as $vmk => $vmv )
	printf("<li><a href=\"?vo=%s%s\">%s</a></li>\n",$vo,$vmv,$vmk);
echo "</ul>";
echo "</div>\n";
?>
