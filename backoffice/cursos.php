<?php

/* Incluyo los archivos necesarios */
include "../includes/common.php";
include INCLUDES_BACKOFFICE_DIR . "common_functions.php";
include INCLUDES_BACKOFFICE_DIR . "templates.php";
include INCLUDES_BACKOFFICE_DIR . "database.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_usuarios.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_perfiles.php";
include INCLUDES_BACKOFFICE_DIR . "backoffice_secciones.php";
include INCLUDES_BACKOFFICE_DIR . "cursos.php";

// Chequeo permisos y perfiles
$intSeccionBackOffice = 11;
$intBackofficePermisoPagina = PERMISO_SOLO_LECTURA;
include_once("include_permisos.php");

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"MENU" => TEMPLATES_DIR . "menu.html",
	"ENCABEZADO" => TEMPLATES_DIR . "encabezado.html",
	"PAGINA" => TEMPLATES_DIR . "cursos.html",
	"PAGINADOR" => TEMPLATES_DIR . "paginador.html",
	"OPCIONES" => TEMPLATES_DIR . "opciones.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html"
));

$objTemplate->set_block("PAGINA", "CURSOS", "cursos");
$objTemplate->set_block("CURSOS", "MODIFICAR_CURSOS_UPDATE", "modificar_cursos_update");
$objTemplate->set_block("CURSOS", "MODIFICAR_CURSOS_DELETE", "modificar_cursos_delete");
$objTemplate->set_block("CURSOS", "CURSOS_AGREGAR_HOME", "cursos_agregar_home");
$objTemplate->set_block("CURSOS", "CURSOS_BORRAR_HOME", "cursos_borrar_home");
$objTemplate->set_block("PAGINA", "MODIFICAR_CURSOS_ADD", "modificar_cursos_add");
$objTemplate->set_block("PAGINA", "CURSOS_VACIO_BUSQUEDA", "cursos_vacio_busqueda");
$objTemplate->set_block("PAGINA", "CURSOS_VACIO", "cursos_vacio");

/* Si hizo una busqueda, la traigo */
$strBusqueda = (isset($_GET["txtBusqueda"])) ? $_GET["txtBusqueda"] : ((isset($_POST["txtBusqueda"])) ? $_POST["txtBusqueda"] : "");

/* Levanto la pagina a mostrar */
$intPagina = (isset($_GET["intPagina"])) ? intval($_GET["intPagina"]) : 0;
if (!$intPagina || $intPagina <= 0)
	$intPagina = 1;

/* Traigo la cantidad de cursos del sitio */
$objCursos = new clsCursos();
$intCantidadRegistros = $objCursos->getCursosTotal(true, false, $strBusqueda, false, false);

if ($intCantidadRegistros){

	/* Traigo un Listado de todas las Cursos del Site por página */
	$objCursos->getCursos(false, true, false, $strBusqueda, false, false, $intPagina, $intPaginado);

	//for ($i = ($intPagina - 1) * $intPaginado; ($i < $intCantidadRegistros) && ($i < ($intPagina * $intPaginado)); $i++){
	for ($i = 0; $i < $objCursos->intTotal; $i++){
		$objCursos->getCursosRow($i);
		$objTemplate->set_var(array(
			"codCurso" => $objCursos->intCurso,
			"intCursoCoded" => encodeNumber($objCursos->intCurso),
			"strCurso" => HTMLEntitiesFixed($objCursos->strCurso),
			"strTexto" => cutText(showTextBreaks(HTMLEntitiesFixed($objCursos->strTexto)), CARACTERES_TEXTO_LISTADO * 1.5),
			"strFechaInicioInscripcion" => $objCursos->strFechaInicioInscripcion,
			"strFechaFinInscripcion" => $objCursos->strFechaFinInscripcion,
			"strFecha" => $objCursos->strFecha,
			"intCupos" => $objCursos->intCupos - $objCursos->intInscriptos,
			"strFechaAlta" => $objCursos->strFechaAlta,
			"strFechaModificacion" => $objCursos->strFechaModificacion,
			"strFechaListado" => $objCursos->strFechaListado,
			"strEstado" => ($objCursos->blnHabilitado) ? "Habilitado" : "Deshabilitado",
			"estadoIcono" => ($objCursos->blnHabilitado) ? "" : "_on",
			"estadoAlt" => ($objCursos->blnHabilitado) ? "Deshabilitar" : "Habilitar"
		));

		if ($strBusqueda)
			$objTemplate->set_var("txtBusqueda", $strBusqueda);

		if ($blnPermisoModificacion)
			$objTemplate->parse("modificar_cursos_update", "MODIFICAR_CURSOS_UPDATE");
		else
			$objTemplate->set_var("modificar_cursos_update", "");

		if ($blnPermisoBaja)
			$objTemplate->parse("modificar_cursos_delete", "MODIFICAR_CURSOS_DELETE");
		else
			$objTemplate->set_var("modificar_cursos_delete", "");

		$objTemplate->parse("cursos", "CURSOS", true);
	}
}else{
	if ($strBusqueda){
		$objTemplate->set_var("txtBusqueda", $strBusqueda);
		$objTemplate->set_var("cursos", "");
		$objTemplate->set_var("cursos_vacio", "");
		$objTemplate->parse("cursos_vacio_busqueda", "CURSOS_VACIO_BUSQUEDA");
	}else{
		$objTemplate->set_var("cursos", "");
		$objTemplate->set_var("cursos_vacio_busqueda", "");
		$objTemplate->parse("cursos_vacio", "CURSOS_VACIO", true);
	}
}

if ($blnPermisoAlta)
	$objTemplate->parse("modificar_cursos_add", "MODIFICAR_CURSOS_ADD");
else
	$objTemplate->set_var("modificar_cursos_add", "");

/* Incluyo Paginador */
$strPage = "cursos";
$strParameters = "&txtBusqueda=" . $strBusqueda;
include INCLUDES_BACKOFFICE_DIR . "paginador.php";

/* Muestro los items del Menu a los que el usuario tiene acceso */
initBackofficeMenu();
addBackofficeMenuCarpeta("Mensaje Próximamente", "cursos_mensaje.php", "users");
setBackofficeMenu();
setBackOfficeEncabezado("Listado de Cursos ", "(" . $intCantidadRegistros . ")", "Desde aqu&iacute; podr&aacute; administrar todos los cursos del sitio.<br>Para utilizar el buscador s&oacute;lo coloque las palabras a buscar y presione <i>Iniciar B&uacute;squeda</i>.");

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