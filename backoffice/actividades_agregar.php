<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "actividades.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 6;
$intBackofficePermisoPagina = PERMISO_ALTA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "actividades_agregar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("actividades_agregar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strTitulo = (isset($_POST["strTitulo"])) ? stripSlashes(trim($_POST["strTitulo"])) : "";
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Actividad */
	$objActividades = new clsActividades();
	if ($objActividades->insertActividad($strTitulo, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado))
		redirect("actividades_detalle.php?codActividad=" . $objActividades->intActividad);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strActividadTitulo" => HTMLEntitiesFixed(capitalize($strTitulo)),
			"strTitulo" => HTMLEntitiesFixed(capitalizeFirst($strTitulo)),
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strImagen" => $objActividades->strImagen,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTitulo" => $objActividades->errorTitulo,
			"errorTexto" => $objActividades->errorTexto
		));

	}
}else{

	/* Inicializo los campos del formulario */
	$objTemplate->set_var(array(
		"strActividadTitulo" => "Nueva Actividad",
		"strTitulo" => "",
		"strTexto" => "",
		"strImagen" => IMAGEN_NO_DISPONIBLE,
		"strImagenAnterior" => IMAGEN_NO_DISPONIBLE,
		"blnHabilitado" => "",
		"blnDeshabilitado" => "checked"
	));

	$objActividades = new clsActividades();
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_ACTIVIDADES" => PATH_IMAGEN_ACTIVIDADES,
	"PATH_IMAGEN_ACTIVIDADES_LOCAL" => PATH_IMAGEN_ACTIVIDADES_LOCAL,
	"IMAGEN_ACTIVIDADES_ANCHO" => IMAGEN_ACTIVIDADES_ANCHO,
	"IMAGEN_ACTIVIDADES_ALTO" => IMAGEN_ACTIVIDADES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar");
setBackofficeMenu();
setBackOfficeEncabezado("Agregar nueva Actividad", false, "Desde aqu&iacute; podr&aacute; agregar una nueva actividad al sitio");

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