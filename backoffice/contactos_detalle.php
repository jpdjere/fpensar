<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "contactos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 4;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si el contacto a mostrar existe */
$intContacto = (isset($_GET["codContacto"])) ? intval($_GET["codContacto"]) : 0;
if (!$intContacto)
	redirect("contactos.php");

$objContacto = new clsContactos();
if (!$objContacto->getContactos($intContacto))
	redirect("contactos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "contactos_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Traigo el Contacto del backoffice */
$objContacto->getContactosRow();

/* Seteo todos los datos de Contacto */
$objTemplate->set_var(array(
	"intContacto" => HTMLEntitiesFixed($objContacto->intContacto),
	"codContacto" => HTMLEntitiesFixed($objContacto->intContacto),
	"strNombre" => HTMLEntitiesFixed(capitalizeFirst($objContacto->strNombre)),
	"strApellido" => HTMLEntitiesFixed(capitalizeFirst($objContacto->strApellido)),
	"strEmail" => HTMLEntitiesFixed($objContacto->strEmail),
	"strLocalidad" => HTMLEntitiesFixed($objContacto->strLocalidad),
	"strTelefono" => HTMLEntitiesFixed($objContacto->strTelefono),
	"strAsunto" => HTMLEntitiesFixed($objContacto->strAsunto),
	"strMensaje" => showTextBreaks(HTMLEntitiesFixed($objContacto->strMensaje)),
	"strFecha" => $objContacto->strFecha,
));
$strNombreContacto = HTMLEntitiesFixed(capitalizeFirst($objContacto->strNombre) . " " . capitalizeFirst($objContacto->strApellido));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strNombreContacto);
setBackofficeMenu();
setBackofficeEncabezado("Detalle de Contacto ", $strNombreContacto, "Desde aqu&iacute; Ud. puede ver el detalle del contacto actual.");

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