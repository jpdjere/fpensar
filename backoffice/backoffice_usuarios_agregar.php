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

// Chequeo permisos y perfiles
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_usuarios_agregar.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("backoffice_usuarios_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strUsuarioNombre = (isset($_POST["strUsuario"])) ? trim($_POST["strUsuario"]) : "";
	$strContrasenia = (isset($_POST["strContrasenia"])) ? $_POST["strContrasenia"] : "";
	$strContraseniaConfirmacion = (isset($_POST["strContraseniaConfirmacion"])) ? $_POST["strContraseniaConfirmacion"] : "";
	$intPerfil = (isset($_POST["intPerfil"])) ? intval($_POST["intPerfil"]) : "";
	$strDescripcion = (isset($_POST["strDescripcion"])) ? trim($_POST["strDescripcion"]) : "";
	$strNombre = (isset($_POST["strNombre"])) ? trim($_POST["strNombre"]) : "";
	$strApellido = (isset($_POST["strApellido"])) ? trim($_POST["strApellido"]) : "";
	$strEmail = (isset($_POST["strEmail"])) ? trim($_POST["strEmail"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$objCheck = new clsChecker();

	if ($objBackOfficeUsuarios->insertUsuario($strUsuarioNombre, $strContrasenia, $strContraseniaConfirmacion, $intPerfil, $strNombre, $strApellido, $strDescripcion, $strEmail, $blnHabilitado)){
		redirect("backoffice_usuarios_detalle.php?codUsuario=$strUsuarioNombre");
	}else{
		$objTemplate->set_var(array(
			"strUsuario" => HTMLEntitiesFixed($strUsuarioNombre),
			"strUsuarioTitulo" => HTMLEntitiesFixed(capitalizeFirst($strUsuarioNombre)),
			"intPerfil" => $intPerfil,
			"strDescripcion" => HTMLEntitiesFixed($strDescripcion),
			"strNombre" => HTMLEntitiesFixed($strNombre),
			"strApellido" => HTMLEntitiesFixed($strApellido),
			"strEmail" => HTMLEntitiesFixed($strEmail),
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		$objTemplate->set_var(array(
			"errorUsuario" => $objBackOfficeUsuarios->errorUsuario,
			"errorContrasenia" => $objBackOfficeUsuarios->errorContrasenia,
			"errorContraseniaConfirmacion" => $objBackOfficeUsuarios->errorContraseniaConfirmacion,
			"errorPerfil" => $objBackOfficeUsuarios->errorPerfil,
			"errorDescripcion" => $objBackOfficeUsuarios->errorDescripcion,
			"errorNombre" => $objBackOfficeUsuarios->errorNombre,
			"errorApellido" => $objBackOfficeUsuarios->errorApellido,
			"errorEmail" => $objBackOfficeUsuarios->errorEmail
		));
	}

}else{

	$objTemplate->set_var(array(
		"strUsuario" => "",
		"strUsuarioTitulo" => "",
		"strDescripcion" => "",
		"strNombre" => "",
		"strApellido" => "",
		"strEmail" => "",
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objTemplate->set_var(array(
		"errorUsuario" => "",
		"errorContrasenia" => "",
		"errorContraseniaConfirmacion" => "",
		"errorPerfil" => "",
		"errorDescripcion" => "",
		"errorNombre" => "",
		"errorApellido" => "",
		"errorEmail" => ""
	));

	$intPerfil = 0;
}

/* Seteo las opciones disponibles de modificacion */
/* Lleno el combo de Perfiles */
$objBackOfficePerfiles = new clsBackOfficePerfiles();
$objBackOfficePerfiles->getPerfiles(false, false, true);

if ($objBackOfficePerfiles->intTotal){
	$objTemplate->set_block("PAGINA", "PERFILES", "perfiles");
	$objTemplate->set_block("PERFILES", "PERFIL", "perfil");

	for ($i = 0; $i < $objBackOfficePerfiles->intTotal; $i++){
		$objBackOfficePerfiles->getPerfilesRow($i);
		$objTemplate->set_var("intPerfil", $objBackOfficePerfiles->intPerfil);
		$objTemplate->set_var("strPerfil", HTMLEntitiesFixed(capitalizeFirst($objBackOfficePerfiles->strPerfil)));
		$objTemplate->set_var("strSelected", ($objBackOfficePerfiles->intPerfil == $intPerfil) ? "selected" : "");
		$objTemplate->parse("perfil", "PERFIL", true);
	}
	$objTemplate->parse("perfiles", "PERFILES", true);
}else
	$objTemplate->set_var("PERFILES", "No existen perfiles disponibles");

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar Usuario", "#", "config");
addBackofficeMenuCarpeta("PERFILES", "backoffice_perfiles.php", "users");
setBackofficeMenu();
setBackofficeEncabezado("Agregar Usuario", false, "Desde aqu&iacute; Ud. puede agregar un nuevo usuario.");

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
	"PAGINA" => "PAGINA",
	"ENCABEZADO" => "ENCABEZADO",
	"MENU" => "MENU",
	"HEADER" => "HEADER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out");

?>