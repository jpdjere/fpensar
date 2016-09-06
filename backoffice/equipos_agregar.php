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
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "equipos_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("equipos_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
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

	/* Inserto Equipo */
	$objEquipos = new clsEquipos();
	if ($objEquipos->insertEquipo($strNombre, $strCargo, $strTexto, $strImagen, $strImagenAnterior, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL, $blnHabilitado))
		redirect("equipos_detalle.php?codEquipo=" . $objEquipos->intEquipo);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
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

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strEquipoNombre" => "Nueva Equipo",
		"strNombre" => "",
		"strCargo" => "",
		"strTexto" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strImagenAnterior" => IMAGEN_NO_DISPONIBLE,
		"strUsuarioTwitter" => "",
		"strFacebookURL" => "",
		"strTwitterURL" => "",
		"blnGrupo1" => "checked",
		"blnGrupo2" => "",
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objEquipos = new clsEquipos();
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
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nueva Equipo", false, "Desde aqu&iacute; podr&aacute; agregar una nueva equipo al sitio");

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
$objTemplate->p("out", false);

?>