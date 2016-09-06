<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 2;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "backoffice_usuarios.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "USUARIOS", "usuarios");
$objTemplate->set_block("USUARIOS", "MODIFICAR_USUARIOS_UPDATE", "modificar_usuarios_update");
$objTemplate->set_block("USUARIOS", "MODIFICAR_USUARIOS_DELETE", "modificar_usuarios_delete");
$objTemplate->set_block("PAGINA", "AGREGAR_USUARIOS", "agregar_usuarios");

// Levanto el orden  y direccion
$intTotalOpcionesOrden = 3;
$intOrden = isset($_GET["o"]) ? intval($_GET["o"]) : 0;
if (!$intOrden || $intOrden < 1 || $intOrden > $intTotalOpcionesOrden)
	$intOrden = $intTotalOpcionesOrden;
$intDireccion = isset($_GET["d"]) ? intval($_GET["d"]) : 0;
if (!$intDireccion || ($intDireccion != 1 && $intDireccion != 2))
	$intDireccion = (($intOrden == $intTotalOpcionesOrden) ? 2 : 1);

/* Traigo un Listado de todos los Usuarios del backoffice */
$objBackOfficeUsuarios->getUsuarios(false, false, $intOrden, $intDireccion);

/* Levanto la pagina a mostrar */
$intPagina = isset($_GET["intPagina"]) ? $_GET["intPagina"] : "";
if (!$intPagina)
	$intPagina = 1;

$intCantidadRegistros = $objBackOfficeUsuarios->intTotal;
for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	$objBackOfficeUsuarios->getUsuariosRow($i);
	$objTemplate->set_var(array(
		"strUsuario" => HTMLEntitiesFixed($objBackOfficeUsuarios->strUsuario),
		"strNombreUsuario" => HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strUsuario)),
		"strPerfil" => HTMLEntitiesFixed(capitalizeFirst($objBackOfficeUsuarios->strPerfil)),
		"strDescripcion" => HTMLEntitiesFixed($objBackOfficeUsuarios->strDescripcion),
		"strNombre" => HTMLEntitiesFixed($objBackOfficeUsuarios->strNombre),
		"strApellido" => HTMLEntitiesFixed($objBackOfficeUsuarios->strApellido),
		"strEmail" => HTMLEntitiesFixed($objBackOfficeUsuarios->strEmail),
		"strFechaAlta" => $objBackOfficeUsuarios->strFechaAlta,
		"strFechaModificacion" => $objBackOfficeUsuarios->strFechaModificacion,
		"estadoIcono" => ($objBackOfficeUsuarios->blnHabilitado) ? "" : "_on",
		"estadoAlt" => ($objBackOfficeUsuarios->blnHabilitado) ? "Deshabilitar" : "Habilitar",
		"blnUsuarioActual" => ($objBackOfficeUsuarios->strUsuario == $strUsuarioLogueadoBackoffice) ? "true" : "false"
	));

	// Seteo Permisos de UPDATE
	if ($blnPermisoModificacion)
		$objTemplate->parse("modificar_usuarios_update", "MODIFICAR_USUARIOS_UPDATE");
	else
		$objTemplate->set_var("modificar_usuarios_update", "");

	// Seteo Permisos de DETELE
	if ($blnPermisoBaja)
		$objTemplate->parse("modificar_usuarios_delete", "MODIFICAR_USUARIOS_DELETE");
	else
		$objTemplate->set_var("modificar_usuarios_delete", "");

	$objTemplate->parse("usuarios", "USUARIOS", true);
}

if ($blnPermisoAlta)
	$objTemplate->parse("agregar_usuarios", "AGREGAR_USUARIOS");
else
	$objTemplate->set_var("agregar_usuarios", "");

// Parseo Orden
$objTemplate->set_var("strOrdenParameter", "?o=");

// Parseo Direccion
for ($i = 1; $i <= $intTotalOpcionesOrden; $i++){
	$objTemplate->set_var("strDireccionOrden" . $i, ($intOrden == $i) ? (($intDireccion == 1) ? 2 : 1) : 1);
}

/* Incluyo Paginador */
$strPage = "usuarios";
$strParameters = "o=" . $intOrden . "&d=" . $intDireccion;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuItem("Agregar Usuario", "backoffice_usuarios_agregar.php");
addBackofficeMenuCarpeta("PERFILES", "backoffice_perfiles.php", "users");
setBackofficeMenu();
setBackofficeEncabezado("Listado de Usuarios ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; Ud. puede ver todos los usuarios que poseen acceso al Backoffice.");

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