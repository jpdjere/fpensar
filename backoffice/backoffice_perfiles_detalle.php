<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

/* Chequeo si el usuario tiene permisos para ver la seccion */
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Me fijo si el Perfil a mostrar existe */
$intPerfil = (isset($_GET["codPerfil"])) ? intval($_GET["codPerfil"]) : 0;
if (!$intPerfil || !$objBackOfficePerfiles->getPerfiles($intPerfil))
	redirect("backoffice_perfiles.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_perfiles_detalle.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "USUARIOS", "usuarios");
$objTemplate->set_block("PAGINA", "USUARIOS_VACIO", "usuarios_vacio");
$objTemplate->set_block("PAGINA", "SECCIONES", "secciones");

/* Traigo el perfil del usuario */
$objBackOfficePerfiles->getPerfilesRow();

$objTemplate->set_var(array(
	"intPerfil" => $objBackOfficePerfiles->intPerfil,
	"strPerfil" => HTMLEntitiesFixed(capitalize($objBackOfficePerfiles->strPerfil)),
	"strDescripcion" => HTMLEntitiesFixed($objBackOfficePerfiles->strDescripcion)
));

if ($objBackOfficePerfiles->intTotal && $objBackOfficePerfiles->strUsuario){
	for ($i = 0; $i < $objBackOfficePerfiles->intTotal; $i++){
		$objBackOfficePerfiles->getPerfilesRow($i);
		$objTemplate->set_var("strUsuario", capitalize($objBackOfficePerfiles->strUsuario));
		$objTemplate->parse("usuarios", "USUARIOS", $i);
	}
	$objTemplate->set_var("intUsuarios" , $i);
	$objTemplate->set_var("usuarios_vacio", "");
}else{
	$objTemplate->set_var("intUsuarios" , "0");
	$objTemplate->parse("usuarios_vacio", "USUARIOS_VACIO");
}

/* Seteo las opciones disponibles de modificacion */
if ($blnPermisoModificacion){
	$objTemplate->set_block("PAGINA", "MODIFICAR_PERFILES", "modificar_perfiles");
	$objTemplate->parse("modificar_perfiles", "MODIFICAR_PERFILES");
}else{
	$objTemplate->set_var("modificar_perfiles", "");
}

/* Escribo los arrays para los combos */
$objBackOfficePerfiles->getSecciones($intPerfil);

for ($i = 0; $i < $objBackOfficePerfiles->intTotal; $i++){
	$objBackOfficePerfiles->getPerfilesRow($i);
	if ($objBackOfficePerfiles->intAcceso){
		$strNumeroBinario = substr("0000" . decbin($objBackOfficePerfiles->intAcceso), -5);
		$objTemplate->set_var(array(
			"strSeccion" => str_replace("_", " ", $objBackOfficePerfiles->strSeccion),
			"strStyleAcceso1" => ($strNumeroBinario[4] == 1) ? "" : "text-decoration: line-through;",
			"strStyleAcceso2" => ($strNumeroBinario[3] == 1) ? "" : "text-decoration: line-through;",
			"strStyleAcceso4" => ($strNumeroBinario[2] == 1) ? "" : "text-decoration: line-through;",
			"strStyleAcceso8" => ($strNumeroBinario[1] == 1) ? "" : "text-decoration: line-through;",
			"strStyleAcceso16" => ($strNumeroBinario[0] == 1) ? "" : "text-decoration: line-through;"
		));

		$objTemplate->parse("secciones", "SECCIONES", true);
	}
}

/* Muestro el mensaje de error si no se pudo borrar el perfil */
if (checkReferer("backoffice_perfiles_detalle.php")){
	$objTemplate->set_block("PAGINA", "ERROR_BORRADO", "error_borrado");
	$objTemplate->parse("error_borrado", "ERROR_BORRADO");
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Perfiles", "backoffice_perfiles.php");
addBackofficeMenuItem(capitalize($objBackOfficePerfiles->strPerfil), 1);
setBackofficeMenu();
setBackofficeEncabezado("Detalle del Perfil ", capitalize($objBackOfficePerfiles->strPerfil), "Desde aqu&iacute; podr&aacute; ver todos los datos del perfil " . capitalize($objBackOfficePerfiles->strPerfil) . ".");

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