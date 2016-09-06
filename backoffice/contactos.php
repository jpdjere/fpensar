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

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "contactos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "CONTACTOS", "contactos");
$objTemplate->set_block("PAGINA", "CONTACTOS_VACIO", "contactos_vacio");

/* Levanto el orden  y direccion */
$intOrden = isset($_GET["o"]) ? intval($_GET["o"]) : 0;
if (!$intOrden || $intOrden < 1 || $intOrden > 3)
	$intOrden = 3;
$intDireccion = isset($_GET["d"]) ? intval($_GET["d"]) : 0;
if (!$intDireccion || ($intDireccion != 1 && $intDireccion != 2))
	$intDireccion = (($intOrden == 3) ? 2 : 1);

/* Traigo un Listado de todos los Contactos del backoffice */
$objContacto = new clsContactos();
$objContacto->getContactos(false, $intOrden, $intDireccion);

/* Levanto la pagina a mostrar */
$intPagina = isset($_GET["intPagina"]) ? $_GET["intPagina"] : "";
if (!$intPagina)
	$intPagina = 1;

$intCantidadRegistros = 0;
if ($objContacto->intTotal){
	$intCantidadRegistros = $objContacto->intTotal;
	for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
		$objContacto->getContactosRow($i);
		$objTemplate->set_var(array(
			"intContacto" => HTMLEntitiesFixed($objContacto->intContacto),
			"codContacto" => HTMLEntitiesFixed($objContacto->intContacto),
			"strNombre" => HTMLEntitiesFixed(capitalizeFirst($objContacto->strNombre)),
			"strApellido" => HTMLEntitiesFixed(capitalizeFirst($objContacto->strApellido)),
			"strEmail" => HTMLEntitiesFixed($objContacto->strEmail),
			"strLocalidad" => HTMLEntitiesFixed($objContacto->strLocalidad),
			"strTelefono" => HTMLEntitiesFixed($objContacto->strTelefono),
			"strAsunto" => HTMLEntitiesFixed($objContacto->strAsunto),
			"strMensaje" => HTMLEntitiesFixed($objContacto->strMensaje),
			"strFecha" => $objContacto->strFechaListado
		));

		$objTemplate->parse("contactos", "CONTACTOS", true);
	}
	$objTemplate->set_var("contactos_vacio", "");

}else{
	$objTemplate->set_var("contactos", "");
	$objTemplate->parse("contactos_vacio", "CONTACTOS_VACIO");
}

// Parseo Orden
$objTemplate->set_var("strOrdenParameter", "?o=");

// Parseo Direccion
for ($i = 1; $i <= 3; $i++){
	$objTemplate->set_var("strDireccionOrden" . $i, ($intOrden == $i) ? (($intDireccion == 1) ? 2 : 1) : 1);
}

/* Incluyo Paginador */
$strPage = "contactos";
$strParameters = "o=" . $intOrden . "&d=" . $intDireccion;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var("strExportParameters", "?" . $strParameters);

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
setBackofficeMenu();
setBackofficeEncabezado("Listado de Contactos ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; Ud. puede ver todos los contactos del sitio.");

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