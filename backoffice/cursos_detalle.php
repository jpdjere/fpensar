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

/* Me fijo si la curso a mostrar existe */
$objCursos = new clsCursos();
$intCurso = $_GET["codCurso"];
if (!$intCurso || !$objCursos->getCursos($intCurso, true))
	redirect("cursos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_CURSOS", "modificar_cursos");

$objCursos->getCursosRow();
$objTemplate->set_var(array(
	"codCurso" => $objCursos->intCurso,
	"strCurso" => HTMLEntitiesFixed($objCursos->strCurso),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objCursos->strTexto)),
	"strFechaInicioInscripcion" => $objCursos->strFechaInicioInscripcion,
	"strFechaFinInscripcion" => $objCursos->strFechaFinInscripcion,
	"strFecha" => $objCursos->strFecha,
	"intCupos" => $objCursos->intCupos,
	"intInscriptos" => $objCursos->intInscriptos,
	"strFechaAlta" => $objCursos->strFechaAlta,
	"strFechaModificacion" => $objCursos->strFechaModificacion,
	"estadoIcono" => ($objCursos->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objCursos->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoCurso" => ($objCursos->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strCurso = $objCursos->strCurso;
$intCurso = $objCursos->intCurso;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_cursos", "MODIFICAR_CURSOS");

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strCurso);
addBackofficeMenuCarpeta("INSCRIPTOS", "cursos_inscriptos.php?codCurso=" . $intCurso);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Curso ", $strCurso, "Desde aqu&iacute; podr&aacute; ver el detalle del curso.");

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