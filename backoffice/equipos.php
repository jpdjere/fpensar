<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "equipos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 5;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "equipos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "EQUIPOS", "equipos");
$objTemplate->set_block("EQUIPOS", "MODIFICAR_EQUIPOS_UPDATE", "modificar_equipos_update");
$objTemplate->set_block("EQUIPOS", "MODIFICAR_EQUIPOS_ORDER", "modificar_equipos_order");
$objTemplate->set_block("MODIFICAR_EQUIPOS_ORDER", "ORDENAR_SUBIR", "ordenar_subir");
$objTemplate->set_block("MODIFICAR_EQUIPOS_ORDER", "ORDENAR_BAJAR", "ordenar_bajar");
$objTemplate->set_block("MODIFICAR_EQUIPOS_ORDER", "ORDENAR_SUBIR_VACIO", "ordenar_subir_vacio");
$objTemplate->set_block("MODIFICAR_EQUIPOS_ORDER", "ORDENAR_BAJAR_VACIO", "ordenar_bajar_vacio");
$objTemplate->set_block("EQUIPOS", "MODIFICAR_EQUIPOS_DELETE", "modificar_equipos_delete");
$objTemplate->set_block("EQUIPOS", "EQUIPOS_AGREGAR_HOME", "equipos_agregar_home");
$objTemplate->set_block("EQUIPOS", "EQUIPOS_BORRAR_HOME", "equipos_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_EQUIPOS_ADD", "modificar_equipos_add");
$objTemplate->set_block("PAGINA", "EQUIPOS_VACIO_BUSQUEDA", "equipos_vacio_busqueda");
$objTemplate->set_block("PAGINA", "EQUIPOS_VACIO", "equipos_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");
$intGrupo = (isset($_GET["intGrupo"])) ? intval($_GET["intGrupo"]) : 0;
if ($intGrupo < 1 || $intGrupo > 2)
	$intGrupo = 0;

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de equipos del sitio */
$objEquipos = new clsEquipos();
$intCantidadRegistros = $objEquipos->getEquiposTotal($intGrupo, true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Equipos del Site por página */
	$objEquipos->getEquipos(false, $intGrupo, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	for ($i = 0; $i < $objEquipos->intTotal; $i++){
		$objEquipos->getEquiposRow($i);
		$objTemplate->set_var(array(
			"codEquipo" => $objEquipos->intEquipo,
			"intEquipoCoded" => encodeNumber($objEquipos->intEquipo),
			"strNombre" => HTMLEntitiesFixed($objEquipos->strNombre),
			"strCargo" => HTMLEntitiesFixed($objEquipos->strCargo),
			"strTexto" => cutText(showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto)), CARACTERES_TEXTO_LISTADO * 1.5),
			"strImagen" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
			"strFechaAlta" => $objEquipos->strFechaAlta,
			"strFechaModificacion" => $objEquipos->strFechaModificacion,
			"strEstado" => ($objEquipos->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objEquipos->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objEquipos->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_equipos_update", "MODIFICAR_EQUIPOS_UPDATE");
		else
			$objTemplate->set_var("modificar_equipos_update", "");

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

			$objTemplate->parse("modificar_equipos_order", "MODIFICAR_EQUIPOS_ORDER");
		}else{
			$objTemplate->parse("modificar_equipos_order", "");
		}

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_equipos_delete", "MODIFICAR_EQUIPOS_DELETE");
		else
			$objTemplate->set_var("modificar_equipos_delete", "");

		$objTemplate->parse("equipos", "EQUIPOS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("equipos", "");
		$objTemplate->set_var("equipos_vacio", "");
		$objTemplate->parse("equipos_vacio_busqueda", "EQUIPOS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("equipos", "");
		$objTemplate->set_var("equipos_vacio_busqueda", "");
		$objTemplate->parse("equipos_vacio", "EQUIPOS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_equipos_add", "MODIFICAR_EQUIPOS_ADD");
else
	$objTemplate->set_var("modificar_equipos_add", "");

/* Incluyo Paginador */
$strPage = "equipos";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

$objTemplate->set_var(array(
	"PATH_IMAGEN_EQUIPOS" => PATH_IMAGEN_EQUIPOS,
	"PATH_IMAGEN_EQUIPOS_LOCAL" => PATH_IMAGEN_EQUIPOS_LOCAL,
	"IMAGEN_EQUIPOS_CHICA_ANCHO" => IMAGEN_EQUIPOS_CHICA_ANCHO,
	"IMAGEN_EQUIPOS_CHICA_ALTO" => IMAGEN_EQUIPOS_CHICA_ALTO,
	"IMAGEN_EQUIPOS_GRANDE_ANCHO" => IMAGEN_EQUIPOS_GRANDE_ANCHO,
	"IMAGEN_EQUIPOS_GRANDE_ALTO" => IMAGEN_EQUIPOS_GRANDE_ALTO
));

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Todos", "equipos.php", "infra");
addBackofficeMenuCarpeta($objEquipos->arrGrupos[0], "equipos.php?intGrupo=1", "infra");
addBackofficeMenuCarpeta($objEquipos->arrGrupos[1], "equipos.php?intGrupo=2", "infra");
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Equipos ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todos los integrantes de equipos del sitio.<br>Para utilizar el buscador s&oacute;lo escriba el nombe o cargo del integrante a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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