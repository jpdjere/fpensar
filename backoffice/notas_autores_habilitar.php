<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "notas.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 7;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si la nota a mostrar existe */
$objNotas = new clsNotas();
$intAutor = (isset($_GET["codAutor"])) ? intval($_GET["codAutor"]) : 0;
if (!$intAutor || !$objNotas->getAutores($intAutor, true))
	redirect("notas_autores.php");

$objNotas->getAutoresRow();
$objNotas->setEstadoAutor($intAutor, !$objNotas->blnHabilitado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("notas_autores.php");

?>