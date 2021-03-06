<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "notas.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 7;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la nota a mostrar existe */
$objNotas = new clsNotas();
$intMedio = (isset($_GET["codMedio"])) ? intval($_GET["codMedio"]) : 0;
if (!$intMedio || !$objNotas->getMedios($intMedio, true))
	redirect("notas_medios.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_medios_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_NOTAS", "modificar_notas");

$objNotas->getMediosRow();
$objTemplate->set_var(array(
	"codMedio" => $objNotas->intMedio,
	"strMedio" => HTMLEntitiesFixed($objNotas->strMedio),
	"strImagen" => $objNotas->strImagen,
	"strFechaAlta" => $objNotas->strFechaAlta,
	"strFechaModificacion" => $objNotas->strFechaModificacion,
	"estadoIcono" => ($objNotas->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objNotas->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoNota" => ($objNotas->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strMedio = $objNotas->strMedio;
$intMedio = $objNotas->intMedio;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_notas", "MODIFICAR_NOTAS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_MEDIOS" => PATH_IMAGEN_NOTAS_MEDIOS,
	"PATH_IMAGEN_NOTAS_MEDIOS_LOCAL" => PATH_IMAGEN_NOTAS_MEDIOS_LOCAL,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO" => IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ALTO" => IMAGEN_NOTAS_MEDIOS_CHICA_ALTO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "notas_autores.php", "users");
addBackofficeMenuCarpeta("Medios", "#", "users");
addBackofficeMenuItem($strMedio);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Medio ", $strMedio, "Desde aqu&iacute; podr&aacute; ver el detalle del medio del nota.");

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