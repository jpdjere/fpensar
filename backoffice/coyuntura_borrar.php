<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "coyuntura.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 8;
$intBackofficePermisoPagina = PERMISO_BAJA;
include_once("include_permisos.php");

/* Levanto la Coyuntura a modificar y compruebo que exista */
$objCoyuntura = new clsCoyuntura();
$intCoyuntura = (isset($_GET["codCoyuntura"])) ? intval($_GET["codCoyuntura"]) : 0;
if (!$intCoyuntura || !$objCoyuntura->getCoyuntura($intCoyuntura, true))
	redirect("coyuntura.php");

$objCoyuntura->deleteCoyuntura($intCoyuntura);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("coyuntura.php");

?>