<?php

/* Incluyo los archivos necesarios */
include "includes/common.php";
include INCLUDES_DIR . "common_functions.php";
include INCLUDES_DIR . "templates.php";
include INCLUDES_DIR . "database.php";
include INCLUDES_DIR . "cursos.php";

// Levanto variables
$intCurso = (isset($_GET["c"])) ? intval($_GET["c"]) : 0;
$strEmail = (isset($_GET["e"])) ? $_GET["e"] : "";
$strCheck = (isset($_GET["t"])) ? $_GET["t"] : "";

if ($intCurso && $strEmail && $strCheck){

	// Instancio Cursos
	$objCursos = new clsCursos();

	// Chequeo que no esté inscripto
	$blnInscripto = $objCursos->getInscripto($intCurso, $strEmail);
	if ($blnInscripto){
		echo("INSCRIPTO");
		die();
	}

	// Me fijo si hay cupos
	$objCursos->getCursosActivos($intCurso, true);
	$objCursos->getCursosRow();
	if ($objCursos->intCuposDisponibles > 0){
		// Guardo al postulante
		$objCursos->inscriptUsuario($intCurso, $strEmail);
		if ($objCursos->intInscripto){
			$objCursos->getCursos($intCurso);
			$objCursos->getCursosRow();
			echo ("OK|" . $intCurso . "|" . $objCursos->intCuposDisponibles . "|");
		}else{
			echo("ERROR_DB");
		}
	}else{
		echo("ERROR_NO_CUPOS");
	}

}else{
	echo("ERROR");
}

?>