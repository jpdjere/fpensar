<?php

/* Genero paginador */
if (isset($intCantidadRegistros) && $intCantidadRegistros > $intPaginado){
	$objTemplate->set_block("PAGINADOR", "PAGINADO", "paginado");
	$objTemplate->set_block("PAGINADO", "NUMEROS_PAGINA", "numeros_pagina");
	$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_NORMAL", "pagina_normal");
	$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_ACTIVA", "pagina_activa");

	/* Seteo el nombre de la pagina y los parametros adicionales en el paginador */
	$objTemplate->set_var("strPagina", $strPage);
	$objTemplate->set_var("strParameters", (isset($strParameters)) ? $strParameters : "");

	$intCorteNumeros = 10;
	$intMaximoPaginado = (intval($intCantidadRegistros / $intPaginado) < $intCorteNumeros) ? intval($intCantidadRegistros / $intPaginado) : $intCorteNumeros;
	$intNumeroMinimo = ($intPagina > intval($intCorteNumeros / 2)) ? ((($intPaginado * ($intPagina + intval($intCorteNumeros / 2))) > $intCantidadRegistros) ? ($intCantidadRegistros - ($intMaximoPaginado * $intPaginado)) : ($intPaginado * ($intPagina - intval($intCorteNumeros / 2)))) : 0;
	$intNumeroMaximo = ($intPagina > intval($intCorteNumeros / 2)) ? ($intPaginado * ($intPagina + intval($intCorteNumeros / 2))) : ($intCorteNumeros * $intPaginado);

	$intCantidadRegistros -= (($intCantidadRegistros % $intPaginado == 0) ? 1 : 0);
	for ($i = $intNumeroMinimo; $i <= $intCantidadRegistros && $i <= $intNumeroMaximo; $i += $intPaginado){
		$intContador = intval($i / $intPaginado) + 1;
		$objTemplate->set_var("intPagina", $intContador);
		($intContador == $intPagina) ? $objTemplate->parse("pagina_normal", "PAGINA_ACTIVA", true) : $objTemplate->parse("pagina_normal", "PAGINA_NORMAL", true);
	}

	if ($intPagina > 1){
		$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_ANTERIOR", "pagina_anterior");
		$objTemplate->set_var("intPagina", $intPagina - 1);
		$objTemplate->parse("pagina_anterior", "PAGINA_ANTERIOR");
		$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_INICIO", "pagina_inicio");
		$objTemplate->set_var("intPagina", 1);
		$objTemplate->parse("pagina_inicio", "PAGINA_INICIO");
	}
	if ($intPagina < ($intCantidadRegistros / $intPaginado)){
		$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_SIGUIENTE", "pagina_siguiente");
		$objTemplate->set_var("intPagina", $intPagina + 1);
		$objTemplate->parse("pagina_siguiente", "PAGINA_SIGUIENTE");
		$intNumeroPaginaFinal = intval($intCantidadRegistros / $intPaginado) + ((($intCantidadRegistros / $intPaginado) - intval($intCantidadRegistros / $intPaginado)) ? 1 : 0);
		$objTemplate->set_block("NUMEROS_PAGINA", "PAGINA_FINAL", "pagina_final");
		$objTemplate->set_var("intPagina", $intNumeroPaginaFinal);
		$objTemplate->parse("pagina_final", "PAGINA_FINAL");
	}

	$objTemplate->parse("numeros_pagina", "NUMEROS_PAGINA");
	$objTemplate->parse("paginado","PAGINADO");
} else
	$objTemplate->set_var("numeros_pagina", "");

?>