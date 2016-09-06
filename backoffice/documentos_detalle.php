<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "documentos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 3;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la documento a mostrar existe */
$objDocumentos = new clsDocumentos();
$intDocumento = $_GET["codDocumento"];
if (!$intDocumento || !$objDocumentos->getDocumentos($intDocumento, true))
	redirect("documentos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "documentos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_DOCUMENTOS", "modificar_documentos");

$objDocumentos->getDocumentosRow();
$objTemplate->set_var(array(
	"codDocumento" => $objDocumentos->intDocumento,
	"strAutor" => HTMLEntitiesFixed($objDocumentos->strAutor),
	"strTitulo" => HTMLEntitiesFixed($objDocumentos->strTitulo),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objDocumentos->strTexto)),
	"strTags" => str_replace(",", ", ", HTMLEntitiesFixed($objDocumentos->strTags)),
	"strImagen" => $objDocumentos->strImagen,
	"strArchivo" => $objDocumentos->strArchivo,
	"strFechaListado" => $objDocumentos->strFechaListado,
	"strFechaAlta" => $objDocumentos->strFechaAlta,
	"strFechaModificacion" => $objDocumentos->strFechaModificacion,
	"estadoIcono" => ($objDocumentos->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objDocumentos->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoDocumento" => ($objDocumentos->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloDocumento = $objDocumentos->strTitulo;
$intDocumento = $objDocumentos->intDocumento;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_documentos", "MODIFICAR_DOCUMENTOS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_DOCUMENTOS" => PATH_IMAGEN_DOCUMENTOS,
	"PATH_IMAGEN_DOCUMENTOS_LOCAL" => PATH_IMAGEN_DOCUMENTOS_LOCAL,
	"IMAGEN_DOCUMENTOS_CHICA_ANCHO" => IMAGEN_DOCUMENTOS_CHICA_ANCHO,
	"IMAGEN_DOCUMENTOS_CHICA_ALTO" => IMAGEN_DOCUMENTOS_CHICA_ALTO,
	"IMAGEN_DOCUMENTOS_GRANDE_ANCHO" => IMAGEN_DOCUMENTOS_GRANDE_ANCHO,
	"IMAGEN_DOCUMENTOS_GRANDE_ALTO" => IMAGEN_DOCUMENTOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloDocumento);
addBackofficeMenuCarpeta("Autores", "documentos_autores.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Documento ", $strTituloDocumento, "Desde aqu&iacute; podr&aacute; ver el detalle del documento.");

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