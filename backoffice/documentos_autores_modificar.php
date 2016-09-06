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
$intSeccionBackOffice = 3;
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si la nota a mostrar existe */
$objNotas = new clsNotas();
$intAutor = (isset($_GET["codAutor"])) ? intval($_GET["codAutor"]) : ((isset($_POST["codAutor"])) ? intval($_POST["codAutor"]) : "");
if (!$intAutor || !$objNotas->getAutores($intAutor, true))
	redirect("documentos_autores.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "documentos_autores_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("documentos_autores_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$intAutor = (isset($_POST["codAutor"])) ? intval($_POST["codAutor"]) : "";
	$strAutor = (isset($_POST["strAutor"])) ? stripSlashes(trim($_POST["strAutor"])) : "";
	$strImagen = (isset($_FILES["strImagen"]["name"])) ? $_FILES["strImagen"] : "";
	$strImagenAnterior = (isset($_POST["strImagenAnterior"])) ? $_POST["strImagenAnterior"] : "";
	$blnHabilitado = (isset($_POST["strHabilitado"])) ? ($_POST["strHabilitado"] == "true") : false;

	/* Inserto Nota */
	$objNotas = new clsNotas();
	if ($objNotas->updateAutor($intAutor, $strAutor, $strImagen, $strImagenAnterior, $blnHabilitado))
		redirect("documentos_autores_detalle.php?codAutor=" . $intAutor);
	else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"codAutor" => $intAutor,
			"strAutor" => HTMLEntitiesFixed(capitalize($strAutor)),
			"strImagen" => $objNotas->strImagen,
			"blnHabilitado" => ($blnHabilitado) ? "checked" : "",
			"blnDeshabilitado" => ($blnHabilitado) ? "" : "checked"
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorAutor" => $objNotas->errorAutor,
			"errorImagen" => $objNotas->errorImagen
		));

	}
}else{

	/* Levanto los datos de la Nota */
	$objNotas->getAutoresRow();
	$objTemplate->set_var(array(
		"codAutor" => $objNotas->intAutor,
		"strAutor" => HTMLEntitiesFixed($objNotas->strAutor),
		"strImagen" => $objNotas->strImagen,
		"blnHabilitado" => ($objNotas->blnHabilitado) ? "checked" : "",
		"blnDeshabilitado" => ($objNotas->blnHabilitado) ? "" : "checked"
	));

	$strAutor = $objNotas->strAutor;
}

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_AUTORES" => PATH_IMAGEN_NOTAS_AUTORES,
	"PATH_IMAGEN_NOTAS_AUTORES_LOCAL" => PATH_IMAGEN_NOTAS_AUTORES_LOCAL,
	"IMAGEN_NOTAS_AUTORES_CHICA_ANCHO" => IMAGEN_NOTAS_AUTORES_CHICA_ANCHO,
	"IMAGEN_NOTAS_AUTORES_CHICA_ALTO" => IMAGEN_NOTAS_AUTORES_CHICA_ALTO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO" => IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ALTO" => IMAGEN_NOTAS_AUTORES_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "#", "users");
addBackofficeMenuItem($strAutor);
setBackofficeMenu();
setBackOfficeEncabezado("Modificar autor ", $strAutor, "Desde aqu&iacute; podr&aacute; modificar el autor de un nota.");

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