<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "equipos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 5;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto la Equipo a modificar y compruebo que exista */
$objEquipos = new clsEquipos();
$intEquipo = (isset($_GET["codEquipo"])) ? intval($_GET["codEquipo"]) : 0;
if (!$intEquipo || !$objEquipos->getEquipos($intEquipo, false, true))
	redirect("equipos.php");

$objEquipos->getEquiposRow();
$objEquipos->setEstado($intEquipo, !$objEquipos->blnHabilitado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("equipos.php");

?>