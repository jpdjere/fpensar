<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "eventos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 10;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "eventos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "EVENTOS", "eventos");
$objTemplate->set_block("EVENTOS", "MODIFICAR_EVENTOS_UPDATE", "modificar_eventos_update");
$objTemplate->set_block("EVENTOS", "MODIFICAR_EVENTOS_DELETE", "modificar_eventos_delete");
$objTemplate->set_block("EVENTOS", "EVENTOS_AGREGAR_HOME", "eventos_agregar_home");
$objTemplate->set_block("EVENTOS", "EVENTOS_BORRAR_HOME", "eventos_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_EVENTOS_ADD", "modificar_eventos_add");
$objTemplate->set_block("PAGINA", "EVENTOS_VACIO_BUSQUEDA", "eventos_vacio_busqueda");
$objTemplate->set_block("PAGINA", "EVENTOS_VACIO", "eventos_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de eventos del sitio */
$objEventos = new clsEventos();
$intCantidadRegistros = $objEventos->getEventosTotal(true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Eventos del Site por página */
	$objEventos->getEventos(false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	//for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	for ($i = 0; $i < $objEventos->intTotal; $i++){
		$objEventos->getEventosRow($i);
		$objTemplate->set_var(array(
			"codEvento" => $objEventos->intEvento,
			"intEventoCoded" => encodeNumber($objEventos->intEvento),
			"strTitulo" => HTMLEntitiesFixed($objEventos->strTitulo),
			"strTexto" => cutText(showTextBreaks(HTMLEntitiesFixed($objEventos->strTexto)), CARACTERES_TEXTO_LISTADO * 1.5),
			"strFechaAlta" => $objEventos->strFechaAlta,
			"strFechaModificacion" => $objEventos->strFechaModificacion,
			"strFechaListado" => $objEventos->strFechaListado,
			"strEstado" => ($objEventos->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objEventos->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objEventos->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_eventos_update", "MODIFICAR_EVENTOS_UPDATE");
		else
			$objTemplate->set_var("modificar_eventos_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_eventos_delete", "MODIFICAR_EVENTOS_DELETE");
		else
			$objTemplate->set_var("modificar_eventos_delete", "");

		$objTemplate->parse("eventos", "EVENTOS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("eventos", "");
		$objTemplate->set_var("eventos_vacio", "");
		$objTemplate->parse("eventos_vacio_busqueda", "EVENTOS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("eventos", "");
		$objTemplate->set_var("eventos_vacio_busqueda", "");
		$objTemplate->parse("eventos_vacio", "EVENTOS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_eventos_add", "MODIFICAR_EVENTOS_ADD");
else
	$objTemplate->set_var("modificar_eventos_add", "");

/* Incluyo Paginador */
$strPage = "eventos";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Eventos ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todas las eventos del sitio.<br>Para utilizar el buscador s&oacute;lo coloque las palabras a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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