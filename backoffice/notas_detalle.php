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
$intNota = $_GET["codNota"];
if (!$intNota || !$objNotas->getNotas($intNota, true))
	redirect("notas.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_NOTAS", "modificar_notas");

$objNotas->getNotasRow();
$objTemplate->set_var(array(
	"codNota" => $objNotas->intNota,
	"strAutor" => HTMLEntitiesFixed($objNotas->strAutor),
	"strTitulo" => HTMLEntitiesFixed($objNotas->strTitulo),
	"strTexto" => showTextBreaks(HTMLEntitiesFixed($objNotas->strTexto)),
	"strImagen" => $objNotas->strImagen,
	"strArchivo" => $objNotas->strArchivo,
	"strMedio" => $objNotas->strMedio,
	"strImagenMedio" => $objNotas->strImagenMedio,
	"strLinkURL" => $objNotas->strLinkURL,
	"strFechaListado" => $objNotas->strFechaListado,
	"strFechaAlta" => $objNotas->strFechaAlta,
	"strFechaModificacion" => $objNotas->strFechaModificacion,
	"estadoIcono" => ($objNotas->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objNotas->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoNota" => ($objNotas->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strTituloNota = $objNotas->strTitulo;
$intNota = $objNotas->intNota;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_notas", "MODIFICAR_NOTAS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_MEDIOS" => PATH_IMAGEN_NOTAS_MEDIOS,
	"PATH_IMAGEN_NOTAS_MEDIOS_LOCAL" => PATH_IMAGEN_NOTAS_MEDIOS_LOCAL,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO" => IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ALTO" => IMAGEN_NOTAS_MEDIOS_CHICA_ALTO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO,
	"PATH_IMAGEN_NOTAS_AUTORES" => PATH_IMAGEN_NOTAS_AUTORES,
	"PATH_IMAGEN_NOTAS_AUTORES_LOCAL" => PATH_IMAGEN_NOTAS_AUTORES_LOCAL,
	"IMAGEN_NOTAS_AUTORES_CHICA_ANCHO" => IMAGEN_NOTAS_AUTORES_CHICA_ANCHO,
	"IMAGEN_NOTAS_AUTORES_CHICA_ALTO" => IMAGEN_NOTAS_AUTORES_CHICA_ALTO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO" => IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ALTO" => IMAGEN_NOTAS_AUTORES_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloNota);
addBackofficeMenuCarpeta("Autores", "notas_autores", "users");
addBackofficeMenuCarpeta("Medios", "notas_medios.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Nota ", $strTituloNota, "Desde aqu&iacute; podr&aacute; ver el detalle de la nota.");

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