<?php

// Defino Variables
$strBackOfficeMenuSeccion = "";
if (!isset($blnPermisoUsuarioActual)){
	$blnPermisoUsuarioActual = false;
}

/* Instancio Clases */
$objBackOfficeUsuarios = new clsBackOfficeUsuarios();
$objBackOfficePerfiles = new clsBackOfficePerfiles();
$objBackOfficeSecciones = new clsBackOfficeSecciones();

$strUsuarioLogueadoBackoffice = (isset($_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"])) ? $_SESSION[WEBSITE_KEY . "_" . "strUsuarioBackoffice"] : "";
if (!$strUsuarioLogueadoBackoffice)
	redirect("timeout.php");

// Obtengo Secciones y perfil
$objBackOfficeSecciones->getSecciones(false, $strUsuarioLogueadoBackoffice);

if ($intSeccionBackOffice && !$objBackOfficeSecciones->intTotal){
	redirect("restricted.php");
}

$intAccesoUsuarioBackoffice = 0;
if ($intSeccionBackOffice){
	for ($i = 0; $i < $objBackOfficeSecciones->intTotal; $i++){
		$objBackOfficeSecciones->getSeccionesRow($i);
		if ($objBackOfficeSecciones->intSeccion == $intSeccionBackOffice){
			$intAccesoUsuarioBackoffice = $objBackOfficeSecciones->intAcceso;
		}
	}

	if (!$intAccesoUsuarioBackoffice){
		if (!$blnPermisoUsuarioActual){
			redirect("restricted.php");
		}
	}
}

// Obtengo Permisos
$blnPermisoLectura = $objBackOfficePerfiles->getPermisoSeccion($intAccesoUsuarioBackoffice, PERMISO_SOLO_LECTURA);
$blnPermisoAlta = $objBackOfficePerfiles->getPermisoSeccion($intAccesoUsuarioBackoffice, PERMISO_ALTA);
$blnPermisoBaja = $objBackOfficePerfiles->getPermisoSeccion($intAccesoUsuarioBackoffice, PERMISO_BAJA);
$blnPermisoModificacion = $objBackOfficePerfiles->getPermisoSeccion($intAccesoUsuarioBackoffice, PERMISO_MODIFICACION);

if ($intSeccionBackOffice && !$blnPermisoUsuarioActual && !$objBackOfficePerfiles->getPermisoSeccion($intAccesoUsuarioBackoffice, $intBackofficePermisoPagina)){
	redirect("restricted.php");
}

?>