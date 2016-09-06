<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "coyuntura.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 8;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si el auto a mostrar existe */
$objCoyuntura = new clsCoyuntura();
$intCoyuntura = (isset($_GET["codCoyuntura"])) ? $_GET["codCoyuntura"] : $_POST["codCoyuntura"];
if (!$intCoyuntura || !$objCoyuntura->getCoyuntura($intCoyuntura, true))
	redirect("coyuntura.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "coyuntura_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("coyuntura_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intCoyuntura = (isset($_POST["codCoyuntura"])) ? intval($_POST["codCoyuntura"]) : "";
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strSeccionInternacional = (isset($_POST["strSeccionInternacional"])) ? stripSlashes(trim($_POST["strSeccionInternacional"])) : "";
	$strSeccionEconomia = (isset($_POST["strSeccionEconomia"])) ? stripSlashes(trim($_POST["strSeccionEconomia"])) : "";
	$strSeccionPolitica = (isset($_POST["strSeccionPolitica"])) ? stripSlashes(trim($_POST["strSeccionPolitica"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$strArchivo = (isset($_FILES["strArchivo"]["name"])) ? $_FILES["strArchivo"] : "";
	$strArchivoAnterior = (isset($_POST["strArchivoAnterior"])) ? trim($_POST["strArchivoAnterior"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$strTituloCoyuntura = $strTitulo;

	/* Inserto Coyuntura */
	$objCoyuntura = new clsCoyuntura();
	if ($objCoyuntura->updateCoyuntura($intCoyuntura, $strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado)){
		redirect("coyuntura_detalle.php?codCoyuntura=" . $intCoyuntura);
	}else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codCoyuntura" => $intCoyuntura,
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strSeccionInternacional" => HTMLEntitiesFixed(capitalizeFirst($strSeccionInternacional)),
			"strSeccionEconomia" => HTMLEntitiesFixed(capitalizeFirst($strSeccionEconomia)),
			"strSeccionPolitica" => HTMLEntitiesFixed(capitalizeFirst($strSeccionPolitica)),
			"strImagen" => $objCoyuntura->strImagen,
			"strArchivo" => $objCoyuntura->strArchivo,
			"strFecha" => $strFecha,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTitulo" => $objCoyuntura->errorTitulo,
			"errorSeccionInternacional" => $objCoyuntura->errorSeccionInternacional,
			"errorSeccionEconomia" => $objCoyuntura->errorSeccionEconomia,
			"errorSeccionPolitica" => $objCoyuntura->errorSeccionPolitica,
			"errorImagen" => $objCoyuntura->errorImagen,
			"errorArchivo" => $objCoyuntura->errorArchivo,
			"errorFecha" => $objCoyuntura->errorFecha
		));

	}
}else{

	/* Levanto los datos de la Coyuntura */
	$objCoyuntura->getCoyunturaRow();
	$objTemplate->set_var(array(
		"codCoyuntura" => $objCoyuntura->intCoyuntura,
		"strTitulo" => HTMLEntitiesFixed($objCoyuntura->strTitulo),
		"strSeccionInternacional" => HTMLEntitiesFixed($objCoyuntura->strSeccionInternacional),
		"strSeccionEconomia" => HTMLEntitiesFixed($objCoyuntura->strSeccionEconomia),
		"strSeccionPolitica" => HTMLEntitiesFixed($objCoyuntura->strSeccionPolitica),
		"strImagen" => $objCoyuntura->strImagen,
		"strArchivo" => $objCoyuntura->strArchivo,
		"strFecha" => $objCoyuntura->strFecha,
		"blnHabilitado" => ($objCoyuntura->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objCoyuntura->blnHabilitado) ? "" : "checked"
	));

	$strTituloCoyuntura = $objCoyuntura->strTitulo;
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_COYUNTURA" => PATH_IMAGEN_COYUNTURA,
	"PATH_IMAGEN_COYUNTURA_LOCAL" => PATH_IMAGEN_COYUNTURA_LOCAL,
	"IMAGEN_COYUNTURA_CHICA_ANCHO" => IMAGEN_COYUNTURA_CHICA_ANCHO,
	"IMAGEN_COYUNTURA_CHICA_ALTO" => IMAGEN_COYUNTURA_CHICA_ALTO,
	"IMAGEN_COYUNTURA_GRANDE_ANCHO" => IMAGEN_COYUNTURA_GRANDE_ANCHO,
	"IMAGEN_COYUNTURA_GRANDE_ALTO" => IMAGEN_COYUNTURA_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloCoyuntura);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar edici&oacute;n de Coyuntura ", $strTituloCoyuntura, "Desde aqu&iacute; podr&aacute; modificar una edici&oacute;n de Coyuntura.");

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