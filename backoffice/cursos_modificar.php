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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si el auto a mostrar existe */
$objCursos = new clsCursos();
$intCurso = (isset($_GET["codCurso"])) ? $_GET["codCurso"] : $_POST["codCurso"];
if (!$intCurso || !$objCursos->getCursos($intCurso, true))
	redirect("cursos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("cursos_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intCurso = (isset($_POST["codCurso"])) ? intval($_POST["codCurso"]) : "";
	$strCurso = (isset($_POST["strCurso"])) ? stripSlashes(trim($_POST["strCurso"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strFechaInicioInscripcion = (isset($_POST["strFechaInicioInscripcion"])) ? trim($_POST["strFechaInicioInscripcion"]) : "";
	$strFechaFinInscripcion = (isset($_POST["strFechaFinInscripcion"])) ? trim($_POST["strFechaFinInscripcion"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$intCupos = (isset($_POST["intCupos"])) ? trim($_POST["intCupos"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$strCursoCurso = $strCurso;

	/* Inserto Curso */
	$objCursos = new clsCursos();
	if ($objCursos->updateCurso($intCurso, $strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos, $blnHabilitado))
		redirect("cursos_detalle.php?codCurso=" . $intCurso);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codCurso" => $intCurso,
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

	/* Levanto los datos del Curso */
	$objCursos->getCursosRow();
	$objTemplate->set_var(array(
		"strCursoCurso" => HTMLEntitiesFixed(capitalize($objCursos->strCurso)),
		"codCurso" => $objCursos->intCurso,
		"strCurso" => HTMLEntitiesFixed($objCursos->strCurso),
		"strTexto" => showTextBreaks(HTMLEntitiesFixed($objCursos->strTexto), true),
		"strFechaInicioInscripcion" => $objCursos->strFechaInicioInscripcion,
		"strFechaFinInscripcion" => $objCursos->strFechaFinInscripcion,
		"strFecha" => $objCursos->strFecha,
		"intCupos" => $objCursos->intCupos,
		"blnHabilitado" => ($objCursos->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objCursos->blnHabilitado) ? "" : "checked"
	));

	$strCursoCurso = $objCursos->strCurso;
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strCursoCurso);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar Curso ", $strCursoCurso, "Desde aqu&iacute; podr&aacute; modificar un curso.");

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