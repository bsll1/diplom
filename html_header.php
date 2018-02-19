<?php
/*echo "<head>\n";
	echo "\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
	echo "\t<meta name=\"author\" content=\"Andrii Salnikov\" />\n";
	printf("\t<title>%s</title>",sprintf($voms_title,$pva_version));
	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"./styles/main.css\" />\n";
	echo "\t<script src=\"./js/functions.js\" type=\"text/javascript\" ></script>\n";
echo "</head>\n";

echo "<body>\n";
echo "<div id=\"center\">\n";*/
?>
<?php
	$smarty->assign("voms_title", $voms_title);
	$smarty->assign("pva_version", $pva_version);
	$smarty->display('templates/html_header.tpl');
?>
