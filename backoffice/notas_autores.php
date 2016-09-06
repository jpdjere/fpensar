<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "notas.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 7;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "notas_autores.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "NOTAS", "notas");
$objTemplate->set_block("NOTAS", "MODIFICAR_NOTAS_UPDATE", "modificar_notas_autores_update");
$objTemplate->set_block("NOTAS", "MODIFICAR_NOTAS_DELETE", "modificar_notas_autores_delete");
$objTemplate->set_block("NOTAS", "NOTAS_AGREGAR_HOME", "notas_autores_agregar_home");
$objTemplate->set_block("NOTAS", "NOTAS_BORRAR_HOME", "notas_autores_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_NOTAS_ADD", "modificar_notas_autores_add");
$objTemplate->set_block("PAGINA", "NOTAS_VACIO_BUSQUEDA", "notas_autores_vacio_busqueda");
$objTemplate->set_block("PAGINA", "NOTAS_VACIO", "notas_autores_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de notas del sitio */
$objNotas = new clsNotas();
$intCantidadRegistros = $objNotas->getAutoresTotal(true, false, $strBusqueda);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Notas del Site por página */
	$objNotas->getAutores(false, true, false, $strBusqueda, $intPagina, $intPaginado);

	//for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	for ($i = 0; $i < $objNotas->intTotal; $i++){
		$objNotas->getAutoresRow($i);
		$objTemplate->set_var(array(
			"codAutor" => $objNotas->intAutor,
			"strAutor" => HTMLEntitiesFixed($objNotas->strAutor),
			"strImagen" => ($objNotas->strImagen) ? $objNotas->strImagen : IMAGEN_NO_DISPONIBLE,
			"strFechaAlta" => $objNotas->strFechaAlta,
			"strFechaModificacion" => $objNotas->strFechaModificacion,
			"strEstado" => ($objNotas->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objNotas->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objNotas->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_notas_autores_update", "MODIFICAR_NOTAS_UPDATE");
		else
			$objTemplate->set_var("modificar_notas_autores_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_notas_autores_delete", "MODIFICAR_NOTAS_DELETE");
		else
			$objTemplate->set_var("modificar_notas_autores_delete", "");

		$objTemplate->parse("notas", "NOTAS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("notas", "");
		$objTemplate->set_var("notas_autores_vacio", "");
		$objTemplate->parse("notas_autores_vacio_busqueda", "NOTAS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("notas", "");
		$objTemplate->set_var("notas_autores_vacio_busqueda", "");
		$objTemplate->parse("notas_autores_vacio", "NOTAS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_notas_autores_add", "MODIFICAR_NOTAS_ADD");
else
	$objTemplate->set_var("modificar_notas_autores_add", "");

$objTemplate->set_var(array(
	"PATH_IMAGEN_NOTAS_AUTORES" => PATH_IMAGEN_NOTAS_AUTORES,
	"PATH_IMAGEN_NOTAS_AUTORES_LOCAL" => PATH_IMAGEN_NOTAS_AUTORES_LOCAL,
	"IMAGEN_NOTAS_AUTORES_CHICA_ANCHO" => IMAGEN_NOTAS_AUTORES_CHICA_ANCHO,
	"IMAGEN_NOTAS_AUTORES_CHICA_ALTO" => IMAGEN_NOTAS_AUTORES_CHICA_ALTO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO" => IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO,
	"IMAGEN_NOTAS_AUTORES_GRANDE_ALTO" => IMAGEN_NOTAS_AUTORES_GRANDE_ALTO
));

/* Incluyo Paginador */
$strPage = "notas";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Autores", "#", "users");
addBackofficeMenuCarpeta("Medios", "notas_medios.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Autores de Notas ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todos los autores de los notas del sitio.<br>Para utilizar el buscador s&oacute;lo coloque las palabras a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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