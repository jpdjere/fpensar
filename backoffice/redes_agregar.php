<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "redes.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 12;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "redes_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("redes_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$intProvincia = (isset($_POST["intProvincia"])) ? intval($_POST["intProvincia"]) : 0;
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Red */
	$objRedes = new clsRedes();
	if ($objRedes->insertRed($strTitulo, $intProvincia, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado))
		redirect("redes_detalle.php?codRed=" . $objRedes->intRed);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strRedTitulo" => HTMLEntitiesFixed(capitalize($strTitulo)),
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed($strTexto), true),
			"strImagen" => $objRedes->strImagen,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTitulo" => $objRedes->errorTitulo,
			"errorProvincia" => $objRedes->errorProvincia,
			"errorTexto" => $objRedes->errorTexto,
			"errorImagen" => $objRedes->errorImagen
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strRedTitulo" => "Nueva Red",
		"strTitulo" => "",
		"strTexto" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strImagenAnterior" => IMAGEN_NO_DISPONIBLE,
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objRedes = new clsRedes();
	$intProvincia = 0;
}

/* Muestro el Combo de Redes */
$objTemplate->set_block("PAGINA", "PROVINCIAS", "provincias");
$objRedes = new clsRedes();
$objRedes->getProvincias();

/* Coloco Primera Opcion */
$objTemplate->set_var(array(
	"intProvincia" => 0,
	"strProvincia" => "Seleccione...",
	"strSelected" => ""
));
$objTemplate->parse("provincias", "PROVINCIAS");

for ($i = 0; $i < $objRedes->intTotal; $i++){
	$objRedes->getProvinciasRow($i);
	$objTemplate->set_var(array(
		"intProvincia" => $objRedes->intProvincia,
		"strProvincia" => $objRedes->strProvincia,
		"strSelected" => ($objRedes->intProvincia == $intProvincia) ? "selected" : ""
	));
	$objTemplate->parse("provincias", "PROVINCIAS", true);
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_REDES" => PATH_IMAGEN_REDES,
	"PATH_IMAGEN_REDES_LOCAL" => PATH_IMAGEN_REDES_LOCAL,
	"IMAGEN_REDES_ANCHO" => IMAGEN_REDES_ANCHO,
	"IMAGEN_REDES_ALTO" => IMAGEN_REDES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nuevo Red", false, "Desde aqu&iacute; podr&aacute; agregar un nuevo red al sitio");

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