<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "coyuntura.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 8;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "coyuntura.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "COYUNTURA", "coyuntura");
$objTemplate->set_block("COYUNTURA", "MODIFICAR_COYUNTURA_UPDATE", "modificar_coyuntura_update");
$objTemplate->set_block("COYUNTURA", "MODIFICAR_COYUNTURA_DELETE", "modificar_coyuntura_delete");
$objTemplate->set_block("COYUNTURA", "COYUNTURA_AGREGAR_HOME", "coyuntura_agregar_home");
$objTemplate->set_block("COYUNTURA", "COYUNTURA_BORRAR_HOME", "coyuntura_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_COYUNTURA_ADD", "modificar_coyuntura_add");
$objTemplate->set_block("PAGINA", "COYUNTURA_VACIO_BUSQUEDA", "coyuntura_vacio_busqueda");
$objTemplate->set_block("PAGINA", "COYUNTURA_VACIO", "coyuntura_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de coyuntura del sitio */
$objCoyuntura = new clsCoyuntura();
$intCantidadRegistros = $objCoyuntura->getCoyunturaTotal(true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Coyuntura del Site por página */
	$objCoyuntura->getCoyuntura(false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	//for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	for ($i = 0; $i < $objCoyuntura->intTotal; $i++){
		$objCoyuntura->getCoyunturaRow($i);
		$objTemplate->set_var(array(
			"codCoyuntura" => $objCoyuntura->intCoyuntura,
			"intCoyunturaCoded" => encodeNumber($objCoyuntura->intCoyuntura),
			"strTitulo" => HTMLEntitiesFixed($objCoyuntura->strTitulo),
			"strFechaAlta" => $objCoyuntura->strFechaAlta,
			"strFechaModificacion" => $objCoyuntura->strFechaModificacion,
			"strFechaListado" => $objCoyuntura->strFechaListado,
			"strEstado" => ($objCoyuntura->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objCoyuntura->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objCoyuntura->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_coyuntura_update", "MODIFICAR_COYUNTURA_UPDATE");
		else
			$objTemplate->set_var("modificar_coyuntura_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_coyuntura_delete", "MODIFICAR_COYUNTURA_DELETE");
		else
			$objTemplate->set_var("modificar_coyuntura_delete", "");

		$objTemplate->parse("coyuntura", "COYUNTURA", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("coyuntura", "");
		$objTemplate->set_var("coyuntura_vacio", "");
		$objTemplate->parse("coyuntura_vacio_busqueda", "COYUNTURA_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("coyuntura", "");
		$objTemplate->set_var("coyuntura_vacio_busqueda", "");
		$objTemplate->parse("coyuntura_vacio", "COYUNTURA_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_coyuntura_add", "MODIFICAR_COYUNTURA_ADD");
else
	$objTemplate->set_var("modificar_coyuntura_add", "");

/* Incluyo Paginador */
$strPage = "coyuntura";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
setBackofficeMenu();
setBackOfficeEncabezado("Listado de ediciones de Coyuntura ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todas las ediciones de Coyuntura del sitio.<br>Para utilizar el buscador s&oacute;lo coloque las palabras a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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