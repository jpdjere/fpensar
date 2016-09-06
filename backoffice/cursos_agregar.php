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
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 11;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("cursos_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strCurso = (isset($_POST["strCurso"])) ? stripSlashes(trim($_POST["strCurso"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strFechaInicioInscripcion = (isset($_POST["strFechaInicioInscripcion"])) ? trim($_POST["strFechaInicioInscripcion"]) : "";
	$strFechaFinInscripcion = (isset($_POST["strFechaFinInscripcion"])) ? trim($_POST["strFechaFinInscripcion"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$intCupos = (isset($_POST["intCupos"])) ? trim($_POST["intCupos"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Curso */
	$objCursos = new clsCursos();
	if ($objCursos->insertCurso($strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos, $blnHabilitado))
		redirect("cursos_detalle.php?codCurso=" . $objCursos->intCurso);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strCursoCurso" => HTMLEntitiesFixed(capitalize($strCurso)),
			"strCurso" => HTMLEntitiesFixed(capitalizeFirst($strCurso)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strFechaInicioInscripcion" => $strFechaInicioInscripcion,
			"strFechaFinInscripcion" => $strFechaFinInscripcion,
			"strFecha" => $strFecha,
			"intCupos" => $intCupos,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorCurso" => $objCursos->errorCurso,
			"errorTexto" => $objCursos->errorTexto,
			"errorFechaInicioInscripcion" => $objCursos->errorFechaInicioInscripcion,
			"errorFechaFinInscripcion" => $objCursos->errorFechaFinInscripcion,
			"errorFecha" => $objCursos->errorFecha,
			"errorCupos" => $objCursos->errorCupos
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strCursoCurso" => "Nueva Curso",
		"strCurso" => "",
		"strTexto" => "",
		"strFechaInicioInscripcion" => "",
		"strFechaFinInscripcion" => "",
		"strFecha" => "",
		"intCupos" => "",
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objCursos = new clsCursos();
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nuevo Curso", false, "Desde aqu&iacute; podr&aacute; agregar un nuevo curso al sitio");

/* Seteo variables Comunes */
$objTemplate->set_var(array(
	"WEB_TITLE" => ":: $strNombreEmpresa : Backoffice :: ",
	"WEB_DESCRIPTION" => $strDescripcionSitio,
	"WEB_KEYWORDS" => $strKeywordsSitio,
	"NOMBRE_EMPRESA" => $strNombreEmpresa,
	"FECHA" => getFecha()
));

/* Parseo Templates */
$objTemplate->parseArray(array(
	"FOOTER" => "FOOTER",
	"OPCIONES" => "OPCIONES",
	"PAGINA" => "PAGINA",
	"ENCABEZADO" => "ENCABEZADO",
	"MENU" => "MENU",
	"HEADER" => "HEADER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out");

?>