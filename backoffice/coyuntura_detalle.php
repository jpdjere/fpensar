<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "coyuntura.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 8;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la coyuntura a mostrar existe */
$objCoyuntura = new clsCoyuntura();
$intCoyuntura = $_GET["codCoyuntura"];
if (!$intCoyuntura || !$objCoyuntura->getCoyuntura($intCoyuntura, true))
	redirect("coyuntura.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "coyuntura_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_COYUNTURA", "modificar_coyuntura");

$objCoyuntura->getCoyunturaRow();
$objTemplate->set_var(array(
	"codCoyuntura" => $objCoyuntura->intCoyuntura,
	"strTitulo" => HTMLEntitiesFixed($objCoyuntura->strTitulo),
	"strSeccionInternacional" => HTMLEntitiesFixed($objCoyuntura->strSeccionInternacional),
	"strSeccionEconomia" => HTMLEntitiesFixed($objCoyuntura->strSeccionEconomia),
	"strSeccionPolitica" => HTMLEntitiesFixed($objCoyuntura->strSeccionPolitica),
	"strImagen" => $objCoyuntura->strImagen,
	"strArchivo" => $objCoyuntura->strArchivo,
	"strFechaListado" => $objCoyuntura->strFechaListado,
	"strFechaAlta" => $objCoyuntura->strFechaAlta,
	"strFechaModificacion" => $objCoyuntura->strFechaModificacion,
	"estadoIcono" => ($objCoyuntura->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objCoyuntura->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoCoyuntura" => ($objCoyuntura->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloCoyuntura = $objCoyuntura->strTitulo;
$intCoyuntura = $objCoyuntura->intCoyuntura;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_coyuntura", "MODIFICAR_COYUNTURA");

$objTemplate->set_var(array(
	"PATH_IMAGEN_COYUNTURA" => PATH_IMAGEN_COYUNTURA,
	"PATH_IMAGEN_COYUNTURA_LOCAL" => PATH_IMAGEN_COYUNTURA_LOCAL,
	"IMAGEN_COYUNTURA_CHICA_ANCHO" => IMAGEN_COYUNTURA_CHICA_ANCHO,
	"IMAGEN_COYUNTURA_CHICA_ALTO" => IMAGEN_COYUNTURA_CHICA_ALTO,
	"IMAGEN_COYUNTURA_GRANDE_ANCHO" => IMAGEN_COYUNTURA_GRANDE_ANCHO,
	"IMAGEN_COYUNTURA_GRANDE_ALTO" => IMAGEN_COYUNTURA_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloCoyuntura);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de edici&oacute;n de Coyuntura ", $strTituloCoyuntura, "Desde aqu&iacute; podr&aacute; ver el detalle de una edici&oacute;n de Coyuntura.");

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