<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";

/* Chequeo si el usuario tiene permisos para ver la seccion */
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

if (!$blnPermisoLectura)
	redirect("restricted.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_perfiles.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "PERFILES", "perfiles");
$objTemplate->set_block("PERFILES", "MODIFICAR_PERFILES_UPDATE", "modificar_perfiles_update");
$objTemplate->set_block("PERFILES", "MODIFICAR_PERFILES_DELETE", "modificar_perfiles_delete");
$objTemplate->set_block("PAGINA", "AGREGAR_PERFILES", "agregar_perfiles");

/* Traigo un Listado de todos los Permisos del backoffice */
$objBackOfficePerfiles->getPerfiles(false, false, true);

/* Levanto la pagina a mostrar */
$intPagina = isset($_GET["intPagina"]) ? $_GET["intPagina"] : "";
if (!$intPagina)
	$intPagina = 1;

$intCantidadRegistros = $objBackOfficePerfiles->intTotal;
for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	$objBackOfficePerfiles->getPerfilesRow($i);
	$objTemplate->set_var(array(
		"intPerfil" => $objBackOfficePerfiles->intPerfil,
		"codPerfil" => $objBackOfficePerfiles->intPerfil,
		"strPerfil" => HTMLEntitiesFixed(capitalize($objBackOfficePerfiles->strPerfil)),
		"strDescripcion" => HTMLEntitiesFixed($objBackOfficePerfiles->strDescripcion),
		"intUsuarios" => $objBackOfficePerfiles->intUsuarios,
		"strUsuarios" => ($objBackOfficePerfiles->intUsuarios <= 0) ? "No posee usuarios asignados" : (($objBackOfficePerfiles->intUsuarios > 1) ? $objBackOfficePerfiles->intUsuarios . " usuarios asignados" : "1 usuario asignado")
	));

	if ($blnPermisoModificacion){
		$objTemplate->parse("modificar_perfiles_update", "MODIFICAR_PERFILES_UPDATE");
	}else{
		$objTemplate->set_var("modificar_perfiles_update", "");
	}

	if ($blnPermisoBaja){
		$objTemplate->parse("modificar_perfiles_delete", "MODIFICAR_PERFILES_DELETE");
	}else{
		$objTemplate->set_var("modificar_perfiles_delete", "");
	}

	$objTemplate->parse("perfiles", "PERFILES", true);
}

if ($blnPermisoAlta)
	$objTemplate->parse("agregar_perfiles", "AGREGAR_PERFILES");
else
	$objTemplate->set_var("agregar_perfiles", "");

/* Incluyo Paginador */
$strPage = "perfiles";
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Perfiles", "backoffice_perfiles.php", true);
setBackofficeMenu();
setBackofficeEncabezado("Listado de Perfiles ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; ver y editar todos los perfiles del backoffice.");

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