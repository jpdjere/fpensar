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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto la Destacado a modificar y compruebo que exista */
$objDestacados = new clsDestacados();
$intDestacado = (isset($_GET["codDestacado"])) ? intval($_GET["codDestacado"]) : 0;
$blnOrden = (isset($_GET["blnOrden"])) ? $_GET["blnOrden"] : 0;
if (!$intDestacado || !$blnOrden || !$objDestacados->getDestacados($intDestacado, true))
	redirect("destacados.php");

if ($blnOrden < -2 || $blnOrden > 2)
	redirect("destacados.php");

$objDestacados->orderDestacado($intDestacado, $blnOrden);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("destacados.php");

?>