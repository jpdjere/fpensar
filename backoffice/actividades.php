<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "actividades.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 6;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "actividades.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "ACTIVIDADES", "actividades");
$objTemplate->set_block("ACTIVIDADES", "MODIFICAR_ACTIVIDADES_UPDATE", "modificar_actividades_update");
$objTemplate->set_block("ACTIVIDADES", "MODIFICAR_ACTIVIDADES_ORDER", "modificar_actividades_order");
$objTemplate->set_block("MODIFICAR_ACTIVIDADES_ORDER", "ORDENAR_SUBIR", "ordenar_subir");
$objTemplate->set_block("MODIFICAR_ACTIVIDADES_ORDER", "ORDENAR_BAJAR", "ordenar_bajar");
$objTemplate->set_block("MODIFICAR_ACTIVIDADES_ORDER", "ORDENAR_SUBIR_VACIO", "ordenar_subir_vacio");
$objTemplate->set_block("MODIFICAR_ACTIVIDADES_ORDER", "ORDENAR_BAJAR_VACIO", "ordenar_bajar_vacio");
$objTemplate->set_block("ACTIVIDADES", "MODIFICAR_ACTIVIDADES_DELETE", "modificar_actividades_delete");
$objTemplate->set_block("ACTIVIDADES", "ACTIVIDADES_AGREGAR_HOME", "actividades_agregar_home");
$objTemplate->set_block("ACTIVIDADES", "ACTIVIDADES_BORRAR_HOME", "actividades_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_ACTIVIDADES_ADD", "modificar_actividades_add");
$objTemplate->set_block("PAGINA", "ACTIVIDADES_VACIO_BUSQUEDA", "actividades_vacio_busqueda");
$objTemplate->set_block("PAGINA", "ACTIVIDADES_VACIO", "actividades_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de actividades del sitio */
$objActividades = new clsActividades();
$intCantidadRegistros = $objActividades->getActividadesTotal(true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Actividades del Site por página */
	$objActividades->getActividades(false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
		$objActividades->getActividadesRow($i);
		$objTemplate->set_var(array(
			"codActividad" => $objActividades->intActividad,
			"intActividadCoded" => encodeNumber($objActividades->intActividad),
			"strTitulo" => HTMLEntitiesFixed($objActividades->strTitulo),
			"strTexto" => cutText(showTextBreaks(HTMLEntitiesFixed($objActividades->strTexto)), CARACTERES_TEXTO_LISTADO * 1.5),
			"strImagen" => ($objActividades->strImagen) ? $objActividades->strImagen : IMAGEN_NO_DISPONIBLE,
			"strFechaAlta" => $objActividades->strFechaAlta,
			"strFechaModificacion" => $objActividades->strFechaModificacion,
			"strFechaListado" => $objActividades->strFechaListado,
			"strEstado" => ($objActividades->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objActividades->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objActividades->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_actividades_update", "MODIFICAR_ACTIVIDADES_UPDATE");
		else
			$objTemplate->set_var("modificar_actividades_update", "");

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

			$objTemplate->parse("modificar_actividades_order", "MODIFICAR_ACTIVIDADES_ORDER");
		}else{
			$objTemplate->parse("modificar_actividades_order", "");
		}

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_actividades_delete", "MODIFICAR_ACTIVIDADES_DELETE");
		else
			$objTemplate->set_var("modificar_actividades_delete", "");

		$objTemplate->parse("actividades", "ACTIVIDADES", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("actividades", "");
		$objTemplate->set_var("actividades_vacio", "");
		$objTemplate->parse("actividades_vacio_busqueda", "ACTIVIDADES_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("actividades", "");
		$objTemplate->set_var("actividades_vacio_busqueda", "");
		$objTemplate->parse("actividades_vacio", "ACTIVIDADES_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_actividades_add", "MODIFICAR_ACTIVIDADES_ADD");
else
	$objTemplate->set_var("modificar_actividades_add", "");

/* Incluyo Paginador */
$strPage = "actividades";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var(array(
	"PATH_IMAGEN_ACTIVIDADES" => PATH_IMAGEN_ACTIVIDADES,
	"PATH_IMAGEN_ACTIVIDADES_LOCAL" => PATH_IMAGEN_ACTIVIDADES_LOCAL,
	"IMAGEN_ACTIVIDADES_ANCHO" => IMAGEN_ACTIVIDADES_ANCHO,
	"IMAGEN_ACTIVIDADES_ALTO" => IMAGEN_ACTIVIDADES_ALTO,
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Actividades ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todas las actividades del sitio.<br>Para utilizar el buscador s&oacute;lo escriba las palabras a encontrar en la actividad y presione <i>Iniciar B&uacute;squeda</i>.");

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