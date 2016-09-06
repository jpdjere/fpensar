<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "destacados.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 9;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "destacados.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "DESTACADOS", "destacados");
$objTemplate->set_block("DESTACADOS", "MODIFICAR_DESTACADOS_UPDATE", "modificar_destacados_update");
$objTemplate->set_block("DESTACADOS", "MODIFICAR_DESTACADOS_ORDER", "modificar_destacados_order");
$objTemplate->set_block("MODIFICAR_DESTACADOS_ORDER", "ORDENAR_SUBIR", "ordenar_subir");
$objTemplate->set_block("MODIFICAR_DESTACADOS_ORDER", "ORDENAR_BAJAR", "ordenar_bajar");
$objTemplate->set_block("MODIFICAR_DESTACADOS_ORDER", "ORDENAR_SUBIR_VACIO", "ordenar_subir_vacio");
$objTemplate->set_block("MODIFICAR_DESTACADOS_ORDER", "ORDENAR_BAJAR_VACIO", "ordenar_bajar_vacio");
$objTemplate->set_block("DESTACADOS", "MODIFICAR_DESTACADOS_DELETE", "modificar_destacados_delete");
$objTemplate->set_block("DESTACADOS", "DESTACADOS_AGREGAR_HOME", "destacados_agregar_home");
$objTemplate->set_block("DESTACADOS", "DESTACADOS_BORRAR_HOME", "destacados_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_DESTACADOS_ADD", "modificar_destacados_add");
$objTemplate->set_block("PAGINA", "DESTACADOS_VACIO_BUSQUEDA", "destacados_vacio_busqueda");
$objTemplate->set_block("PAGINA", "DESTACADOS_VACIO", "destacados_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de destacados del sitio */
$objDestacados = new clsDestacados();
$intCantidadRegistros = $objDestacados->getDestacadosTotal(false, true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todos los Destacados del Site por página */
	$objDestacados->getDestacados(false, false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
		$objDestacados->getDestacadosRow($i);
		$objTemplate->set_var(array(
			"codDestacado" => $objDestacados->intDestacado,
			"intDestacadoCoded" => encodeNumber($objDestacados->intDestacado),
			"strTitulo" => HTMLEntitiesFixed($objDestacados->strTitulo),
			"strPosicion" => HTMLEntitiesFixed($objDestacados->strPosicion),
			"strImagen" => ($objDestacados->strImagen) ? $objDestacados->strImagen : IMAGEN_NO_DISPONIBLE,
			"strFechaAlta" => $objDestacados->strFechaAlta,
			"strFechaModificacion" => $objDestacados->strFechaModificacion,
			"strEstado" => ($objDestacados->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objDestacados->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objDestacados->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_destacados_update", "MODIFICAR_DESTACADOS_UPDATE");
		else
			$objTemplate->set_var("modificar_destacados_update", "");

		if ($blnPermisoModificacion){
			/* Parseo las posibilidades de Ordenacion hacia Arriba */
			if ($i == 0){
				$objTemplate->parse("ordenar_subir_vacio", "ORDENAR_SUBIR_VACIO");
				$objTemplate->set_var("ordenar_subir", "");
			}else{
				$objTemplate->set_var("ordenar_subir_vacio", "");
				$objTemplate->parse("ordenar_subir", "ORDENAR_SUBIR");
			}

			/* Parseo las posibilidades de Ordenacion hacia Abajo */
			if ($i == ($intCantidadRegistros - 1)){
				$objTemplate->parse("ordenar_bajar_vacio", "ORDENAR_BAJAR_VACIO");
				$objTemplate->set_var("ordenar_bajar", "");
			}else{
				$objTemplate->set_var("ordenar_bajar_vacio", "");
				$objTemplate->parse("ordenar_bajar", "ORDENAR_BAJAR");
			}

			$objTemplate->parse("modificar_destacados_order", "MODIFICAR_DESTACADOS_ORDER");
		}else{
			$objTemplate->parse("modificar_destacados_order", "");
		}

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_destacados_delete", "MODIFICAR_DESTACADOS_DELETE");
		else
			$objTemplate->set_var("modificar_destacados_delete", "");

		$objTemplate->parse("destacados", "DESTACADOS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("destacados", "");
		$objTemplate->set_var("destacados_vacio", "");
		$objTemplate->parse("destacados_vacio_busqueda", "DESTACADOS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("destacados", "");
		$objTemplate->set_var("destacados_vacio_busqueda", "");
		$objTemplate->parse("destacados_vacio", "DESTACADOS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_destacados_add", "MODIFICAR_DESTACADOS_ADD");
else
	$objTemplate->set_var("modificar_destacados_add", "");

/* Incluyo Paginador */
$strPage = "destacados";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var(array(
	"PATH_IMAGEN_DESTACADOS" => PATH_IMAGEN_DESTACADOS,
	"PATH_IMAGEN_DESTACADOS_LOCAL" => PATH_IMAGEN_DESTACADOS_LOCAL,
	"IMAGEN_DESTACADOS_ANCHO" => IMAGEN_DESTACADOS_ANCHO,
	"IMAGEN_DESTACADOS_ALTO" => IMAGEN_DESTACADOS_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Destacados ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todos los destacados del sitio.<br>Para utilizar el buscador s&oacute;lo escriba las palabras a encontrar en la destacado y presione <i>Iniciar B&uacute;squeda</i>.");

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