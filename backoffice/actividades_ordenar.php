<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "actividades.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 6;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto la Actividad a modificar y compruebo que exista */
$objActividades = new clsActividades();
$intActividad = (isset($_GET["codActividad"])) ? intval($_GET["codActividad"]) : 0;
$blnOrden = (isset($_GET["blnOrden"])) ? $_GET["blnOrden"] : 0;
if (!$intActividad || !$blnOrden || !$objActividades->getActividades($intActividad, true))
	redirect("actividades.php");

if ($blnOrden < -2 || $blnOrden > 2)
	redirect("actividades.php");

$objActividades->orderActividad($intActividad, $blnOrden);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("actividades.php");

?>