<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "eventos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 10;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la evento a mostrar existe */
$objEventos = new clsEventos();
$intEvento = $_GET["codEvento"];
if (!$intEvento || !$objEventos->getEventos($intEvento, true))
	redirect("eventos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "eventos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_EVENTOS", "modificar_eventos");

$objEventos->getEventosRow();
$objTemplate->set_var(array(
	"codEvento" => $objEventos->intEvento,
	"strTitulo" => HTMLEntitiesFixed($objEventos->strTitulo),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objEventos->strTexto)),
	"strImagen" => $objEventos->strImagen,
	"strArchivo" => $objEventos->strArchivo,
	"strFechaListado" => $objEventos->strFechaListado,
	"strFechaAlta" => $objEventos->strFechaAlta,
	"strFechaModificacion" => $objEventos->strFechaModificacion,
	"estadoIcono" => ($objEventos->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objEventos->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoEvento" => ($objEventos->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloEvento = $objEventos->strTitulo;
$intEvento = $objEventos->intEvento;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_eventos", "MODIFICAR_EVENTOS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_EVENTOS" => PATH_IMAGEN_EVENTOS,
	"PATH_IMAGEN_EVENTOS_LOCAL" => PATH_IMAGEN_EVENTOS_LOCAL,
	"IMAGEN_EVENTOS_CHICA_ANCHO" => IMAGEN_EVENTOS_CHICA_ANCHO,
	"IMAGEN_EVENTOS_CHICA_ALTO" => IMAGEN_EVENTOS_CHICA_ALTO,
	"IMAGEN_EVENTOS_GRANDE_ANCHO" => IMAGEN_EVENTOS_GRANDE_ANCHO,
	"IMAGEN_EVENTOS_GRANDE_ALTO" => IMAGEN_EVENTOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloEvento);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Evento ", $strTituloEvento, "Desde aqu&iacute; podr&aacute; ver el detalle del evento.");

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