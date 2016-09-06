<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "actividades.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 6;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la actividad a mostrar existe */
$objActividades = new clsActividades();
$intActividad = $_GET["codActividad"];
if (!$intActividad || !$objActividades->getActividades($intActividad, true))
	redirect("actividades.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "actividades_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_ACTIVIDADES", "modificar_actividades");

$objActividades->getActividadesRow();
$objTemplate->set_var(array(
	"codActividad" => $objActividades->intActividad,
	"strTitulo" => HTMLEntitiesFixed($objActividades->strTitulo),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objActividades->strTexto)),
	"strImagen" => ($objActividades->strImagen) ? $objActividades->strImagen : IMAGEN_NO_DISPONIBLE,
	"strFechaAlta" => $objActividades->strFechaAlta,
	"strFechaModificacion" => $objActividades->strFechaModificacion,
	"estadoIcono" => ($objActividades->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objActividades->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoActividad" => ($objActividades->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloActividad = $objActividades->strTitulo;
$intActividad = $objActividades->intActividad;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_actividades", "MODIFICAR_ACTIVIDADES");

$objTemplate->set_var(array(
	"PATH_IMAGEN_ACTIVIDADES" => PATH_IMAGEN_ACTIVIDADES,
	"PATH_IMAGEN_ACTIVIDADES_LOCAL" => PATH_IMAGEN_ACTIVIDADES_LOCAL,
	"IMAGEN_ACTIVIDADES_ANCHO" => IMAGEN_ACTIVIDADES_ANCHO,
	"IMAGEN_ACTIVIDADES_ALTO" => IMAGEN_ACTIVIDADES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloActividad);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Actividad ", $strTituloActividad, "Desde aqu&iacute; podr&aacute; ver el detalle de la actividad.");

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