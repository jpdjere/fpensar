<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "destacados.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 9;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "destacados_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("destacados_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$intPosicion = (isset($_POST["intPosicion"])) ? intval($_POST["intPosicion"]) : 0;
	$strLinkURL = (isset($_POST["strLinkURL"])) ? stripSlashes(trim($_POST["strLinkURL"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Destacado */
	$objDestacados = new clsDestacados();
	if ($objDestacados->insertDestacado($strTitulo, $intPosicion, $strLinkURL, $strImagen, $strImagenAnterior, $blnHabilitado))
		redirect("destacados_detalle.php?codDestacado=" . $objDestacados->intDestacado);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strDestacadoTitulo" => HTMLEntitiesFixed(capitalize($strTitulo)),
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strLinkURL" => HTMLEntitiesFixed($strLinkURL),
			"strImagen" => $objDestacados->strImagen,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTitulo" => $objDestacados->errorTitulo,
			"errorPosicion" => $objDestacados->errorPosicion,
			"errorLinkURL" => $objDestacados->errorLinkURL,
			"errorImagen" => $objDestacados->errorImagen
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strDestacadoTitulo" => "Nueva Destacado",
		"strTitulo" => "",
		"strLinkURL" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strImagenAnterior" => IMAGEN_NO_DISPONIBLE,
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objDestacados = new clsDestacados();
	$intPosicion = 0;
}

/* Muestro el Combo de Destacados */
$objTemplate->set_block("PAGINA", "POSICIONES", "posiciones");
$objDestacados = new clsDestacados();
$objDestacados->getPosiciones();

/* Coloco Primera Opcion */
$objTemplate->set_var(array(
	"intPosicion" => 0,
	"strPosicion" => "Seleccione...",
	"strSelected" => ""
));
$objTemplate->parse("posiciones", "POSICIONES");

for ($i = 0; $i < $objDestacados->intTotal; $i++){
	$objDestacados->getPosicionesRow($i);
	$objTemplate->set_var(array(
		"intPosicion" => $objDestacados->intPosicion,
		"strPosicion" => $objDestacados->strPosicion,
		"strSelected" => ($objDestacados->intPosicion == $intPosicion) ? "selected" : ""
	));
	$objTemplate->parse("posiciones", "POSICIONES", true);
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_DESTACADOS" => PATH_IMAGEN_DESTACADOS,
	"PATH_IMAGEN_DESTACADOS_LOCAL" => PATH_IMAGEN_DESTACADOS_LOCAL,
	"IMAGEN_DESTACADOS_ANCHO" => IMAGEN_DESTACADOS_ANCHO,
	"IMAGEN_DESTACADOS_ALTO" => IMAGEN_DESTACADOS_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nuevo Destacado", false, "Desde aqu&iacute; podr&aacute; agregar un nuevo destacado al sitio");

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