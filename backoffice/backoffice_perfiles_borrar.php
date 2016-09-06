<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_BAJA;
include_once("include_permisos.php");

/* Levanto el perfil a modificar y compruebo que exista */
$intPerfil = (isset($_GET["codPerfil"])) ? intval($_GET["codPerfil"]) : 0;
$objBackOfficePerfiles->deletePerfil($intPerfil);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("perfiles.php?intPerfil=" . $intPerfil);

?>