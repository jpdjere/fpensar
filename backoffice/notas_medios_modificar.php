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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si la nota a mostrar existe */
$objNotas = new clsNotas();
$intMedio = (isset($_GET["codMedio"])) ? intval($_GET["codMedio"]) : ((isset($_POST["codMedio"])) ? intval($_POST["codMedio"]) : "");
if (!$intMedio || !$objNotas->getMedios($intMedio, true))
	redirect("notas_medios.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_medios_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("notas_medios_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intMedio = (isset($_POST["codMedio"])) ? intval($_POST["codMedio"]) : "";
	$strMedio = (isset($_POST["strMedio"])) ? stripSlashes(trim($_POST["strMedio"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Nota */
	$objNotas = new clsNotas();
	if ($objNotas->updateMedio($intMedio, $strMedio, $strImagen, $strImagenAnterior, $blnHabilitado))
		redirect("notas_medios_detalle.php?codMedio=" . $intMedio);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codMedio" => $intMedio,
			"strMedio" => HTMLEntitiesFixed(capitalize($strMedio)),
			"strImagen" => $objNotas->strImagen,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorMedio" => $objNotas->errorMedio,
			"errorImagen" => $objNotas->errorImagen
		));

	}
}else{

	/* Levanto los datos de la Nota */
	$objNotas->getMediosRow();
	$objTemplate->set_var(array(
		"codMedio" => $objNotas->intMedio,
		"strMedio" => HTMLEntitiesFixed($objNotas->strMedio),
		"strImagen" => $objNotas->strImagen,
		"blnHabilitado" => ($objNotas->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objNotas->blnHabilitado) ? "" : "checked"
	));

	$strMedio = $objNotas->strMedio;
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_MEDIOS" => PATH_IMAGEN_NOTAS_MEDIOS,
	"PATH_IMAGEN_NOTAS_MEDIOS_LOCAL" => PATH_IMAGEN_NOTAS_MEDIOS_LOCAL,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO" => IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_CHICA_ALTO" => IMAGEN_NOTAS_MEDIOS_CHICA_ALTO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO,
	"IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO" => IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "notas_autores.php", "users");
addBackofficeMenuCarpeta("Medios", "#", "users");
addBackofficeMenuItem($strMedio);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar medio ", $strMedio, "Desde aqu&iacute; podr&aacute; modificar el medio de un nota.");

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