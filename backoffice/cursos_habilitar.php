<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "cursos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 11;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Levanto la Curso a modificar y compruebo que exista */
$objCursos = new clsCursos();
$intCurso = (isset($_GET["codCurso"])) ? intval($_GET["codCurso"]) : 0;
if (!$intCurso || !$objCursos->getCursos($intCurso, true))
	redirect("cursos.php");

$objCursos->getCursosRow();
$objCursos->setEstado($intCurso, !$objCursos->blnHabilitado);

if ($_SERVER["HTTP_REFERER"])
	redirect($_SERVER["HTTP_REFERER"]);
else
	redirect("cursos.php");

?>