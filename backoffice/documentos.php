<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "documentos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 3;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "documentos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "DOCUMENTOS", "documentos");
$objTemplate->set_block("DOCUMENTOS", "MODIFICAR_DOCUMENTOS_UPDATE", "modificar_documentos_update");
$objTemplate->set_block("DOCUMENTOS", "MODIFICAR_DOCUMENTOS_DELETE", "modificar_documentos_delete");
$objTemplate->set_block("DOCUMENTOS", "DOCUMENTOS_AGREGAR_HOME", "documentos_agregar_home");
$objTemplate->set_block("DOCUMENTOS", "DOCUMENTOS_BORRAR_HOME", "documentos_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_DOCUMENTOS_ADD", "modificar_documentos_add");
$objTemplate->set_block("PAGINA", "DOCUMENTOS_VACIO_BUSQUEDA", "documentos_vacio_busqueda");
$objTemplate->set_block("PAGINA", "DOCUMENTOS_VACIO", "documentos_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de documentos del sitio */
$objDocumentos = new clsDocumentos();
$intCantidadRegistros = $objDocumentos->getDocumentosTotal(true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Documentos del Site por página */
	$objDocumentos->getDocumentos(false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	//for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	for ($i = 0; $i < $objDocumentos->intTotal; $i++){
		$objDocumentos->getDocumentosRow($i);
		$objTemplate->set_var(array(
			"codDocumento" => $objDocumentos->intDocumento,
			"intDocumentoCoded" => encodeNumber($objDocumentos->intDocumento),
			"strAutor" => HTMLEntitiesFixed($objDocumentos->strAutor),
			"strTitulo" => HTMLEntitiesFixed($objDocumentos->strTitulo),
			"strTexto" => cutText(showTextBreaks(HTMLEntitiesFixed($objDocumentos->strTexto)), CARACTERES_TEXTO_LISTADO * 1.5),
			"strFechaAlta" => $objDocumentos->strFechaAlta,
			"strFechaModificacion" => $objDocumentos->strFechaModificacion,
			"strFechaListado" => $objDocumentos->strFechaListado,
			"strEstado" => ($objDocumentos->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objDocumentos->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objDocumentos->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_documentos_update", "MODIFICAR_DOCUMENTOS_UPDATE");
		else
			$objTemplate->set_var("modificar_documentos_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_documentos_delete", "MODIFICAR_DOCUMENTOS_DELETE");
		else
			$objTemplate->set_var("modificar_documentos_delete", "");

		$objTemplate->parse("documentos", "DOCUMENTOS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("documentos", "");
		$objTemplate->set_var("documentos_vacio", "");
		$objTemplate->parse("documentos_vacio_busqueda", "DOCUMENTOS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("documentos", "");
		$objTemplate->set_var("documentos_vacio_busqueda", "");
		$objTemplate->parse("documentos_vacio", "DOCUMENTOS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_documentos_add", "MODIFICAR_DOCUMENTOS_ADD");
else
	$objTemplate->set_var("modificar_documentos_add", "");

/* Incluyo Paginador */
$strPage = "documentos";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "documentos_autores.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Documentos ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todas las documentos del sitio.<br>Para utilizar el buscador s&oacute;lo coloque las palabras a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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