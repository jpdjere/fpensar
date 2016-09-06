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
$intBackofficePermisoPagina = PERMISO_MODIFICACION;
include_once("include_permisos.php");

/* Me fijo si el auto a mostrar existe */
$objRedes = new clsRedes();

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "redes_mensaje_modificar.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

if (checkReferer("redes_mensaje_modificar.php") && $_POST){

	/* Levanto los usuarios del formulario */
	$strTexto = (isset($_POST["strTexto"])) ? stripSlashes(trim($_POST["strTexto"])) : "";
	$strLink = (isset($_POST["strLinkURL"])) ? stripSlashes(trim($_POST["strLinkURL"])) : "";

	/* Inserto Red */
	$objRedes = new clsRedes();
	if ($objRedes->updateRedMensaje($strTexto, $strLink)){
		redirect("redes_mensaje.php");
	}else{

		/* Muestro Datos Ingresados */
		$objTemplate->set_var(array(
			"strTexto" => showTextBreaks(HTMLEntitiesFixed(capitalizeFirst($strTexto)), true),
			"strLinkURL" => HTMLEntitiesFixed($strLink)
		));

		/* Muestro Errores */
		$objTemplate->set_var(array(
			"errorTexto" => $objRedes->errorTexto,
			"errorLinkURL" => $objRedes->errorLink
		));

	}
}else{

	/* Levanto los datos del Red */
	$objRedes->getRedesMensaje();
	if ($objRedes->intTotal){
		$objRedes->getRedesMensajeRow();
		$objTemplate->set_var(array(
			"strTexto" => showTextBreaks(HTMLEntitiesFixed($objRedes->strMensaje), true),
			"strLinkURL" => HTMLEntitiesFixed($objRedes->strLink)
		));
	}

}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Mensaje Próximamente", "redes_mensaje.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Modificar Mensaje Próximamente", "", "Desde aqu&iacute; podr&aacute; modificar el mensaje de próximamente.");

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