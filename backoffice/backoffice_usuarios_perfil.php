<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";

$strUsuarioLogueadoBackoffice = (isset($_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"])) ? $_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"] : "";
if (!$strUsuarioLogueadoBackoffice)
	redirect("timeout.php");

redirect("backoffice_usuarios_detalle.php?codUsuario=" . $strUsuarioLogueadoBackoffice);

?>