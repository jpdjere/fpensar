<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";

/* Inicio Session y borro todo lo que existe en ella */
if (isset($_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"]))
	unset($_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"]);

redirect("index.php");

?>