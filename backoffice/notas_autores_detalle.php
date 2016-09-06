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
$intAutor = (isset($_GET["codAutor"])) ? intval($_GET["codAutor"]) : 0;
if (!$intAutor || !$objNotas->getAutores($intAutor, true))
	redirect("notas_autores.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_autores_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Defino Bloques */
$objTemplate->set_block("PAGINA", "MODIFICAR_NOTAS", "modificar_notas");

$objNotas->getAutoresRow();
$objTemplate->set_var(array(
	"codAutor" => $objNotas->intAutor,
	"strAutor" => HTMLEntitiesFixed($objNotas->strAutor),
	"strImagen" => $objNotas->strImagen,
	"strFechaAlta" => $objNotas->strFechaAlta,
	"strFechaModificacion" => $objNotas->strFechaModificacion,
	"estadoIcono" => ($objNotas->blnHabilitado) ? "" : "_on",
	"estadoAlt" => ($objNotas->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"strEstadoNota" => ($objNotas->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));

$strAutor = $objNotas->strAutor;
$intAutor = $objNotas->intAutor;

if ($blnPermisoModificacion)
	$objTemplate->parse("modificar_notas", "MODIFICAR_NOTAS");

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_AUTORES" => PATH_IMAGEN_NOTAS_AUTORES,
	"PATH_IMAGEN_NOTAS_AUTORES_LOCAL" => PATH_IMAGEN_NOTAS_AUTORES_LOCAL,
	"IMAGEN_NOTAS_AUTORES_CHICA_ANCHO" => IMAGEN_NOTAS_AUTORES_CHICA_ANCHO,
	"IMAGEN_NOTAS_AUTORES_CHICA_ALTO" => IMAGEN_NOTAS_AUTORES_CHICA_ALTO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO" => IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ALTO" => IMAGEN_NOTAS_AUTORES_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "#", "users");
addBackofficeMenuItem($strAutor);
addBackofficeMenuCarpeta("Medios", "notas_medios.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Detalle de Autor ", $strAutor, "Desde aqu&iacute; podr&aacute; ver el detalle del autor del nota.");

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