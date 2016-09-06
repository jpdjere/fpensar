<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "eventos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 10;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto la Evento a modificar y compruebo que exista */
$objEventos = new clsEventos();
$intEvento = (isset($_GET["codEvento"])) ? intval($_GET["codEvento"]) : 0;
if (!$intEvento || !$objEventos->getEventos($intEvento, true))
	redirect("eventos.php");

$objEventos->getEventosRow();
$objEventos->setEstado($intEvento, !$objEventos->blnHabilitado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("eventos.php");

?>