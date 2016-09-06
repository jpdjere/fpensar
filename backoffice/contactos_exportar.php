<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "contactos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 4;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Levanto el orden  y direccion */
$intOrden = isset($_GET["o"]) ? intval($_GET["o"]) : 0;
if (!$intOrden || $intOrden < 1 || $intOrden > 3)
	$intOrden = 3;
$intDireccion = isset($_GET["d"]) ? intval($_GET["d"]) : 0;
if (!$intDireccion || ($intDireccion != 1 && $intDireccion != 2))
	$intDireccion = (($intOrden == 3) ? 2 : 1);

/* Traigo un Listado de todos los Contactos del backoffice */
$objContacto = new clsContactos();
$objContacto->getContactos(false, $intOrden, $intDireccion);

if ($objContacto->intTotal){

	header("Content-type: application/vnd.ms-excel");
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-Disposition:  filename=\"Codabox Listado de Contactos.xls\";");

	echo "<table border=1>" ;
	echo "<tr>";
	echo "<th>Nº</th><th>Nombre</th><th>Email</th><th>Asunto</th><th>Mensaje</th><th>Fecha</th></tr>";

	for ($i = 0; $i < $objContacto->intTotal; $i++){
		$objContacto->getContactosRow($i);
		echo "<tr>";
		echo "<td width=\"50\" align=\"center\">" . $objContacto->intContacto . "</td>";
		echo "<td width=\"150\" align=\"left\">" . capitalizeFirst($objContacto->strNombre) . "</td>";
		echo "<td width=\"250\" align=\"center\">" . $objContacto->strEmail . "</td>";
		echo "<td width=\"250\" align=\"center\">" . $objContacto->strAsunto . "</td>";
		echo "<td width=\"250\" align=\"center\">" . showTextBreaks($objContacto->strMensaje) . "</td>";
		echo "<td width=\"200\" align=\"center\">" . $objContacto->strFecha . "</td>";
	}

	echo "</table>"; 

}else{
	redirect("contactos.php");
}

?>