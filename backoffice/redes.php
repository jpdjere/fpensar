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

// Chequeo permisos y perfiles
$intSeccionBackOffice = 12;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "redes.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "REDES", "redes");
$objTemplate->set_block("REDES", "MODIFICAR_REDES_UPDATE", "modificar_redes_update");
$objTemplate->set_block("REDES", "MODIFICAR_REDES_DELETE", "modificar_redes_delete");
$objTemplate->set_block("REDES", "REDES_AGREGAR_HOME", "redes_agregar_home");
$objTemplate->set_block("REDES", "REDES_BORRAR_HOME", "redes_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_REDES_ADD", "modificar_redes_add");
$objTemplate->set_block("PAGINA", "REDES_VACIO_BUSQUEDA", "redes_vacio_busqueda");
$objTemplate->set_block("PAGINA", "REDES_VACIO", "redes_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de redes del sitio */
$objRedes = new clsRedes();
$intCantidadRegistros = $objRedes->getRedesTotal(false, true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todos los Redes del Site por página */
	$objRedes->getRedes(false, false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
		$objRedes->getRedesRow($i);
		$objTemplate->set_var(array(
			"codRed" => $objRedes->intRed,
			"intRedCoded" => encodeNumber($objRedes->intRed),
			"strTitulo" => HTMLEntitiesFixed($objRedes->strTitulo),
			"strProvincia" => HTMLEntitiesFixed($objRedes->strProvincia),
			"strImagen" => ($objRedes->strImagen) ? $objRedes->strImagen : IMAGEN_NO_DISPONIBLE,
			"strFechaAlta" => $objRedes->strFechaAlta,
			"strFechaModificacion" => $objRedes->strFechaModificacion,
			"strEstado" => ($objRedes->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objRedes->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objRedes->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_redes_update", "MODIFICAR_REDES_UPDATE");
		else
			$objTemplate->set_var("modificar_redes_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_redes_delete", "MODIFICAR_REDES_DELETE");
		else
			$objTemplate->set_var("modificar_redes_delete", "");

		$objTemplate->parse("redes", "REDES", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("redes", "");
		$objTemplate->set_var("redes_vacio", "");
		$objTemplate->parse("redes_vacio_busqueda", "REDES_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("redes", "");
		$objTemplate->set_var("redes_vacio_busqueda", "");
		$objTemplate->parse("redes_vacio", "REDES_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_redes_add", "MODIFICAR_REDES_ADD");
else
	$objTemplate->set_var("modificar_redes_add", "");

/* Incluyo Paginador */
$strPage = "redes";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var(array(
	"PATH_IMAGEN_REDES" => PATH_IMAGEN_REDES,
	"PATH_IMAGEN_REDES_LOCAL" => PATH_IMAGEN_REDES_LOCAL,
	"IMAGEN_REDES_ANCHO" => IMAGEN_REDES_ANCHO,
	"IMAGEN_REDES_ALTO" => IMAGEN_REDES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Link Actividades", "redes_mensaje.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Redes ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todos las redes del sitio.<br>Para utilizar el buscador s&oacute;lo escriba las palabras a encontrar en la red y presione <i>Iniciar B&uacute;squeda</i>.");

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