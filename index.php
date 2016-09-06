<?php

/* Incluyo los archivos necesarios */
include "includes/common.php";
include INCLUDES_DIR . "common_functions.php";
include INCLUDES_DIR . "database.php";
include INCLUDES_DIR . "mobile_detection.php";
include INCLUDES_DIR . "templates.php";
include INCLUDES_DIR . "destacados.php";
include INCLUDES_DIR . "actividades.php";
include INCLUDES_DIR . "equipos.php";
include INCLUDES_DIR . "documentos.php";
include INCLUDES_DIR . "redes.php";
include INCLUDES_DIR . "notas.php";
include INCLUDES_DIR . "coyuntura.php";
include INCLUDES_DIR . "eventos.php";
include INCLUDES_DIR . "cursos.php";

/* Defino Templates */
$objTemplate = new Template(".");
$objTemplate->set_file(array(
	"ESTRUCTURA" => TEMPLATES_DIR . "estructura.html",
	"HEADER" => TEMPLATES_DIR . "header.html",
	"PAGINA" => TEMPLATES_DIR . "index.html",
	"FOOTER" => TEMPLATES_DIR . "footer.html",
	"JS_FUNCTIONS" => TEMPLATES_DIR . "js_functions.html"
));

// Chequeo si es Mobile
$objMobileDetection = new clsMobileDetection();
$blnIsMobile = $objMobileDetection->isMobileBrowser();

$objTemplate->set_var("blnIsMobile", ($blnIsMobile) ? "true" : "false");


/*
// Levanto Destacados
$objDestacados = new clsDestacados();
$objDestacados->getDestacados(false, 1);
$blnDestacado1Chosen = false;
$blnDestacado2Chosen = false;
$blnDestacado3Chosen = false;

if ($objDestacados->intTotal){
	$objTemplate->set_block("PAGINA", "DESTACADOS_1", "destacados_1");
	$objTemplate->set_block("PAGINA", "DESTACADOS_1_BIS", "destacados_1_bis");

	$objDestacados->getDestacadosRow(0);

	$objTemplate->set_var(array(
		"strDestacadosTitulo" => $objDestacados->strTitulo,
		"strDestacadosLinkURL" => $objDestacados->strLinkURL,
		"strDestacadosImagen" => ($objDestacados->strImagen) ? $objDestacados->strImagen : IMAGEN_NO_DISPONIBLE
	));

	$objTemplate->parse("destacados_1", "DESTACADOS_1");
	$objTemplate->parse("destacados_1_bis", "DESTACADOS_1_BIS");
	$blnDestacado1Chosen = true;
}

$objDestacados->getDestacados(false, 2);
if ($objDestacados->intTotal){
	$objTemplate->set_block("PAGINA", "DESTACADOS_2", "destacados_2");
	$objTemplate->set_block("PAGINA", "DESTACADOS_3", "destacados_3");

	for ($i = 0; $i < $objDestacados->intTotal && (!$blnDestacado2Chosen || !$blnDestacado3Chosen); $i++){
		$objDestacados->getDestacadosRow($i);

		$objTemplate->set_var(array(
			"strDestacadosTitulo" => $objDestacados->strTitulo,
			"strDestacadosLinkURL" => $objDestacados->strLinkURL,
			"strDestacadosImagen" => ($objDestacados->strImagen) ? $objDestacados->strImagen : IMAGEN_NO_DISPONIBLE
		));

		if (!$blnDestacado2Chosen){
			$objTemplate->parse("destacados_2", "DESTACADOS_2");
			$blnDestacado2Chosen = true;
		}else if (!$blnDestacado3Chosen){
			$objTemplate->parse("destacados_3", "DESTACADOS_3");
			$blnDestacado3Chosen = true;
		}
	}
}

*/

/*

// Levanto Cursos
$objCursos = new clsCursos();
$objCursos->getCursosActivos();

if ($objCursos->intTotal){

	for ($i = 0; $i < $objCursos->intTotal && $i < 2; $i++){
		$objCursos->getCursosRow($i);

		$objTemplate->set_var(array(
			"intCurso" => $objCursos->intCurso,
			"intLeft" => ($i * 100) . "%",
			"strCursoTitulo" => $objCursos->strCurso,
			"strCursoDetalle" => $objCursos->strTexto,
			"strCursoFechaInicioInscripcionCorta" => str_replace("'", "\'", $objCursos->strFechaInicioInscripcionCorta),
			"strCursoFechaFinInscripcionCorta" => str_replace("'", "\'", $objCursos->strFechaFinInscripcionCorta),
			"strCursoFecha" => str_replace("'", "\'", $objCursos->strFechaCorta),
			"blnInscripcionActiva" => (($objCursos->blnInscripcionActiva) ? "true" : "false"),
			"blnInscripcionFinalizada" => (($objCursos->blnInscripcionFinalizada) ? "true" : "false"),
			"strCursoCupos1" => ($objCursos->intCuposDisponibles > 0) ? (($objCursos->intCuposDisponibles > 10) ? substr($objCursos->intCuposDisponibles, 0, 1) : 0) : 0,
			"strCursoCupos2" => ($objCursos->intCuposDisponibles > 0) ? (substr($objCursos->intCuposDisponibles, -1)) : 0
		));

		$objTemplate->set_block("PAGINA", "CURSO_" . ($i + 1), "curso_" . ($i + 1));
		$objTemplate->parse("curso_" . ($i + 1), "CURSO_" . ($i + 1));
	}
}else{
	$objTemplate->set_block("PAGINA", "NO_CURSOS", "no_cursos");
	$strCursosMensajeProximamente = $objCursos->getCursosMensaje();
	$objTemplate->set_var("CURSOS_MENSAJE_PROXIMAMENTE", showTextBreaks(HTMLEntitiesFixed($strCursosMensajeProximamente)));
	$objTemplate->parse("no_cursos", "NO_CURSOS");
}
*/


// Levanto Actividades
/*$objActividades = new clsActividades();
$objActividades->getActividades();

if ($objActividades->intTotal){
	$objTemplate->set_block("PAGINA", "ACTIVIDADES_COL_1", "actividades_col_1");
	$objTemplate->set_block("PAGINA", "ACTIVIDADES_COL_2", "actividades_col_2");

	for ($i = 0; $i < $objActividades->intTotal; $i++){
		$objActividades->getActividadesRow($i);

		$objTemplate->set_var(array(
			"strActividadTitulo" => $objActividades->strTitulo,
			"strActividadTexto" => $objActividades->strTexto,
			"strActividadImagen" => ($objActividades->strImagen) ? $objActividades->strImagen : IMAGEN_NO_DISPONIBLE
		));

		if (!($i % 2)){
			$objTemplate->parse("actividades_col_1", "ACTIVIDADES_COL_1", $i);
		}else{
			$objTemplate->parse("actividades_col_2", "ACTIVIDADES_COL_2", $i);
		}
	}
}*/

// Levanto Equipos
/*$objEquipos = new clsEquipos();
$objEquipos->getEquipos(false, 1);

if ($objEquipos->intTotal){
	$objTemplate->set_block("PAGINA", "EQUIPOS_1", "equipos_1");

	for ($i = 0; $i < $objEquipos->intTotal; $i++){
		$objEquipos->getEquiposRow($i);

		$objTemplate->set_var(array(
			"strEquipoNombre" => HTMLEntitiesFixed($objEquipos->strNombre),
			"strEquipoNombreJS" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strNombre)),
			"strEquipoCargo" => HTMLEntitiesFixed($objEquipos->strCargo),
			"strEquipoCargoJS" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strCargo)),
			"strEquipoTextoJS" => str_replace("'", "\'", showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto))),
			"strEquipoImagen" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
			"strEquipoUsuarioTwitter" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strUsuarioTwitter)),
			"strEquipoFacebookURL" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strFacebookURL)),
			"strEquipoTwitterURL" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strTwitterURL))
		));

		$objTemplate->parse("equipos_1", "EQUIPOS_1", $i);
	}
}

$objEquipos->getEquipos(false, 2);

if ($objEquipos->intTotal){
	$objTemplate->set_block("PAGINA", "EQUIPOS_2", "equipos_2");

	for ($i = 0; $i < $objEquipos->intTotal; $i++){
		$objEquipos->getEquiposRow($i);

		$objTemplate->set_var(array(
			"strEquipoNombre" => HTMLEntitiesFixed($objEquipos->strNombre),
			"strEquipoNombreJS" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strNombre)),
			"strEquipoCargo" => HTMLEntitiesFixed($objEquipos->strCargo),
			"strEquipoCargoJS" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strCargo)),
			"strEquipoTextoJS" => str_replace("'", "\'", showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto))),
			"strEquipoImagen" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
			"strEquipoUsuarioTwitter" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strUsuarioTwitter)),
			"strEquipoFacebookURL" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strFacebookURL)),
			"strEquipoTwitterURL" => str_replace("'", "\'", HTMLEntitiesFixed($objEquipos->strTwitterURL))
		));

		if ($i == 0){
			$objTemplate->set_var(array(
				"strEquiposNombrePrincipal" => HTMLEntitiesFixed($objEquipos->strNombre),
				"strEquiposCargoPrincipal" => HTMLEntitiesFixed($objEquipos->strCargo),
				"strEquiposTextoPrincipal" => showTextBreaks(HTMLEntitiesFixed($objEquipos->strTexto)),
				"strEquiposImagenPrincipal" => ($objEquipos->strImagen) ? $objEquipos->strImagen : IMAGEN_NO_DISPONIBLE,
				"strEquiposUsuarioTwitterPrincipal" => HTMLEntitiesFixed($objEquipos->strUsuarioTwitter),
				"strEquiposFacebookURLPrincipal" => HTMLEntitiesFixed($objEquipos->strFacebookURL),
				"strEquiposFacebookURLPrincipalDisplay" => (($objEquipos->strFacebookURL) ? "" : "display: none;"),
				"strEquiposTwitterURLPrincipal" => HTMLEntitiesFixed($objEquipos->strTwitterURL),
				"strEquiposTwitterURLPrincipal" => (($objEquipos->strTwitterURL) ? "" : "display: none;")
			));
		}

		$objTemplate->parse("equipos_2", "EQUIPOS_2", $i);
	}
}*/

// Levanto Documentos
/*$objDocumentos = new clsDocumentos();
$objDocumentos->getDocumentos();

if ($objDocumentos->intTotal){
	$objTemplate->set_block("PAGINA", "DOCUMENTOS", "documentos");

	for ($i = 0; $i < $objDocumentos->intTotal && $i < 100; $i++){
		$objDocumentos->getDocumentosRow($i);

		$objTemplate->set_var(array(
			"strDocumentoTitulo" => $objDocumentos->strTitulo,
			"strDocumentoTituloJS" => str_replace("'", "\'", $objDocumentos->strTitulo),
			"strAutorTextoJS" => str_replace("'", "\'", $objDocumentos->strAutor),
			"strDocumentoTextoJS" => str_replace("'", "\'", cutText($objDocumentos->strTexto, 500)),
			"strDocumentoFecha" => str_replace("'", "\'", $objDocumentos->strFecha),
			"strDocumentoImagen" => ($objDocumentos->strImagen) ? $objDocumentos->strImagen : IMAGEN_NO_DISPONIBLE,
			"strDocumentoArchivo" => $objDocumentos->strArchivo
		));

		if ($i == 0){
			$objTemplate->set_var(array(
				"strDocumentoTituloPrincipal" => $objDocumentos->strTitulo,
				"strDocumentoTextoPrincipal" => cutText($objDocumentos->strTexto, 500),
				"strDocumentoAutorPrincipal" => $objDocumentos->strAutor,
				"strDocumentoFechaPrincipal" => $objDocumentos->strFecha,
				"strDocumentoImagenPrincipal" => ($objDocumentos->strImagen) ? $objDocumentos->strImagen : IMAGEN_NO_DISPONIBLE,
				"strDocumentoArchivoPrincipal" => $objDocumentos->strArchivo
			));
		}
		$objTemplate->parse("documentos", "DOCUMENTOS", $i);
	}
}*/

// Levanto Notas
/*$objNotas = new clsNotas();
$objNotas->getNotas();

if ($objNotas->intTotal){
	$objTemplate->set_block("PAGINA", "MEDIOS", "medios");

	for ($i = 0; $i < $objNotas->intTotal && $i < 20; $i++){
		$objNotas->getNotasRow($i);

		$objTemplate->set_var(array(
			"strNotaTitulo" => $objNotas->strTitulo,
			"strNotaAutor" => $objNotas->strAutor,
			"strNotaTexto" => str_replace("'", "\'", cutText($objNotas->strTexto, 200)),
			"strNotaFecha" => str_replace("'", "\'", $objNotas->strFecha),
			"strNotaImagen" => ($objNotas->strImagen) ? $objNotas->strImagen : IMAGEN_NO_DISPONIBLE,
			"strNotaLink" => $objNotas->strLinkURL,
			"strNotaMedio" => $objNotas->strMedio
		));
		$objTemplate->parse("medios", "MEDIOS", $i);
	}
}*/

/* Levanto Redes */
$objRedes = new clsRedes();

/* Traigo un Listado de todos los Redes del Site por página */
$objRedes->getRedes();
$arrRedesProvincias = array();
$objTemplate->set_block("JS_FUNCTIONS", "RED_FEDERAL", "red_federal");
for ($i = 0; $i < $objRedes->intTotal; $i++){
	$objRedes->getRedesRow($i);
	$arrRedesProvincias[$objRedes->intProvincia] = "['" . $objRedes->strProvincia . "', '" . $objRedes->strTitulo . "', '" . $objRedes->strTexto . "', '" . $objRedes->strImagen . "']";

	$objTemplate->set_var(array(
		"RED_PROVINCIA_ID" => removeSpecialChars(str_replace(" ", "", capitalizeAll($objRedes->strProvincia))),
		"RED_PROVINCIA" => $objRedes->strProvincia,
		"RED_TITULO" => str_replace("'", "\'", $objRedes->strTitulo),
		"DES_TEXTO" => str_replace("\r\n", "<br/>", str_replace("'", "\'", $objRedes->strTexto)),
		"DES_IMAGEN" => ($objRedes->strImagen) ? $objRedes->strImagen : IMAGEN_NO_DISPONIBLE
	));
	$objTemplate->parse("red_federal", "RED_FEDERAL", $i);
}

/* Levanto Mensaje Redes */
$objRedes->getRedesMensaje();
$objRedes->getRedesMensajeRow();
$objTemplate->set_var(array(
	"strRedMensajeTexto" => showTextBreaks(HTMLEntitiesFixed($objRedes->strMensaje)),
	"strRedMensajeLinkURL" => HTMLEntitiesFixed($objRedes->strLink)
));


// Levanto Eventos
$objEventos = new clsEventos();
$objEventos->getEventos();

/*
if ($objEventos->intTotal){
	$objTemplate->set_block("PAGINA", "EVENTOS", "eventos");
	$strEventosFotos = "";

	for ($i = 0; $i < $objEventos->intTotal && $i < 15; $i++){
		$objEventos->getEventosRow($i);

		$objTemplate->set_var(array(
			"intEvento" => $objEventos->intEvento,
			"intLeft" => ($i * 100) . "%",
			"strEventoTitulo" => $objEventos->strTitulo,
			"strEventoDetalle" => $objEventos->strTexto,
			"strEventoFecha" => str_replace("'", "\'", $objEventos->strFecha),
			"strEventoImagen" => ($objEventos->strImagen) ? $objEventos->strImagen : IMAGEN_NO_DISPONIBLE,
			"strEventoArchivo" => $objEventos->strArchivo
		));
		$strEventosFotos .= ($strEventosFotos) ? "," : "";
		$strEventosFotos .= "'" . $objEventos->strImagen . "'";

		$objTemplate->parse("eventos", "EVENTOS", $i);
	}

	$objTemplate->set_var(array(
		"strEventosDisplayNext" => "none",
		"strEventosDisplayPrevious" => ($objEventos->intTotal > 1) ? "block" : "none",
		"DES_EVENTOS_FOTOS" => $strEventosFotos
	));
}
*/

$objTemplate->set_var(array(
	"intTotalEventos" => $objEventos->intTotal
));



// Seteo variables
$objTemplate->set_var(array(
	"WEB_TITLE" => $strTituloSitio,
	"WEB_TITLE_JS" => str_replace("'", "\'", $strTituloSitio),
	"WEB_DESCRIPTION" => $strDescripcionSitio,
	"WEB_DESCRIPTION_JS" => str_replace("'", "\'", $strDescripcionSitio),
	"WEB_KEYWORDS" => $strKeywordsSitio,
	"WEB_URL" => $strURLSitio,
	"WEB_FB_IMAGE" => $strURLSitio . "images/app_img.jpg",
	"NEWLINE_HTML" => ""
));

// Parseo Pagina
$objTemplate->parseArray(array(
	"JS_FUNCTIONS" => "JS_FUNCTIONS",
	"FOOTER" => "FOOTER",
	"PAGINA" => "PAGINA",
	"HEADER" => "HEADER"
));

$objTemplate->parse("out", array("ESTRUCTURA"));
$objTemplate->p("out", false);

?>