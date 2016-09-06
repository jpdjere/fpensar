<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "notas.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 7;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("notas_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intAutor = (isset($_POST["intAutor"])) ? stripSlashes(trim($_POST["intAutor"])) : "";
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strArchivo = (isset($_FILES["strArchivo"]["name"])) ? $_FILES["strArchivo"] : "";
	$strArchivoAnterior = (isset($_POST["strArchivoAnterior"])) ? trim($_POST["strArchivoAnterior"]) : "";
	$intMedio = (isset($_POST["intMedio"])) ? stripSlashes(trim($_POST["intMedio"])) : "";
	$strLinkURL = (isset($_POST["strLinkURL"])) ? stripSlashes(trim($_POST["strLinkURL"])) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Nota */
	$objNotas = new clsNotas();
	if ($objNotas->insertNota($intAutor, $strTitulo, $strTexto, $strArchivo, $strArchivoAnterior, $intMedio, $strLinkURL, $strFecha, $blnHabilitado))
		redirect("notas_detalle.php?codNota=" . $objNotas->intNota);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strArchivo" => $objNotas->strArchivo,
			"strLinkURL" => $strLinkURL,
			"strFecha" => $strFecha,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorAutor" => $objNotas->errorAutor,
			"errorTitulo" => $objNotas->errorTitulo,
			"errorTexto" => $objNotas->errorTexto,
			"errorArchivo" => $objNotas->errorArchivo,
			"errorMedio" => $objNotas->errorMedio,
			"errorLinkURL" => $objNotas->errorLinkURL,
			"errorFecha" => $objNotas->errorFecha
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strNotaTitulo" => "Nuevo Nota",
		"strAutor" => "",
		"strTitulo" => "",
		"strTexto" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strArchivo" => "",
		"strLinkURL" => "",
		"strFecha" => date("d") . "/" . date("m") . "/" . date("Y"),
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objNotas = new clsNotas();
	$intAutor = 0;
	$intMedio = 0;
}

/* Muestro el Combo de Autores */
$objTemplate->set_block("PAGINA", "AUTORES", "autores");
$objAutores = new clsNotas();
$objAutores->getAutores();

/* Coloco Primera Opcion */
$objTemplate->set_var(array(
	"intAutor" => 0,
	"strAutor" => "Seleccione...",
	"strSelected" => ""
));
$objTemplate->parse("autores", "AUTORES");

for ($i = 0; $i < $objAutores->intTotal; $i++){
	$objAutores->getAutoresRow($i);
	$objTemplate->set_var(array(
		"intAutor" => $objAutores->intAutor,
		"strAutor" => $objAutores->strAutor,
		"strSelected" => ($objAutores->intAutor == $intAutor) ? "selected" : ""
	));
	$objTemplate->parse("autores", "AUTORES", true);
}

/* Muestro el Combo de Medios */
$objTemplate->set_block("PAGINA", "MEDIOS", "medios");
$objMedios = new clsNotas();
$objMedios->getMedios();

/* Coloco Primera Opcion */
$objTemplate->set_var(array(
	"intMedio" => 0,
	"strMedio" => "Seleccione...",
	"strSelected" => ""
));
$objTemplate->parse("medios", "MEDIOS");

for ($i = 0; $i < $objMedios->intTotal; $i++){
	$objMedios->getMediosRow($i);
	$objTemplate->set_var(array(
		"intMedio" => $objMedios->intMedio,
		"strMedio" => $objMedios->strMedio,
		"strSelected" => ($objMedios->intMedio == $intMedio) ? "selected" : ""
	));
	$objTemplate->parse("medios", "MEDIOS", true);
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
addBackofficeMenuCarpeta("Autores", "notas_autores.php", "users");
addBackofficeMenuCarpeta("Medios", "notas_medios.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nueva Nota", false, "Desde aqu&iacute; podr&aacute; agregar una nueva nota al sitio");

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