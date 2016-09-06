<?php

/* Incluyo los archivos necesarios */
include "includes/common.php";
include INCLUDES_DIR . "common_functions.php";
include INCLUDES_DIR . "database.php";
include INCLUDES_DIR . "documentos.php";

// Obtengo Buscqueda
$strBusqueda = (isset($_GET["b"])) ? trim($_GET["b"]) : "";
/*if (!$strBusqueda){
	echo("ERROR");
	die();
}*/

// Levanto Documentos
$objDocumentos = new clsDocumentos();
$objDocumentos->getDocumentos(false, false, false, $strBusqueda);

$strResultados = "";
for ($i = 0; $i < $objDocumentos->intTotal && $i < 20; $i++){
	$objDocumentos->getDocumentosRow($i);

	$strResultados .= ($strResultados) ? "|" : "";
	$strResultados .= rawurlencode($objDocumentos->strTitulo) . "$$";
	$strResultados .= rawurlencode($objDocumentos->strAutor) . "$$";
	$strResultados .= rawurlencode($objDocumentos->strTexto) . "$$";
	$strResultados .= rawurlencode($objDocumentos->strFecha) . "$$";
	$strResultados .= rawurlencode($objDocumentos->strImagen) . "$$";
	$strResultados .= rawurlencode($objDocumentos->strArchivo) . "$$";

}

echo ("OK;" . $strResultados . ";")

?>