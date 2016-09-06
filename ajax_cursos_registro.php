<?php

/* Incluyo los archivos necesarios */
include "includes/common.php";
include INCLUDES_DIR . "common_functions.php";
include INCLUDES_DIR . "templates.php";
include INCLUDES_DIR . "database.php";
include INCLUDES_DIR . "cursos.php";

// Levanto variables
$intCurso = (isset($_GET["c"])) ? intval($_GET["c"]) : 0;
$strNombre = (isset($_GET["n"])) ? $_GET["n"] : "";
$strApellido = (isset($_GET["a"])) ? $_GET["a"] : "";
$strDNI = (isset($_GET["d"])) ? $_GET["d"] : "";
$strEmail = (isset($_GET["e"])) ? $_GET["e"] : "";
$strProvincia = (isset($_GET["p"])) ? $_GET["p"] : "";
$strTelefono = (isset($_GET["t"])) ? $_GET["t"] : "";
$strContrasenia = (isset($_GET["p"])) ? $_GET["p"] : "";
$strCheck = (isset($_GET["w"])) ? $_GET["w"] : "";

if ($intCurso && $strNombre && $strApellido && $strDNI && $strEmail && $strProvincia && $strTelefono && $strContrasenia && $strCheck){

	// Instancio Cursos
	$objCursos = new clsCursos();

	// Chequeo que no esté inscripto
	$blnInscripto = $objCursos->getInscripto($intCurso, $strEmail);
	if ($blnInscripto){
		echo("INSCRIPTO");
		die();
	}

	// Guardo al postulante
	$objCursos->inscriptNuevoUsuario($intCurso, $strNombre, $strApellido, $strDNI, $strEmail, $strProvincia, $strTelefono, $strContrasenia);
	if ($objCursos->intInscripto){
		$objCursos->getCursos($intCurso);
		$objCursos->getCursosRow();
		echo ("OK|" . $intCurso . "|" . $objCursos->intCuposDisponibles . "|");
	}else{
		echo("ERROR_DB");
	}

}else{
	echo("ERROR");
}

?>