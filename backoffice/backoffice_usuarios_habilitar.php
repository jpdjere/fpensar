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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto el usuario a modificar y compruebo que exista */
$strUsuarioNombre = (isset($_GET["codUsuario"])) ? $_GET["codUsuario"] : "";
if (!$strUsuarioNombre || !$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre))
	redirect("backoffice_usuarios.php");

$objBackOfficeUsuarios->getUsuariosRow();
$objBackOfficeUsuarios->setEstado($strUsuarioNombre, !$objBackOfficeUsuarios->blnHabilitado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("backoffice_usuarios.php");

?>