<?php

/****************************************************************************
* Class clsDestacados: Clase de Destacados                                    *
****************************************************************************/

class clsDestacados {

	var $intDestacado;
	var $strTitulo;
	var $intPosicion;
	var $strLink;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	/* Chequeo una Destacado a subir */
	function chequearDestacado($strTitulo, $intPosicion, $strLinkURL){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strTitulo, 3, 100, "strTitulo");
		$objCheck->checkURL($strLinkURL, 10, 255, "strLinkURL");
		$objCheck->checkCombo($intPosicion, "intPosicion");

		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorLinkURL = (isset($objCheck->arrErrors["strLinkURL"])) ? $objCheck->arrErrors["strLinkURL"] : "";
		$this->errorPosicion = (isset($objCheck->arrErrors["intPosicion"])) ? $objCheck->arrErrors["intPosicion"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	/* Inserta una Destacado en la Tabla DESTACADOS */
	function insertDestacado($strTitulo, $intPosicion, $strLinkURL, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearDestacado($strTitulo, $intPosicion, $strLinkURL);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageHeight(PATH_IMAGEN_DESTACADOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_DESTACADOS_ANCHO, IMAGEN_DESTACADOS_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strLinkURL = stringToSQL($strLinkURL);
		$intPosicion = intval($intPosicion);

		/* Me fijo el último orden de esta tabla */
		$strSQL = " SELECT ";
		$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		DESTACADOS ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = ($objQuery->Row) ? ($objQuery->Record[0]["NUM_ORDEN"] + 1) : 1;

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	DESTACADOS";
		$strSQL .= "		(DES_TITULO, ";
		$strSQL .= "		COD_POSICION, ";
		$strSQL .= "		DES_URL, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		NUM_ORDEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strTitulo', ";
		$strSQL .= "		$intPosicion, ";
		$strSQL .= "		'$strLinkURL', ";
		$strSQL .= "		'" . $this->strImagen . "', ";
		$strSQL .= "		$intOrden, ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

		$this->intDestacado = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Destacado */
	function updateDestacado($intDestacado, $strTitulo, $intPosicion, $strLinkURL, $strImagen, $strImagenAnterior, $blnHabilitado){
		$intDestacado = intval($intDestacado);

		$this->chequearDestacado($strTitulo, $intPosicion, $strLinkURL);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageHeight(PATH_IMAGEN_DESTACADOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_DESTACADOS_ANCHO, IMAGEN_DESTACADOS_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strLinkURL = stringToSQL($strLinkURL);
		$intPosicion = intval($intPosicion);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	DESTACADOS";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			COD_POSICION = $intPosicion, ";
		$strSQL .= "			DES_URL = '$strLinkURL', ";
		$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_DESTACADO = $intDestacado";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Destacado de la Tabla Destacados */
	function deleteDestacado($intDestacado){
		$intDestacado = intval($intDestacado);

		/* Borro la tabla DESTACADOS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	DESTACADOS ";
		$strSQL .= "		WHERE COD_DESTACADO = $intDestacado";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intDestacado, $blnQualify = false){
		$intDestacado = intval($intDestacado);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	DESTACADOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_DESTACADO = $intDestacado";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	// Ordenar Destacados
	function orderDestacado($intDestacado, $blnOrden){
		$intDestacado = intval($intDestacado);
		if (!$intDestacado)
			return false;

		/* Obtengo el orden de la destacado actual */
		$strSQL = " SELECT ";
		$strSQL .= " 		NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		DESTACADOS ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_DESTACADO = $intDestacado ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = $objQuery->Record[0]["NUM_ORDEN"];

		/* Obtengo el orden de la destacado a actualizar */
		$strSQL = " SELECT ";
		if ($blnOrden > 1 || $blnOrden == -1)
			$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		else
			$strSQL .= " 		MIN(NUM_ORDEN) AS NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		DESTACADOS ";
		$strSQL .= "	WHERE ";
		if ($blnOrden == -1 || $blnOrden == -2)
			$strSQL .= " 		NUM_ORDEN < $intOrden ";
		else if ($blnOrden == 1 || $blnOrden == 2)
			$strSQL .= " 		NUM_ORDEN > $intOrden ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);
		$intOrdenNuevo = $objQuery->Record[0]["NUM_ORDEN"];

		if ($blnOrden == 1 || $blnOrden == -1){
			/* Updeteo el nuevo orden de la destacado a actualizar */
			$strSQL = " UPDATE ";
			$strSQL .= " 		DESTACADOS ";
			$strSQL .= " 	SET ";
			$strSQL .= " 		NUM_ORDEN = $intOrden ";
			$strSQL .= "	WHERE ";
			$strSQL .= "		NUM_ORDEN = $intOrdenNuevo ";
			$objQuery->query($strSQL);
		}else{
			if ($blnOrden == 2){
				for ($i = ($intOrden + 1); $i <= $intOrdenNuevo; $i++){
					/* Updeteo todas las destacados a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		DESTACADOS ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i - 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}else if ($blnOrden == -2){
				for ($i = ($intOrden - 1); $i >= $intOrdenNuevo; $i--){
					/* Updeteo todas las destacados a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		DESTACADOS ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i + 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}
		}

		/* Updeteo el nuevo orden de la destacado a ordenar */
		$strSQL = " UPDATE ";
		$strSQL .= " 		DESTACADOS ";
		$strSQL .= " 	SET ";
		$strSQL .= " 		NUM_ORDEN = $intOrdenNuevo ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_DESTACADO = $intDestacado ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

	}

	/* Levanto los datos de la base */
	function getDestacadosTotal($intPosicion = false, $blnBackoffice = false, $arrDestacadosToExclude = false, $intPosicionBusqueda = false){
		$intPosicion = intval($intPosicion);
		$intPosicionBusqueda = stringToSQL($intPosicionBusqueda);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(destacados.COD_DESTACADO) AS NUM_DESTACADOS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DESTACADOS destacados ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intPosicion){
			$strSQL .= "		AND destacados.COD_POSICION = $intPosicion ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND destacados.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND destacados.FLG_HABILITADO = 'N' ";
		}

		if ($arrDestacadosToExclude){
			if (is_array($arrDestacadosToExclude)){
				$strSQL .= "		AND destacados.COD_DESTACADO NOT IN (";
				for ($i = 0; $i < sizeOf($arrDestacadosToExclude); $i++){
					$strSQL .= "			" . intval($arrDestacadosToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrDestacadosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND destacados.COD_DESTACADO <> " . intval($arrDestacadosToExclude) . " ";
		}

		if ($intPosicionBusqueda){
			$strSQL .= "		AND (destacados.DES_TITULO LIKE '%" . $intPosicionBusqueda . "%' ";
			$strSQL .= "		OR destacados.COD_POSICION LIKE '%" . $intPosicionBusqueda . "%') ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			destacados.COD_DESTACADO ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getDestacados($arrDestacados = false, $intPosicion = false, $blnBackoffice = false, $arrDestacadosToExclude = false, $intPosicionBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){
		$intPosicion = intval($intPosicion);

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		destacados.COD_DESTACADO, ";
		$strSQL .= "		destacados.DES_TITULO, ";
		$strSQL .= "		destacados.COD_POSICION, ";
		$strSQL .= "		posiciones.DES_POSICION, ";
		$strSQL .= "		destacados.DES_URL, ";
		$strSQL .= "		destacados.DES_IMAGEN, ";
		$strSQL .= "		DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		destacados.FEC_FECHA_ALTA, ";
		$strSQL .= "		destacados.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		destacados.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DESTACADOS destacados, ";
		$strSQL .= "		DESTACADOS_POSICIONES posiciones ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		destacados.COD_POSICION = posiciones.COD_POSICION ";

		if ($arrDestacados){
			if (is_array($arrDestacados)){
				$strSQL .= "		AND destacados.COD_DESTACADO IN (";
				for ($i = 0; $i < sizeOf($arrDestacados); $i++){
					$strSQL .= "			$arrDestacados[$i] ";
					$strSQL .= ($i != (sizeOf($arrDestacados) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND destacados.COD_DESTACADO = $arrDestacados";
		}

		if ($intPosicion){
			$strSQL .= "		AND destacados.COD_POSICION = $intPosicion ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND destacados.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND destacados.FLG_HABILITADO = 'N' ";
		}

		if ($arrDestacadosToExclude){
			if (is_array($arrDestacadosToExclude)){
				$strSQL .= "		AND destacados.COD_DESTACADO NOT IN (";
				for ($i = 0; $i < sizeOf($arrDestacadosToExclude); $i++){
					$strSQL .= "			$arrDestacadosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrDestacadosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND destacados.COD_DESTACADO <> $arrDestacadosToExclude";
		}

		if ($intPosicionBusqueda){
			$strSQL .= "		AND (destacados.DES_TITULO LIKE '%" . $intPosicionBusqueda . "%' ";
			$strSQL .= "		OR destacados.COD_POSICION LIKE '%" . $intPosicionBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(destacados.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			destacados.COD_DESTACADO ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			destacados.NUM_ORDEN DESC, destacados.FEC_FECHA_ALTA DESC ";

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

	function getDestacadosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intDestacado = $this->arrRecord[$intNumRecord]["COD_DESTACADO"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->intPosicion = $this->arrRecord[$intNumRecord]["COD_POSICION"];
			$this->strPosicion = $this->arrRecord[$intNumRecord]["DES_POSICION"];
			$this->strLinkURL = $this->arrRecord[$intNumRecord]["DES_URL"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["FEC_FECHA_LISTADO"];
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

	/* Levanto los datos de las posiciones */
	function getPosiciones($intPosicion = false){
		$intPosicion = intval($intPosicion);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		d.COD_POSICION, ";
		$strSQL .= "		d.DES_POSICION ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DESTACADOS_POSICIONES d ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intPosicion){
			$strSQL .= "		AND d.COD_POSICION = $intPosicion ";
		}

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			d.DES_POSICION ASC ";

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

	function getPosicionesRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intPosicion = $this->arrRecord[$intNumRecord]["COD_POSICION"];
			$this->strPosicion = $this->arrRecord[$intNumRecord]["DES_POSICION"];
			return true;
		} else
			return false;
	}

}

?>