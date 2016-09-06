<?php

/****************************************************************************
* Class clsEventos: Clase de Eventos                                    *
****************************************************************************/

class clsEventos {

	var $intEvento;
	var $strTitulo;
	var $strTexto;
	var $strImagen;
	var $strArchivo;
	var $strFecha;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	/* Chequeo una Evento a subir */
	function chequearEvento($strTitulo, $strTexto, $strFecha){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strTitulo, 3, 100, "strTitulo");
		$objCheck->checkAnyText($strTexto, 10, 1000, "strTexto");
		$objCheck->checkDateSpecific($strFecha, 6, 10, "strFecha");

		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorFecha = (isset($objCheck->arrErrors["strFecha"])) ? $objCheck->arrErrors["strFecha"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagen($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen && $strImagen["name"]){
			$strImageName = md5(microtime());
			$this->strImagen = resizeImageSpecial(PATH_IMAGEN_EVENTOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_EVENTOS_GRANDE_ANCHO, IMAGEN_EVENTOS_GRANDE_ALTO, $strImageName . ".jpg");
			$this->strImagenChica = resizeImageSpecial(PATH_IMAGEN_EVENTOS_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_EVENTOS_CHICA_ANCHO, IMAGEN_EVENTOS_CHICA_ALTO, $this->strImagen);
			if (!$this->strImagen){
				$this->strImagen = $strImagenAnterior;
				$this->errorImagen = "La imagen no ha podido ser procesada";
				$this->intErrores++;
			}
		}else if ($strImagenAnterior != IMAGEN_NO_DISPONIBLE){
			$this->strImagen = $strImagenAnterior;
		}else{
			$this->strImagen = $strImagenAnterior;
			$this->errorImagen = "Debe elegir una imagen";
			$this->intErrores++;
		}
	}

	/* Inserta una Evento en la Tabla EVENTOS */
	function insertEvento($strTitulo, $strTexto, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearEvento($strTitulo, $strTexto, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_EVENTOS_LOCAL);
			if (!$this->strArchivo){
				$this->strArchivo = $strArchivoAnterior;
				$this->errorArchivo = "El archivo no ha podido ser procesado";
				$this->intErrores++;
			}
		}else if ($strArchivoAnterior){
			$this->strArchivo = $strArchivoAnterior;
		}else{
			//$this->errorArchivo = "Debe elegir un archivo válido";
			$this->errorArchivo = "";
			//$this->intErrores++;
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	EVENTOS";
		$strSQL .= "		(DES_TITULO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		DES_ARCHIVO, ";
		$strSQL .= "		FEC_FECHA, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strTitulo', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'" . $this->strImagen . "', ";
		$strSQL .= "		'" . $this->strArchivo . "', ";
		$strSQL .= "		'$strFecha', ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->intEvento = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Evento */
	function updateEvento($intEvento, $strTitulo, $strTexto, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearEvento($strTitulo, $strTexto, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_EVENTOS_LOCAL);
			if (!$this->strArchivo){
				$this->strArchivo = $strArchivoAnterior;
				$this->errorArchivo = "El archivo no ha podido ser procesado";
				$this->intErrores++;
			}
		}else if ($strArchivoAnterior){
			$this->strArchivo = $strArchivoAnterior;
		}else{
			$this->errorArchivo = "Debe elegir un archivo válido";
			$this->intErrores++;
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	EVENTOS";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		if ($strArchivo)
			$strSQL .= "			DES_ARCHIVO = '" . $this->strArchivo . "', ";
		$strSQL .= "			FEC_FECHA = '" . $strFecha . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_EVENTO = $intEvento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Evento de la Tabla Eventos */
	function deleteEvento($intEvento){

		/* Borro la tabla EVENTOS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	EVENTOS ";
		$strSQL .= "		WHERE COD_EVENTO = $intEvento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intEvento, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	EVENTOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_EVENTO = $intEvento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getEventosTotal($blnBackoffice = false, $arrEventosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(eventos.COD_EVENTO) AS NUM_EVENTOS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		EVENTOS eventos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND eventos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND eventos.FLG_HABILITADO = 'N' ";
		}

		if ($arrEventosToExclude){
			if (is_array($arrEventosToExclude)){
				$strSQL .= "		AND eventos.COD_EVENTO NOT IN (";
				for ($i = 0; $i < sizeOf($arrEventosToExclude); $i++){
					$strSQL .= "			$arrEventosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrEventosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND eventos.COD_EVENTO <> $arrEventosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (eventos.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR eventos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(eventos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(eventos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			eventos.COD_EVENTO ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getEventos($arrEventos = false, $blnBackoffice = false, $arrEventosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		eventos.COD_EVENTO, ";
		$strSQL .= "		eventos.DES_TITULO, ";
		$strSQL .= "		eventos.DES_TEXTO, ";
		$strSQL .= "		eventos.DES_IMAGEN, ";
		$strSQL .= "		eventos.DES_ARCHIVO, ";
		$strSQL .= "		DATE_FORMAT(eventos.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA, ";
		$strSQL .= "		DATE_FORMAT(eventos.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(eventos.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(eventos.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(eventos.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		eventos.FEC_FECHA_ALTA, ";
		$strSQL .= "		eventos.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		eventos.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		EVENTOS eventos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrEventos){
			if (is_array($arrEventos)){
				$strSQL .= "		AND eventos.COD_EVENTO IN (";
				for ($i = 0; $i < sizeOf($arrEventos); $i++){
					$strSQL .= "			$arrEventos[$i] ";
					$strSQL .= ($i != (sizeOf($arrEventos) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND eventos.COD_EVENTO = $arrEventos";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND eventos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND eventos.FLG_HABILITADO = 'N' ";
		}

		if ($arrEventosToExclude){
			if (is_array($arrEventosToExclude)){
				$strSQL .= "		AND eventos.COD_EVENTO NOT IN (";
				for ($i = 0; $i < sizeOf($arrEventosToExclude); $i++){
					$strSQL .= "			$arrEventosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrEventosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND eventos.COD_EVENTO <> $arrEventosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (eventos.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR eventos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(eventos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(eventos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			eventos.COD_EVENTO ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			eventos.FEC_FECHA DESC, eventos.DES_TITULO ";

		if ($intPagina){
			$strSQL .= " 	LIMIT " . (($intPagina - 1) * $intPaginado) . ", " . $intPaginado;
		}

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->arrRecord = $objQuery->Record;
		$this->intTotal = $objQuery->Row;

		return $objQuery->Row;
	}

	function getEventosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intEvento = $this->arrRecord[$intNumRecord]["COD_EVENTO"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->strArchivo = $this->arrRecord[$intNumRecord]["DES_ARCHIVO"];
			$this->strFecha = $this->arrRecord[$intNumRecord]["DES_FECHA"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["DES_FECHA_LISTADO"];
			$this->strFechaDia = $this->arrRecord[$intNumRecord]["FEC_FECHA_DIA"];
			$this->strFechaMes = $this->arrRecord[$intNumRecord]["FEC_FECHA_MES"];
			$this->strFechaAnio = $this->arrRecord[$intNumRecord]["FEC_FECHA_ANIO"];
			$this->strFechaAlta = $this->arrRecord[$intNumRecord]["FEC_FECHA_ALTA"];
			$this->strFechaModificacion = $this->arrRecord[$intNumRecord]["FEC_FECHA_MODIFICACION"];
			$this->blnHabilitado = ($this->arrRecord[$intNumRecord]["FLG_HABILITADO"] == "S") ? true : false;
			return true;
		} else
			return false;
	}

	/* Levanto los datos de la base */
	function getEventoHome(){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		n.COD_EVENTO, ";
		$strSQL .= "		n.DES_TITULO, ";
		$strSQL .= "		n.DES_TEXTO, ";
		$strSQL .= "		DATE_FORMAT(n.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA_LISTADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		EVENTOS n ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		n.FLG_HABILITADO = 'S' ";
		$strSQL .= "	ORDER BY ";
		$strSQL .= "		n.FEC_FECHA DESC ";
		$strSQL .= " 	LIMIT 1 ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->arrRecord = $objQuery->Record;
		$this->intTotal = $objQuery->Row;

		return $objQuery->Row;
	}

	function getEventoHomeRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intEvento = $this->arrRecord[$intNumRecord]["COD_EVENTO"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["DES_FECHA_LISTADO"];
			return true;
		} else
			return false;
	}

}

?>