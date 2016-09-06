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
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "eventos_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("eventos_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$strArchivo = (isset($_FILES["strArchivo"]["name"])) ? $_FILES["strArchivo"] : "";
	$strArchivoAnterior = (isset($_POST["strArchivoAnterior"])) ? trim($_POST["strArchivoAnterior"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Evento */
	$objEventos = new clsEventos();
	if ($objEventos->insertEvento($strTitulo, $strTexto, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado))
		redirect("eventos_detalle.php?codEvento=" . $objEventos->intEvento);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
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

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strEventoTitulo" => "Nueva Evento",
		"strTitulo" => "",
		"strTexto" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strArchivo" => "",
		"strFecha" => date("d") . "/" . date("m") . "/" . date("Y"),
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objEventos = new clsEventos();
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
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nuevo Evento", false, "Desde aqu&iacute; podr&aacute; agregar un nuevo evento al sitio");

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