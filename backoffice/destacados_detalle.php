<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "destacados.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 9;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la destacado a mostrar existe */
$objDestacados = new clsDestacados();
$intDestacado = $_GET["codDestacado"];
if (!$intDestacado || !$objDestacados->getDestacados($intDestacado, false, true))
	redirect("destacados.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "destacados_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_DESTACADOS", "modificar_destacados");

$objDestacados->getDestacadosRow();
$objTemplate->set_var(array(
	"codDestacado" => $objDestacados->intDestacado,
	"strTitulo" => HTMLEntitiesFixed($objDestacados->strTitulo),
	"strPosicion" => HTMLEntitiesFixed($objDestacados->strPosicion),
	"strLinkURL" => HTMLEntitiesFixed($objDestacados->strLinkURL),
	"strImagen" => ($objDestacados->strImagen) ? $objDestacados->strImagen : IMAGEN_NO_DISPONIBLE,
	"strFechaAlta" => $objDestacados->strFechaAlta,
	"strFechaModificacion" => $objDestacados->strFechaModificacion,
	"estadoIcono" => ($objDestacados->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objDestacados->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoDestacado" => ($objDestacados->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloDestacado = $objDestacados->strTitulo;
$intDestacado = $objDestacados->intDestacado;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_destacados", "MODIFICAR_DESTACADOS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_DESTACADOS" => PATH_IMAGEN_DESTACADOS,
	"PATH_IMAGEN_DESTACADOS_LOCAL" => PATH_IMAGEN_DESTACADOS_LOCAL,
	"IMAGEN_DESTACADOS_ANCHO" => IMAGEN_DESTACADOS_ANCHO,
	"IMAGEN_DESTACADOS_ALTO" => IMAGEN_DESTACADOS_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloDestacado);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Destacado ", $strTituloDestacado, "Desde aqu&iacute; podr&aacute; ver el detalle de la destacado.");

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