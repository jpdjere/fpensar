<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "eventos.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 10;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si el auto a mostrar existe */
$objEventos = new clsEventos();
$intEvento = (isset($_GET["codEvento"])) ? $_GET["codEvento"] : $_POST["codEvento"];
if (!$intEvento || !$objEventos->getEventos($intEvento, true))
	redirect("eventos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "eventos_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("eventos_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intEvento = (isset($_POST["codEvento"])) ? intval($_POST["codEvento"]) : "";
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$strArchivo = (isset($_FILES["strArchivo"]["name"])) ? $_FILES["strArchivo"] : "";
	$strArchivoAnterior = (isset($_POST["strArchivoAnterior"])) ? trim($_POST["strArchivoAnterior"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	$strTituloEvento = $strTitulo;

	/* Inserto Evento */
	$objEventos = new clsEventos();
	if ($objEventos->updateEvento($intEvento, $strTitulo, $strTexto, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado))
		redirect("eventos_detalle.php?codEvento=" . $intEvento);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codEvento" => $intEvento,
			"strEventoTitulo" => HTMLEntitiesFixed(capitalize($strTitulo)),
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strImagen" => $objEventos->strImagen,
			"strArchivo" => $objEventos->strArchivo,
			"strFecha" => $strFecha,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTitulo" => $objEventos->errorTitulo,
			"errorTexto" => $objEventos->errorTexto,
			"errorImagen" => $objEventos->errorImagen,
			"errorArchivo" => $objEventos->errorArchivo,
			"errorFecha" => $objEventos->errorFecha
		));

	}
}else{

	/* Levanto los datos del Evento */
	$objEventos->getEventosRow();
	$objTemplate->set_var(array(
		"strEventoTitulo" => HTMLEntitiesFixed(capitalize($objEventos->strTitulo)),
		"codEvento" => $objEventos->intEvento,
		"strTitulo" => HTMLEntitiesFixed($objEventos->strTitulo),
		"strTexto" => showTextBreaks(HTMLEntitiesFixed($objEventos->strTexto), true),
		"strImagen" => $objEventos->strImagen,
		"strArchivo" => $objEventos->strArchivo,
		"strFecha" => $objEventos->strFecha,
		"blnHabilitado" => ($objEventos->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objEventos->blnHabilitado) ? "" : "checked"
	));

	$strTituloEvento = $objEventos->strTitulo;
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_EVENTOS" => PATH_IMAGEN_EVENTOS,
	"PATH_IMAGEN_EVENTOS_LOCAL" => PATH_IMAGEN_EVENTOS_LOCAL,
	"IMAGEN_EVENTOS_CHICA_ANCHO" => IMAGEN_EVENTOS_CHICA_ANCHO,
	"IMAGEN_EVENTOS_CHICA_ALTO" => IMAGEN_EVENTOS_CHICA_ALTO,
	"IMAGEN_EVENTOS_GRANDE_ANCHO" => IMAGEN_EVENTOS_GRANDE_ANCHO,
	"IMAGEN_EVENTOS_GRANDE_ALTO" => IMAGEN_EVENTOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strTituloEvento);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar Evento ", $strTituloEvento, "Desde aqu&iacute; podr&aacute; modificar un evento.");

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