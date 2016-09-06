<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "documentos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 3;
$intBackofficePermisoPagina = PERMISO_BAJA;
include_once("include_permisos.php");

/* Levanto la Documento a modificar y compruebo que exista */
$objDocumentos = new clsDocumentos();
$intDocumento = (isset($_GET["codDocumento"])) ? intval($_GET["codDocumento"]) : 0;
if (!$intDocumento || !$objDocumentos->getDocumentos($intDocumento, true))
	redirect("documentos.php");

$objDocumentos->deleteDocumento($intDocumento);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("documentos.php");

?>