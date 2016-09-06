<?php

/****************************************************************************
* Class clsRedes: Clase de Redes                                    *
****************************************************************************/

class clsRedes {

	var $intRed;
	var $strTitulo;
	var $intProvincia;
	var $strTexto;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	/* Chequeo una Red a subir */
	function chequearRed($strTitulo, $intProvincia, $strTexto){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strTitulo, 3, 100, "strTitulo");
		$objCheck->checkAnyText($strTexto, 10, 1000, "strTexto");
		$objCheck->checkCombo($intProvincia, "intProvincia");

		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorProvincia = (isset($objCheck->arrErrors["intProvincia"])) ? $objCheck->arrErrors["intProvincia"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	/* Inserta una Red en la Tabla REDES */
	function insertRed($strTitulo, $intProvincia, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearRed($strTitulo, $intProvincia, $strTexto);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageWidth(PATH_IMAGEN_REDES_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_REDES_ANCHO, IMAGEN_REDES_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL($strTexto);
		$intProvincia = intval($intProvincia);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	REDES";
		$strSQL .= "		(DES_TITULO, ";
		$strSQL .= "		COD_PROVINCIA, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strTitulo', ";
		$strSQL .= "		$intProvincia, ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'" . $this->strImagen . "', ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->intRed = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Red */
	function updateRed($intRed, $strTitulo, $intProvincia, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado){
		$intRed = intval($intRed);

		$this->chequearRed($strTitulo, $intProvincia, $strTexto);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageWidth(PATH_IMAGEN_REDES_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_REDES_ANCHO, IMAGEN_REDES_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL($strTexto);
		$intProvincia = intval($intProvincia);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	REDES";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			COD_PROVINCIA = $intProvincia, ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_RED = $intRed";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Red de la Tabla Redes */
	function deleteRed($intRed){
		$intRed = intval($intRed);

		/* Borro la tabla REDES */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	REDES ";
		$strSQL .= "		WHERE COD_RED = $intRed";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intRed, $blnQualify = false){
		$intRed = intval($intRed);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	REDES ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_RED = $intRed";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getRedesTotal($intProvincia = false, $blnBackoffice = false, $arrRedesToExclude = false, $intProvinciaBusqueda = false){
		$intProvincia = intval($intProvincia);
		$intProvinciaBusqueda = stringToSQL($intProvinciaBusqueda);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(redes.COD_RED) AS NUM_REDES ";
		$strSQL .= "	FROM ";
		$strSQL .= "		REDES redes ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intProvincia){
			$strSQL .= "		AND redes.COD_PROVINCIA = $intProvincia ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND redes.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND redes.FLG_HABILITADO = 'N' ";
		}

		if ($arrRedesToExclude){
			if (is_array($arrRedesToExclude)){
				$strSQL .= "		AND redes.COD_RED NOT IN (";
				for ($i = 0; $i < sizeOf($arrRedesToExclude); $i++){
					$strSQL .= "			" . intval($arrRedesToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrRedesToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND redes.COD_RED <> " . intval($arrRedesToExclude) . " ";
		}

		if ($intProvinciaBusqueda){
			$strSQL .= "		AND (redes.DES_TITULO LIKE '%" . $intProvinciaBusqueda . "%' ";
			$strSQL .= "		OR redes.COD_PROVINCIA LIKE '%" . $intProvinciaBusqueda . "%') ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			redes.COD_RED ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getRedes($arrRedes = false, $intProvincia = false, $blnBackoffice = false, $arrRedesToExclude = false, $intProvinciaBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){
		$intProvincia = intval($intProvincia);

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		redes.COD_RED, ";
		$strSQL .= "		redes.DES_TITULO, ";
		$strSQL .= "		redes.COD_PROVINCIA, ";
		$strSQL .= "		provincias.DES_PROVINCIA, ";
		$strSQL .= "		redes.DES_TEXTO, ";
		$strSQL .= "		redes.DES_IMAGEN, ";
		$strSQL .= "		DATE_FORMAT(redes.FEC_FECHA_ALTA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(redes.FEC_FECHA_ALTA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(redes.FEC_FECHA_ALTA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(redes.FEC_FECHA_ALTA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		redes.FEC_FECHA_ALTA, ";
		$strSQL .= "		redes.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		redes.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		REDES redes, ";
		$strSQL .= "		PROVINCIAS provincias ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		redes.COD_PROVINCIA = provincias.COD_PROVINCIA ";

		if ($arrRedes){
			if (is_array($arrRedes)){
				$strSQL .= "		AND redes.COD_RED IN (";
				for ($i = 0; $i < sizeOf($arrRedes); $i++){
					$strSQL .= "			$arrRedes[$i] ";
					$strSQL .= ($i != (sizeOf($arrRedes) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND redes.COD_RED = $arrRedes";
		}

		if ($intProvincia){
			$strSQL .= "		AND redes.COD_PROVINCIA = $intProvincia ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND redes.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND redes.FLG_HABILITADO = 'N' ";
		}

		if ($arrRedesToExclude){
			if (is_array($arrRedesToExclude)){
				$strSQL .= "		AND redes.COD_RED NOT IN (";
				for ($i = 0; $i < sizeOf($arrRedesToExclude); $i++){
					$strSQL .= "			$arrRedesToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrRedesToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND redes.COD_RED <> $arrRedesToExclude";
		}

		if ($intProvinciaBusqueda){
			$strSQL .= "		AND (redes.DES_TITULO LIKE '%" . $intProvinciaBusqueda . "%' ";
			$strSQL .= "		OR redes.COD_PROVINCIA LIKE '%" . $intProvinciaBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(redes.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(redes.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			redes.COD_PROVINCIA ASC, redes.FEC_FECHA_ALTA DESC ";

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

	function getRedesRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intRed = $this->arrRecord[$intNumRecord]["COD_RED"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->intProvincia = $this->arrRecord[$intNumRecord]["COD_PROVINCIA"];
			$this->strProvincia = $this->arrRecord[$intNumRecord]["DES_PROVINCIA"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
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

	/* Levanto los datos de las provincias */
	function getProvincias($intProvincia = false){
		$intProvincia = intval($intProvincia);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		d.COD_PROVINCIA, ";
		$strSQL .= "		d.DES_PROVINCIA ";
		$strSQL .= "	FROM ";
		$strSQL .= "		PROVINCIAS d ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intProvincia){
			$strSQL .= "		AND d.COD_PROVINCIA = $intProvincia ";
		}

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			d.DES_PROVINCIA ASC ";

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

	function getProvinciasRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intProvincia = $this->arrRecord[$intNumRecord]["COD_PROVINCIA"];
			$this->strProvincia = $this->arrRecord[$intNumRecord]["DES_PROVINCIA"];
			return true;
		} else
			return false;
	}

	function getRedesMensaje(){
		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		redes.DES_MENSAJE, ";
		$strSQL .= "		redes.DES_LINK ";
		$strSQL .= "	FROM ";
		$strSQL .= "		REDES_MENSAJE redes ";

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

	function getRedesMensajeRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->strMensaje = $this->arrRecord[$intNumRecord]["DES_MENSAJE"];
			$this->strLink = $this->arrRecord[$intNumRecord]["DES_LINK"];
			return true;
		} else
			return false;
	}

	function updateRedMensaje($strTexto, $strLink){
		/* Corrigo Texto Entrante */
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strLink = stringToSQL($strLink);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	REDES_MENSAJE ";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_MENSAJE = '$strTexto', ";
		$strSQL .= "			DES_LINK = '$strLink' ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

}

?>