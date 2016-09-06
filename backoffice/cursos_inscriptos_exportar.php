<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "cursos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 11;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

$intCurso = (isset($_GET["codCurso"])) ? intval($_GET["codCurso"]) : 0;
if (!$intCurso)
	redirect("cursos.php");

/* Levanto el orden  y direccion */
$intOrden = isset($_GET["o"]) ? intval($_GET["o"]) : 0;
if (!$intOrden || $intOrden < 1 || $intOrden > 3)
	$intOrden = 3;
$intDireccion = isset($_GET["d"]) ? intval($_GET["d"]) : 0;
if (!$intDireccion || ($intDireccion != 1 && $intDireccion != 2))
	$intDireccion = (($intOrden == 3) ? 2 : 1);

/* Traigo un Listado de todos los Cursos del backoffice */
$objCurso = new clsCursos();
$objCurso->getInscriptos($intCurso, false, $intOrden, $intDireccion);

if ($objCurso->intTotal){

	header("Content-type: application/vnd.ms-excel");
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-Disposition:  filename=\"Fundación Pensar Listado de Inscriptos a Curso.xls\";");

	$objCurso->getInscriptosRow(0);

	echo "<table border=1>" ;
	echo "<tr>";
	echo "<th colspan='7'>" . $objCurso->strCurso . "</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<th>Nº</th><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Email</th><th>Provincia</th><th>Teléfono</th><th>Fecha</th>";
	echo "</tr>";

	for ($i = 0; $i < $objCurso->intTotal; $i++){
		$objCurso->getInscriptosRow($i);
		echo "<tr>";
		echo "<td width=\"50\" align=\"center\">" . $objCurso->intCurso . "</td>";
		echo "<td width=\"150\" align=\"left\">" . capitalizeFirst($objCurso->strNombre) . "</td>";
		echo "<td width=\"150\" align=\"left\">" . capitalizeFirst($objCurso->strApellido) . "</td>";
		echo "<td width=\"150\" align=\"center\">" . $objCurso->strDNI . "</td>";
		echo "<td width=\"250\" align=\"center\">" . $objCurso->strEmail . "</td>";
		echo "<td width=\"250\" align=\"center\">" . capitalizeFirst($objCurso->strProvincia) . "</td>";
		echo "<td width=\"250\" align=\"center\">" . $objCurso->strTelefono . "</td>";
		echo "<td width=\"200\" align=\"center\">" . $objCurso->strFecha . "</td>";
		echo "</tr>";
	}

	echo "</table>"; 

}else{
	redirect("cursos_inscriptos.php?codCurso=" . $intCurso);
}

?>