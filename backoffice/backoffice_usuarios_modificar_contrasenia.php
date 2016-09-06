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
	"PAGINA" => TEMPLATES_DIR . "backoffice_usuarios_modificar_contrasenia.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("backoffice_usuarios_modificar_contrasenia.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strUsuarioNombre = (isset($_POST["strUsuario"])) ? $_POST["strUsuario"] : "";
	$strContrasenia = (isset($_POST["strContrasenia"])) ? $_POST["strContrasenia"] : "";
	$strContraseniaConfirmacion = (isset($_POST["strContraseniaConfirmacion"])) ? $_POST["strContraseniaConfirmacion"] : "";

	$objCheck = new clsChecker();

	if ($objBackOfficeUsuarios->updateContrasenia($strUsuarioNombre, $strContrasenia, $strContraseniaConfirmacion)){
		redirect("backoffice_usuarios_detalle.php?codUsuario=$strUsuarioNombre");
	}else{
		$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre);
		$objBackOfficeUsuarios->getUsuariosRow();
		$objTemplate->set_var(array(
			"strUsuario" => $strUsuarioNombre,
			"strUsuarioTitulo" => capitalize($strUsuarioNombre),
			"strPerfil" => capitalize($objBackOfficeUsuarios->strPerfil),
			"strNombre" => $objBackOfficeUsuarios->strNombre,
			"strApellido" => $objBackOfficeUsuarios->strApellido
		));

		$objTemplate->set_var(array(
			"errorContrasenia" => $objBackOfficeUsuarios->errorContrasenia,
			"errorContraseniaConfirmacion" => $objBackOfficeUsuarios->errorContraseniaConfirmacion
		));
	}

}else{

	/* Levanto los datos del Usuario */
	$strUsuarioNombre = $_GET["codUsuario"];
	if (!$strUsuarioNombre || !$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre))
		redirect("backoffice_usuarios.php");

	$objBackOfficeUsuarios->getUsuariosRow();

	/* Seteo todos los datos de Usuario */
	$objTemplate->set_var(array(
		"strUsuario" => capitalize($objBackOfficeUsuarios->strUsuario),
		"strUsuarioTitulo" => capitalize($objBackOfficeUsuarios->strUsuario),
		"strPerfil" => capitalize($objBackOfficeUsuarios->strPerfil),
		"strNombre" => $objBackOfficeUsuarios->strNombre,
		"strApellido" => $objBackOfficeUsuarios->strApellido
	));

	$objTemplate->set_var(array(
		"errorContrasenia" => "",
		"errorContraseniaConfirmacion" => ""
	));
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Editando " . capitalizeFirst($strUsuarioNombre), "#");
addBackofficeMenuCarpeta("PERFILES", "backoffice_perfiles.php", "users");
setBackofficeMenu();
setBackofficeEncabezado("Modificar contrase&ntilde;a de Usuario ", capitalize($strUsuarioNombre), "Desde aqu&iacute; Ud. puede cambiar la contrase&ntilde;a de acceso del usuario actual.");

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