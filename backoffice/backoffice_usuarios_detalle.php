<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "checker.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";

/* Chequeo si el usuario tiene permisos para ver la seccion */
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
$blnPermisoUsuarioActual = true;
include_once("include_permisos.php");

$strUsuarioNombre = (isset($_GET["codUsuario"])) ? $_GET["codUsuario"] : "";
/* Me fijo si el usuario a mostrar existe */
if (!$strUsuarioNombre || !$objBackOfficeUsuarios->getUsuarios($strUsuarioNombre))
	redirect("backoffice_usuarios.php");

/* Me fijo si el usuario tiene permisos para ver esta secion, o no tiene permisos, pero esta viendo su usuario */
$blnUserLoggedNoAccess = false;
if (!$blnPermisoLectura){
	/* Me fijo si quere ver su usuario */
	if ($strUsuarioLogueadoBackoffice != $strUsuarioNombre){
		redirect("restricted.php");
	}else{
		$blnUserLoggedNoAccess = true;
	}
}

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_usuarios_detalle.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

/* Traigo el Usuario del backoffice */
$objBackOfficeUsuarios->getUsuariosRow();

/* Seteo todos los datos de Usuario */
$objTemplate->set_var(array(
	"strUsuario" => HTMLEntitiesFixed($objBackOfficeUsuarios->strUsuario),
	"strUsuarioTitulo" => HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strUsuario)),
	"strNombreUsuario" => HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strUsuario)),
	"strPerfil" => HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strPerfil)),
	"strDescripcion" => HTMLEntitiesFixed($objBackOfficeUsuarios->strDescripcion),
	"strNombre" => HTMLEntitiesFixed($objBackOfficeUsuarios->strNombre),
	"strApellido" => HTMLEntitiesFixed($objBackOfficeUsuarios->strApellido),
	"strEmail" => HTMLEntitiesFixed($objBackOfficeUsuarios->strEmail),
	"strFechaAlta" => $objBackOfficeUsuarios->strFechaAlta,
	"strFechaModificacion" => $objBackOfficeUsuarios->strFechaModificacion,
	"estadoAlt" => ($objBackOfficeUsuarios->blnHabilitado) ? "Deshabilitar" : "Habilitar",
	"estadoIcono" => ($objBackOfficeUsuarios->blnHabilitado) ? "" : "_on",
	"blnUsuarioActual" => ($objBackOfficeUsuarios->strUsuario == $strUsuarioLogueadoBackoffice) ? "true" : "false",
	"strHabilitado" => ($objBackOfficeUsuarios->blnHabilitado) ? "Habilitado" : "Deshabilitado"
));
$strNombreUsuario = HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strUsuario));

/* Seteo las opciones disponibles de modificacion */
if ($blnPermisoModificacion){
	$objTemplate->set_block("PAGINA", "MODIFICAR_USUARIOS", "modificar_usuarios");
	$objTemplate->parse("modificar_usuarios", "MODIFICAR_USUARIOS");
}
/* Oculto el volver si la persona no tiene acceso a la seccion, pero es el usuario logueado */
if (!$blnUserLoggedNoAccess){
	$objTemplate->set_block("PAGINA", "VOLVER", "volver");
	$objTemplate->parse("volver", "VOLVER");
}

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem($strNombreUsuario, "#");
addBackofficeMenuItem("Agregar Usuario", "backoffice_usuarios_agregar.php");
addBackofficeMenuCarpeta("PERFILES", "backoffice_perfiles.php", "users");
setBackofficeMenu();
setBackofficeEncabezado("Detalle de Usuario ", $strNombreUsuario, "Desde aqu&iacute; Ud. puede ver el detalle del usuario actual.");

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