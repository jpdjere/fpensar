<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "destacados.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 9;
$intBackofficePermisoPagina = PERMISO_BAJA;
include_once("include_permisos.php");

/* Levanto la Destacado a modificar y compruebo que exista */
$objDestacados = new clsDestacados();
$intDestacado = (isset($_GET["codDestacado"])) ? intval($_GET["codDestacado"]) : 0;
if (!$intDestacado || !$objDestacados->getDestacados($intDestacado, false, true))
	redirect("destacados.php");

$objDestacados->deleteDestacado($intDestacado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("destacados.php");

?>