<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "redes.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 12;
$intBackofficePermisoPagina = PERMISO_BAJA;
include_once("include_permisos.php");

/* Levanto la Red a modificar y compruebo que exista */
$objRedes = new clsRedes();
$intRed = (isset($_GET["codRed"])) ? intval($_GET["codRed"]) : 0;
if (!$intRed || !$objRedes->getRedes($intRed, false, true))
	redirect("redes.php");

$objRedes->deleteRed($intRed);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("redes.php");

?>