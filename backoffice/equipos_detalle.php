<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "equipos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 5;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si la equipo a mostrar existe */
$objEquipos = new clsEquipos();
$intEquipo = $_GET["codEquipo"];
if (!$intEquipo || !$objEquipos->getEquipos($intEquipo, false, true))
	redirect("equipos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "equipos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_EQUIPOS", "modificar_equipos");

$objEquipos->getEquiposRow();
$objTemplate->set_var(array(
	"codEquipo" => $objEquipos->intEquipo,
	"strNombre" => HTMLEntitiesFixed($objEquipos->strNombre),
	"strCargo" => HTMLEntitiesFixed($objEquipos->strCargo),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto)),
	"strImagen" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
	"strGrupo" => HTMLEntitiesFixed($objEquipos->strGrupo),
	"strUsuarioTwitter" => HTMLEntitiesFixed($objEquipos->strUsuarioTwitter),
	"strFacebookURL" => HTMLEntitiesFixed($objEquipos->strFacebookURL),
	"strTwitterURL" => HTMLEntitiesFixed($objEquipos->strTwitterURL),
	"strFechaAlta" => $objEquipos->strFechaAlta,
	"strFechaModificacion" => $objEquipos->strFechaModificacion,
	"estadoIcono" => ($objEquipos->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objEquipos->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoEquipo" => ($objEquipos->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strNombreEquipo = $objEquipos->strNombre;
$intEquipo = $objEquipos->intEquipo;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_equipos", "MODIFICAR_EQUIPOS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_EQUIPOS" => PATH_IMAGEN_EQUIPOS,
	"PATH_IMAGEN_EQUIPOS_LOCAL" => PATH_IMAGEN_EQUIPOS_LOCAL,
	"IMAGEN_EQUIPOS_CHICA_ANCHO" => IMAGEN_EQUIPOS_CHICA_ANCHO,
	"IMAGEN_EQUIPOS_CHICA_ALTO" => IMAGEN_EQUIPOS_CHICA_ALTO,
	"IMAGEN_EQUIPOS_GRANDE_ANCHO" => IMAGEN_EQUIPOS_GRANDE_ANCHO,
	"IMAGEN_EQUIPOS_GRANDE_ALTO" => IMAGEN_EQUIPOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strNombreEquipo);
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Equipo ", $strNombreEquipo, "Desde aqu&iacute; podr&aacute; ver el detalle de la equipo.");

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