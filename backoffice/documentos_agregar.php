<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "documentos.php";
include INCLUDES_BACKOFFICE_DIR . "notas.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 3;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "documentos_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("documentos_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intAutor = (isset($_POST["intAutor"])) ? intval($_POST["intAutor"]) : 0;
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strTags = (isset($_POST["hidden-strTags"])) ? stripSlashes(trim($_POST["hidden-strTags"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$strArchivo = (isset($_FILES["strArchivo"]["name"])) ? $_FILES["strArchivo"] : "";
	$strArchivoAnterior = (isset($_POST["strArchivoAnterior"])) ? trim($_POST["strArchivoAnterior"]) : "";
	$strFecha = (isset($_POST["strFecha"])) ? trim($_POST["strFecha"]) : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Documento */
	$objDocumentos = new clsDocumentos();
	if ($objDocumentos->insertDocumento($intAutor, $strTitulo, $strTexto, $strTags, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado))
		redirect("documentos_detalle.php?codDocumento=" . $objDocumentos->intDocumento);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strDocumentoTitulo" => HTMLEntitiesFixed(capitalize($strTitulo)),
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strTags" => HTMLEntitiesFixed($strTags),
			"strTagsParsed" => str_replace(',', '", "', $strTags),
			"strImagen" => $objDocumentos->strImagen,
			"strArchivo" => $objDocumentos->strArchivo,
			"strFecha" => $strFecha,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorAutor" => $objNotas->errorAutor,
			"errorTitulo" => $objDocumentos->errorTitulo,
			"errorTexto" => $objDocumentos->errorTexto,
			"errorTags" => $objDocumentos->errorTags,
			"errorImagen" => $objDocumentos->errorImagen,
			"errorArchivo" => $objDocumentos->errorArchivo,
			"errorFecha" => $objDocumentos->errorFecha
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strDocumentoTitulo" => "Nueva Documento",
		"strTitulo" => "",
		"strTexto" => "",
		"strTags" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strArchivo" => "",
		"strFecha" => date("d") . "/" . date("m") . "/" . date("Y"),
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objDocumentos = new clsDocumentos();
	$intAutor = 0;
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

$objTemplate->set_var(array(
	"PATH_IMAGEN_DOCUMENTOS" => PATH_IMAGEN_DOCUMENTOS,
	"PATH_IMAGEN_DOCUMENTOS_LOCAL" => PATH_IMAGEN_DOCUMENTOS_LOCAL,
	"IMAGEN_DOCUMENTOS_CHICA_ANCHO" => IMAGEN_DOCUMENTOS_CHICA_ANCHO,
	"IMAGEN_DOCUMENTOS_CHICA_ALTO" => IMAGEN_DOCUMENTOS_CHICA_ALTO,
	"IMAGEN_DOCUMENTOS_GRANDE_ANCHO" => IMAGEN_DOCUMENTOS_GRANDE_ANCHO,
	"IMAGEN_DOCUMENTOS_GRANDE_ALTO" => IMAGEN_DOCUMENTOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
addBackofficeMenuCarpeta("Autores", "documentos_autores.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nuevo Documento", false, "Desde aqu&iacute; podr&aacute; agregar un nuevo documento al sitio");

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