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
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

// Levanto Curso
$intCurso = (isset($_GET["codCurso"])) ? intval($_GET["codCurso"]) : "";
if (!$intCurso)
	redirect("cursos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos_inscriptos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "INSCRIPTOS", "inscriptos");
$objTemplate->set_block("PAGINA", "INSCRIPTOS_VACIO", "inscriptos_vacio");

$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto el orden  y direccion */
$intOrden = isset($_GET["o"]) ? intval($_GET["o"]) : 0;
if (!$intOrden || $intOrden < 1 || $intOrden > 3)
	$intOrden = 3;
$intDireccion = isset($_GET["d"]) ? intval($_GET["d"]) : 0;
if (!$intDireccion || ($intDireccion != 1 && $intDireccion != 2))
	$intDireccion = (($intOrden == 3) ? 2 : 1);

/* Traigo un Listado de todos los Cursos del backoffice */
$objCursos = new clsCursos();
$objCursos->getInscriptos($intCurso, false, $intOrden, $intDireccion, $strBusqueda);

/* Levanto la pagina a mostrar */
$intPagina = isset($_GET["intPagina"]) ? $_GET["intPagina"] : "";
if (!$intPagina)
	$intPagina = 1;

$intCantidadRegistros = $objCursos->intTotal;
$objCursos->getInscriptosRow(0);

if ($objCursos->intTotal && $objCursos->strEmail){
	for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
		$objCursos->getInscriptosRow($i);
		$objTemplate->set_var(array(
			"intInscripto" => $objCursos->intInscripto,
			"codInscripto" => $objCursos->intInscripto,
			"strCurso" => HTMLEntitiesFixed($objCursos->strCurso),
			"strNombre" => HTMLEntitiesFixed(capitalizeFirst($objCursos->strNombre)),
			"strApellido" => HTMLEntitiesFixed(capitalizeFirst($objCursos->strApellido)),
			"strEmail" => HTMLEntitiesFixed($objCursos->strEmail),
			"strProvincia" => HTMLEntitiesFixed($objCursos->strProvincia),
			"strTelefono" => HTMLEntitiesFixed($objCursos->strTelefono),
			"strFecha" => $objCursos->strFechaListado
		));

		$objTemplate->parse("inscriptos", "INSCRIPTOS", true);
	}
	$objTemplate->set_var("inscriptos_vacio", "");

}else{
	$objTemplate->set_var("inscriptos", "");
	$objTemplate->parse("inscriptos_vacio", "INSCRIPTOS_VACIO");
}

$objTemplate->set_var(array(
	"codCurso" => $intCurso,
	"txtBusqueda" => $strBusqueda
));
$strCurso = $objCursos->strCurso;

// Parseo Orden
$objTemplate->set_var("strOrdenParameter", "?o=");

// Parseo Direccion
for ($i = 1; $i <= 3; $i++){
	$objTemplate->set_var("strDireccionOrden" . $i, ($intOrden == $i) ? (($intDireccion == 1) ? 2 : 1) : 1);
}

/* Incluyo Paginador */
$strPage = "cursos";
$strParameters = "o=" . $intOrden . "&d=" . $intDireccion;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var("strExportParameters", "?" . $strParameters);

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strCurso, "cursos_detalle.php?codCurso=" . $intCurso);
addBackofficeMenuCarpeta("INSCRIPTOS", "cursos_inscriptos.php?codCurso=" . $intCurso);
setBackofficeMenu();
setBackofficeEncabezado("Listado de Inscriptos a curso " . $strCurso, " (" . $intCantidadRegistros . ")", "Desde aqu&iacute; Ud. puede ver todos los cursos del sitio.");

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