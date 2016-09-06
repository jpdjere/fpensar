<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "redes.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 12;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la red a mostrar existe */
$objRedes = new clsRedes();
$intRed = $_GET["codRed"];
if (!$intRed || !$objRedes->getRedes($intRed, false, true))
	redirect("redes.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "redes_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_REDES", "modificar_redes");

$objRedes->getRedesRow();
$objTemplate->set_var(array(
	"codRed" => $objRedes->intRed,
	"strTitulo" => HTMLEntitiesFixed($objRedes->strTitulo),
	"strProvincia" => HTMLEntitiesFixed($objRedes->strProvincia),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objRedes->strTexto)),
	"strImagen" => ($objRedes->strImagen) ? $objRedes->strImagen : IMAGEN_NO_DISPONIBLE,
	"strFechaAlta" => $objRedes->strFechaAlta,
	"strFechaModificacion" => $objRedes->strFechaModificacion,
	"estadoIcono" => ($objRedes->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objRedes->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoRed" => ($objRedes->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloRed = $objRedes->strTitulo;
$intRed = $objRedes->intRed;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_redes", "MODIFICAR_REDES");

$objTemplate->set_var(array(
	"PATH_IMAGEN_REDES" => PATH_IMAGEN_REDES,
	"PATH_IMAGEN_REDES_LOCAL" => PATH_IMAGEN_REDES_LOCAL,
	"IMAGEN_REDES_ANCHO" => IMAGEN_REDES_ANCHO,
	"IMAGEN_REDES_ALTO" => IMAGEN_REDES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloRed);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Red ", $strTituloRed, "Desde aqu&iacute; podr&aacute; ver el detalle de la red.");

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