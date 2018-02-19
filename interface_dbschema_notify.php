<?php
if ( ! checkDBSchema() ) {
	printf("<div id=\"notify\" style=\"margin-bottom: 15px;\" >%s</div>", $voms_dbschema_nofity );
}
?>
