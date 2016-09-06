<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";

/* Inicio Session */
$strUsuarioLogueadoBackoffice = (isset($_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"])) ? $_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"] : "";
if ($strUsuarioLogueadoBackoffice){
	redirect("backoffice.php");
}

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura_simple.html",
	"PAGINA" => TEMPLATES_DIR . "login.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if ($_POST && (checkReferer("index.php") || checkReferer(""))){
	$strUsuario = (isset($_POST["strUsuario"])) ? $_POST["strUsuario"] : "";
	$strContrasenia = (isset($_POST["strContrasenia"])) ? $_POST["strContrasenia"] : "";

	if ($strUsuario && $strContrasenia){
		/* Instancio Clase Usuario */
		$objQuery = new DB_Sql();

		/* Instancio Clase Usuario */
		$objBackOfficeUsuarios = new clsBackOfficeUsuarios();
		if ($objBackOfficeUsuarios->checkUsuario($strUsuario, $strContrasenia)){

			/* Grabo en session el usuario y redirecciono */
			$strUsuarioBackoffice = $strUsuario;
			$_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"] = $strUsuario;
			header ("Location: backoffice.php");
		}else{
			$objTemplate->set_var("errorUsuario", $objBackOfficeUsuarios->errorUsuarioLogin);
			$objTemplate->set_var("strUsuario", $strUsuario);
		}
	}else if ($strUsuario){
		$objTemplate->set_var("errorUsuario", "Debe ingresar su contrase&ntilde;a");
		$objTemplate->set_var("strUsuario", $strUsuario);
	}else{
		$objTemplate->set_var("errorUsuario", "Debe ingresar su usuario y contrase&ntilde;a");
	}
}else{
	$objTemplate->set_var("strUsuario", "");
	$objTemplate->set_var("strContrasenia", "");
}

// Set Page Class
$objTemplate->set_var("strPageClass", ' class="loginPage"');

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
	"PAGINA" => "PAGINA",
	"FOOTER" => "FOOTER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out");

?>