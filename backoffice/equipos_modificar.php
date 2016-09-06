<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "equipos.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 5;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si el auto a mostrar existe */
$objEquipos = new clsEquipos();
$intEquipo = (isset($_GET["codEquipo"])) ? $_GET["codEquipo"] : $_POST["codEquipo"];
if (!$intEquipo || !$objEquipos->getEquipos($intEquipo, false, true))
	redirect("equipos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "equipos_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("equipos_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intEquipo = (isset($_POST["codEquipo"])) ? intval($_POST["codEquipo"]) : "";
	$strNombre = (isset($_POST["strNombre"])) ? stripSlashes(trim($_POST["strNombre"])) : "";
	$strCargo = (isset($_POST["strCargo"])) ? stripSlashes(trim($_POST["strCargo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$intGrupo = (isset($_POST["intGrupo"])) ? intval($_POST["intGrupo"]) : 0;
	$strUsuarioTwitter = (isset($_POST["strUsuarioTwitter"])) ? stripSlashes(trim($_POST["strUsuarioTwitter"])) : "";
	$strFacebookURL = (isset($_POST["strFacebookURL"])) ? trim($_POST["strFacebookURL"]) : "";
	$strTwitterURL = (isset($_POST["strTwitterURL"])) ? trim($_POST["strTwitterURL"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$strNombreEquipo = $strNombre;

	/* Inserto Equipo */
	$objEquipos = new clsEquipos();
	if ($objEquipos->updateEquipo($intEquipo, $strNombre, $strCargo, $strTexto, $strImagen, $strImagenAnterior, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL, $blnHabilitado))
		redirect("equipos_detalle.php?codEquipo=" . $intEquipo);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codEquipo" => $intEquipo,
			"strEquipoNombre" => HTMLEntitiesFixed(capitalize($strNombre)),
			"strNombre" => HTMLEntitiesFixed(capitalizeFirst($strNombre)),
			"strCargo" => HTMLEntitiesFixed(capitalizeFirst($strCargo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strImagen" => $objEquipos->strImagen,
			"strUsuarioTwitter" => HTMLEntitiesFixed($strUsuarioTwitter),
			"strFacebookURL" => HTMLEntitiesFixed($strFacebookURL),
			"strTwitterURL" => HTMLEntitiesFixed($strTwitterURL),
			"blnGrupo1" => ($intGrupo == 1) ? "checked" : "",
			"blnGrupo2" => ($intGrupo == 2) ? "checked" : "",
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorNombre" => $objEquipos->errorNombre,
			"errorCargo" => $objEquipos->errorCargo,
			"errorTexto" => $objEquipos->errorTexto,
			"errorImagen" => $objEquipos->errorImagen,
			"errorGrupo" => $objEquipos->errorGrupo,
			"errorUsuarioTwitter" => $objEquipos->errorUsuarioTwitter,
			"errorFacebookURL" => $objEquipos->errorFacebookURL,
			"errorTwitterURL" => $objEquipos->errorTwitterURL
		));

	}
}else{

	/* Levanto los datos de la Equipo */
	$objEquipos->getEquiposRow();
	$objTemplate->set_var(array(
		"strEquipoNombre" => HTMLEntitiesFixed(capitalize($objEquipos->strNombre)),
		"codEquipo" => $objEquipos->intEquipo,
		"strNombre" => HTMLEntitiesFixed($objEquipos->strNombre),
		"strCargo" => HTMLEntitiesFixed($objEquipos->strCargo),
		"strTexto" => showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto), true),
		"strImagen" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
		"strUsuarioTwitter" => HTMLEntitiesFixed($objEquipos->strUsuarioTwitter),
		"strFacebookURL" => HTMLEntitiesFixed($objEquipos->strFacebookURL),
		"strTwitterURL" => HTMLEntitiesFixed($objEquipos->strTwitterURL),
		"blnGrupo1" => ($objEquipos->intGrupo == 1) ? "checked" : "",
		"blnGrupo2" => ($objEquipos->intGrupo == 2) ? "checked" : "",
		"blnHabilitado" => ($objEquipos->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objEquipos->blnHabilitado) ? "" : "checked"
	));

	$strNombreEquipo = $objEquipos->strNombre;
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_EQUIPOS" => PATH_IMAGEN_EQUIPOS,
	"PATH_IMAGEN_EQUIPOS_LOCAL" => PATH_IMAGEN_EQUIPOS_LOCAL,
	"IMAGEN_EQUIPOS_CHICA_ANCHO" => IMAGEN_EQUIPOS_CHICA_ANCHO,
	"IMAGEN_EQUIPOS_CHICA_ALTO" => IMAGEN_EQUIPOS_CHICA_ALTO,
	"IMAGEN_EQUIPOS_GRANDE_ANCHO" => IMAGEN_EQUIPOS_GRANDE_ANCHO,
	"IMAGEN_EQUIPOS_GRANDE_ALTO" => IMAGEN_EQUIPOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strNombreEquipo);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar Equipo ", $strNombreEquipo, "Desde aqu&iacute; podr&aacute; modificar una equipo.");

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