<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";

/* Chequeo si el usuario tiene permisos para ver la seccion */
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
$blnPermisoUsuarioActual = true;
include_once("include_permisos.php");

$strUsuarioNombre = (isset($_GET["codUsuario"])) ? $_GET["codUsuario"] : ((isset($_POST["strUsuario"])) ? $_POST["strUsuario"] : "");
/* Me fijo si el usuario a mostrar existe */
if (!$strUsuarioNombre || !$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre))
	redirect("backoffice_usuarios.php");

/* Me fijo si el usuario tiene permisos para ver esta secion, o no tiene permisos, pero esta viendo su usuario */
$blnUserLoggedNoAccess = false;
if (!$blnPermisoLectura){
	/* Me fijo si quere ver su usuario */
	if ($strUsuarioLogueadoBackoffice != $strUsuarioNombre){
		redirect("restricted.php");
	}else{
		$blnUserLoggedNoAccess = true;
	}
}

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_usuarios_modificar.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("backoffice_usuarios_modificar.php") && $_POST){
	/* Levanto los usuarios del formulario */
	$strUsuarioNombre = (isset($_POST["strUsuario"])) ? trim($_POST["strUsuario"]) : "";
	$intPerfil = (isset($_POST["intPerfil"])) ? $_POST["intPerfil"] : "";
	$strDescripcion = (isset($_POST["strDescripcion"])) ? trim($_POST["strDescripcion"]) : "";
	$strNombre = (isset($_POST["strNombre"])) ? trim($_POST["strNombre"]) : "";
	$strApellido = (isset($_POST["strApellido"])) ? trim($_POST["strApellido"]) : "";
	$strEmail = (isset($_POST["strEmail"])) ? trim($_POST["strEmail"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$objCheck = new clsChecker();

	if ($objBackOfficeUsuarios->updateUsuario($strUsuarioNombre, $intPerfil, $strNombre, $strApellido, $strDescripcion, $strEmail, $blnHabilitado))
		redirect("backoffice_usuarios_detalle.php?codUsuario=" . $strUsuarioNombre);
	else{
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
			"errorPerfil" => $objBackOfficeUsuarios->errorPerfil,
			"errorDescripcion" => $objBackOfficeUsuarios->errorDescripcion,
			"errorNombre" => $objBackOfficeUsuarios->errorNombre,
			"errorApellido" => $objBackOfficeUsuarios->errorApellido,
			"errorEmail" => $objBackOfficeUsuarios->errorEmail
		));
	}

}else{

	/* Levanto los datos del Usuario */
	$strUsuarioNombre = $_GET["codUsuario"];
	if (!$strUsuarioNombre || !$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre))
		redirect("backoffice_usuarios.php");

	$objBackOfficeUsuarios->getUsuariosRow();

	/* Seteo el Perfil para el Combo */
	$intPerfil = $objBackOfficeUsuarios->intPerfil;

	/* Seteo todos los datos de Usuario */
	$objTemplate->set_var(array(
		"strUsuario" => $objBackOfficeUsuarios->strUsuario,
		"strUsuarioTitulo" => capitalizeFirst($objBackOfficeUsuarios->strUsuario),
		"intPerfil" => $objBackOfficeUsuarios->intPerfil,
		"strDescripcion" => $objBackOfficeUsuarios->strDescripcion,
		"strNombre" => $objBackOfficeUsuarios->strNombre,
		"strApellido" => $objBackOfficeUsuarios->strApellido,
		"strEmail" => $objBackOfficeUsuarios->strEmail,
		"blnHabilitado" => ($objBackOfficeUsuarios->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objBackOfficeUsuarios->blnHabilitado) ? "" : "checked"
	));

	$objTemplate->set_var(array(
		"errorUsuario" => "",
		"errorPerfil" => "",
		"errorDescripcion" => "",
		"errorNombre" => "",
		"errorApellido" => "",
		"errorEmail" => ""
	));

	$intPerfil = $objBackOfficeUsuarios->intPerfil;
}

/* Seteo las opciones disponibles de modificacion */
/* Lleno el combo de Perfiles */
$objBackOfficePerfiles = new clsBackOfficePerfiles();
$objBackOfficePerfiles->getPerfiles((($blnUserLoggedNoAccess) ? $intPerfil : false), false, true);

if ($objBackOfficePerfiles->intTotal){
	$objTemplate->set_block("PAGINA", "PERFILES", "perfiles");
	$objTemplate->set_block("PERFILES", "PERFIL", "perfil");

	for ($i = 0; $i < $objBackOfficePerfiles->intTotal; $i++){
		$objBackOfficePerfiles->getPerfilesRow($i);
		$objTemplate->set_var("intPerfil", $objBackOfficePerfiles->intPerfil);
		$objTemplate->set_var("strPerfil", HTMLEntitiesFixed(capitalizeFirst($objBackOfficePerfiles->strPerfil)));
		$objTemplate->set_var("strSelected", ($objBackOfficePerfiles->intPerfil == $intPerfil) ? "selected" : "");

		// Si no es administrador
		$objTemplate->parse("perfil", "PERFIL", true);
	}
	$objTemplate->parse("perfiles", "PERFILES", true);
}else
	$objTemplate->set_var("PERFILES", "No existen perfiles disponibles");

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Editando " . capitalizeFirst($strUsuarioNombre), "#");
addBackofficeMenuCarpeta("PERFILES", "backoffice_perfiles.php", "users");
setBackofficeMenu();
setBackofficeEncabezado("Modificar Usuario ",  capitalizeFirst($strUsuarioNombre), "Desde aqu&iacute; Ud. puede editar los datos del usuario actual.");

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