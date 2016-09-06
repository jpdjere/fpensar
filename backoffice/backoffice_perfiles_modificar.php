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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_perfiles_modificar.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "SECCIONES", "secciones");
$objTemplate->set_block("PAGINA", "SECCIONES_SOLO_LECTURA", "secciones_solo_lectura");
$objTemplate->set_block("PAGINA", "SECCIONES_ACCESO_TOTAL", "secciones_acceso_total");

if (checkReferer("backoffice_perfiles_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intPerfil = (isset($_POST["codPerfil"])) ? intval($_POST["codPerfil"]) : 0;
	$strPerfil = (isset($_POST["strPerfil"])) ? trim($_POST["strPerfil"]) : "";
	$strPerfilAnterior = (isset($_POST["strPerfilAnterior"])) ? trim($_POST["strPerfilAnterior"]) : "";
	$strDescripcion = (isset($_POST["strDescripcion"])) ? trim($_POST["strDescripcion"]) : "";

	/* Genero los combos de Secciones */
	$arrSecciones = array();
	$arrSeccionesAcceso = array();
	for ($i = 0; $i < $objBackOfficeSecciones->intTotal; $i++){
		$objBackOfficeSecciones->getSeccionesRow($i);
		$arrSecciones[$i] = array($objBackOfficeSecciones->intSeccion, $objBackOfficeSecciones->strSeccion);
		$intSeccionValue = (isset($_POST["chkSeccion" . $objBackOfficeSecciones->intSeccion . "Value"])) ? $_POST["chkSeccion" . $objBackOfficeSecciones->intSeccion . "Value"] : 0;
		$arrSeccionesAcceso[$i] = array($objBackOfficeSecciones->intSeccion, $intSeccionValue);
	}

	if ($objBackOfficePerfiles->updatePerfil($intPerfil, $strPerfil, $strPerfilAnterior, $strDescripcion, $arrSeccionesAcceso)){
		redirect("backoffice_perfiles_detalle.php?codPerfil=" . $intPerfil);
	}else{
		$objTemplate->set_var(array(
			"intPerfil" => $intPerfil,
			"strPerfil" => HTMLEntitiesFixed($strPerfil),
			"strPerfilAnterior" => ($objBackOfficePerfiles->errorPerfil) ? $strPerfilAnterior : $strPerfil,
			"strPerfilTitulo" => ($objBackOfficePerfiles->errorPerfil) ? $strPerfilAnterior : $strPerfil,
			"strDescripcion" => HTMLEntitiesFixed($strDescripcion)
		));

		$objTemplate->set_var(array(
			"errorPerfil" => $objBackOfficePerfiles->errorPerfil,
			"errorDescripcion" => $objBackOfficePerfiles->errorDescripcion
		));
	}

}else{

	/* Me fijo si el Perfil a mostrar existe */
	$intPerfil = (isset($_GET["codPerfil"])) ? intval($_GET["codPerfil"]) : 0;
	if (!$intPerfil || !$objBackOfficePerfiles->getPerfiles($intPerfil))
		redirect("backoffice_perfiles.php");

	/* Traigo el Usuario del backoffice */
	$objBackOfficePerfiles->getPerfilesRow();

	$objTemplate->set_var(array(
		"intPerfil" => $objBackOfficePerfiles->intPerfil,
		"strPerfil" => HTMLEntitiesFixed(capitalize($objBackOfficePerfiles->strPerfil)),
		"strPerfilAnterior" => capitalize($objBackOfficePerfiles->strPerfil),
		"strPerfilTitulo" => capitalize($objBackOfficePerfiles->strPerfil),
		"strDescripcion" => HTMLEntitiesFixed(capitalize($objBackOfficePerfiles->strDescripcion))
	));

	$strPerfil = HTMLEntitiesFixed(capitalize($objBackOfficePerfiles->strPerfil));
	$objTemplate->set_var(array(
		"errorNombre" => "",
		"errorDescripcion" => "",
		"errorSecciones" => ""
	));

	/* Escribo los arrays para los combos */
	$objBackOfficePerfiles->getSecciones($intPerfil);

	/* Genero los combos de Secciones */
	$arrSecciones = array();
	$arrSeccionesAcceso = array();
	for ($i = 0; $i < $objBackOfficeSecciones->intTotal; $i++){
		$objBackOfficeSecciones->getSeccionesRow($i);
		$arrSecciones[$i] = array($objBackOfficeSecciones->intSeccion, $objBackOfficeSecciones->strSeccion);
		$arrSeccionesAcceso[$i] = array($objBackOfficeSecciones->intSeccion, $objBackOfficeSecciones->intAcceso);
	}
}

/* Escribo el combos de Secciones */
$strSeccionesIds = "";
for ($i = 0; $i < sizeOf($arrSecciones); $i++){
	// Posteo Secciones con permisos
	$strNumeroBinario = substr("0000" . decbin($arrSeccionesAcceso[$i][1]), -5);
	$objTemplate->set_var(array(
		"intSeccion" => $arrSecciones[$i][0],
		"strSeccion" => capitalizeAll($arrSecciones[$i][1]),
		"checkSeccion1" => ($strNumeroBinario[4] == 1) ? "checked" : "",
		"checkSeccion2" => ($strNumeroBinario[3] == 1) ? "checked" : "",
		"checkSeccion3" => ($strNumeroBinario[2] == 1) ? "checked" : "",
		"checkSeccion4" => ($strNumeroBinario[1] == 1) ? "checked" : "",
		"checkSeccion5" => ($strNumeroBinario[0] == 1) ? "checked" : ""
	));
	$strSeccionesIds .= (($strSeccionesIds) ? ", " : "") . $arrSecciones[$i][0];

	$objTemplate->parse("secciones", "SECCIONES", true);
}
$objTemplate->set_var("strSeccionesTotal", $strSeccionesIds);

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Perfiles", "backoffice_perfiles.php");
addBackofficeMenuItem($strPerfil);
setBackofficeMenu();
setBackofficeEncabezado("Modificar Perfil ", $strPerfil, "Desde aqu&iacute; editar agregar un perfil de usuario existente.");

/* Seteo variables Comunes */
$objTemplate->set_var(array(
	"TITLE" => ":: $strNombreEmpresa : Backoffice : Usuarios ::",
	"FECHA" => getFecha(),
	"nombreEmpresa" => $strNombreEmpresa
));

/* Parseo Templates */
$objTemplate->parseArray(array(
	"FOOTER" => "FOOTER",
	"PAGINA" => "PAGINA",
	"ENCABEZADO" => "ENCABEZADO",
	"MENU" => "MENU",
	"HEADER" => "HEADER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out");

?>