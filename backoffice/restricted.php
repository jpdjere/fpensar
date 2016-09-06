<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura_simple.html",
	"HEADER" => TEMPLATES_DIR . "header_error.html",
	"PAGINA" => TEMPLATES_DIR . "mensaje_error.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "RESTRICTED", "restricted");
$objTemplate->parse("restricted", "RESTRICTED");

if (isset($_SERVER["HTTP_REFERER"]))
	$objTemplate->set_var("linkPage", $_SERVER["HTTP_REFERER"]);

// Set Page Class
$objTemplate->set_var("strPageClass", ' class="loginPage"');

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
	"HEADER" => "HEADER",
	"PAGINA" => "PAGINA",
	"FOOTER" => "FOOTER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out");

?>