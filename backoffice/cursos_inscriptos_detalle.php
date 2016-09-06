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

/* Me fijo si el curso a mostrar existe */
$intCurso = (isset($_GET["codCurso"])) ? intval($_GET["codCurso"]) : 0;
$intInscripto = (isset($_GET["codInscripto"])) ? intval($_GET["codInscripto"]) : 0;
if (!$intCurso)
	redirect("cursos.php");
if (!$intInscripto)
	redirect("cursos_inscriptos.php?codCurso=" . $intCurso);

$objCurso = new clsCursos();
$objCurso->getInscriptos($intCurso, $intInscripto);
if (!$objCurso->intTotal)
	redirect("cursos_inscriptos.php?codCurso=" . $intCurso);

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos_inscriptos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Traigo el Curso del backoffice */
$objCurso->getInscriptosRow();

/* Seteo todos los datos de Curso */
$objTemplate->set_var(array(
	"intInscripto" => $objCurso->intInscripto,
	"codInscripto" => $objCurso->intInscripto,
	"strNombre" => HTMLEntitiesFixed(capitalizeFirst($objCurso->strNombre)),
	"strApellido" => HTMLEntitiesFixed(capitalizeFirst($objCurso->strApellido)),
	"strDNI" => HTMLEntitiesFixed($objCurso->strDNI),
	"strEmail" => HTMLEntitiesFixed($objCurso->strEmail),
	"strProvincia" => HTMLEntitiesFixed(capitalizeFirst($objCurso->strProvincia)),
	"strTelefono" => HTMLEntitiesFixed($objCurso->strTelefono),
	"strFecha" => $objCurso->strFecha,
));

$strCurso = $objCurso->strCurso;
$strInscripto = ($objCurso->strNombre) ? capitalizeFirst($objCurso->strNombre) . " " . capitalizeFirst($objCurso->strApellido) : $objCurso->strEmail;
$objTemplate->set_var("codCurso", $intCurso);

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strCurso, "cursos_detalle.php?codCurso=" . $intCurso);
addBackofficeMenuCarpeta("INSCRIPTOS", "cursos_inscriptos.php?codCurso=" . $intCurso);
addBackofficeMenuItem($strInscripto);
setBackofficeMenu();
setBackofficeEncabezado("Detalle de inscripto en curso ", $strCurso, "Desde aqu&iacute; Ud. puede ver los datos del inscripto en el curso seleccionado.");

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