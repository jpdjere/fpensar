<?php

/****************************************************************************
* Class clsCoyuntura: Clase de Coyuntura                                    *
****************************************************************************/

class clsCoyuntura {

	var $intCoyuntura;
	var $strTitulo;
	var $strSeccionInternacional;
	var $strSeccionEconomia;
	var $strSeccionPolitica;
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

	/* Chequeo una Coyuntura a subir */
	function chequearCoyuntura($strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strFecha){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strTitulo, 3, 150, "strTitulo");
		$objCheck->checkString($strSeccionInternacional, 3, 150, "strSeccionInternacional");
		$objCheck->checkString($strSeccionEconomia, 3, 150, "strSeccionEconomia");
		$objCheck->checkString($strSeccionPolitica, 3, 150, "strSeccionPolitica");
		$objCheck->checkDateSpecific($strFecha, 6, 10, "strFecha");

		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorSeccionInternacional = (isset($objCheck->arrErrors["strSeccionInternacional"])) ? $objCheck->arrErrors["strSeccionInternacional"] : "";
		$this->errorSeccionEconomia = (isset($objCheck->arrErrors["strSeccionEconomia"])) ? $objCheck->arrErrors["strSeccionEconomia"] : "";
		$this->errorSeccionPolitica = (isset($objCheck->arrErrors["strSeccionPolitica"])) ? $objCheck->arrErrors["strSeccionPolitica"] : "";
		$this->errorFecha = (isset($objCheck->arrErrors["strFecha"])) ? $objCheck->arrErrors["strFecha"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagen($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen && $strImagen["name"]){
			$strImageName = md5(microtime());
			$this->strImagen = resizeImage(PATH_IMAGEN_COYUNTURA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_COYUNTURA_GRANDE_ANCHO, IMAGEN_COYUNTURA_GRANDE_ALTO, $strImageName . ".jpg");
			$this->strImagenChica = resizeImage(PATH_IMAGEN_COYUNTURA_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_COYUNTURA_CHICA_ANCHO, IMAGEN_COYUNTURA_CHICA_ALTO, $this->strImagen);
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

	/* Inserta una Coyuntura en la Tabla COYUNTURA */
	function insertCoyuntura($strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearCoyuntura($strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_COYUNTURA_LOCAL);
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
		$strSeccionInternacional = stringToSQL(capitalizeFirst($strSeccionInternacional));
		$strSeccionEconomia = stringToSQL(capitalizeFirst($strSeccionEconomia));
		$strSeccionPolitica = stringToSQL(capitalizeFirst($strSeccionPolitica));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	COYUNTURA";
		$strSQL .= "		(DES_TITULO, ";
		$strSQL .= "		DES_SECCION_INTERNACIONAL, ";
		$strSQL .= "		DES_SECCION_ECONOMIA, ";
		$strSQL .= "		DES_SECCION_POLITICA, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		DES_ARCHIVO, ";
		$strSQL .= "		FEC_FECHA, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strTitulo', ";
		$strSQL .= "		'$strSeccionInternacional', ";
		$strSQL .= "		'$strSeccionEconomia', ";
		$strSQL .= "		'$strSeccionPolitica', ";
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

		$this->intCoyuntura = mysql_insert_id();
		return $this->intCoyuntura;
	}

	/* Hago Update de la Coyuntura */
	function updateCoyuntura($intCoyuntura, $strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearCoyuntura($strTitulo, $strSeccionInternacional, $strSeccionEconomia, $strSeccionPolitica, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_COYUNTURA_LOCAL);
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
		$strSeccionInternacional = stringToSQL(capitalizeFirst($strSeccionInternacional));
		$strSeccionEconomia = stringToSQL(capitalizeFirst($strSeccionEconomia));
		$strSeccionPolitica = stringToSQL(capitalizeFirst($strSeccionPolitica));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	COYUNTURA";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			DES_SECCION_INTERNACIONAL = '$strSeccionInternacional', ";
		$strSQL .= "			DES_SECCION_ECONOMIA = '$strSeccionEconomia', ";
		$strSQL .= "			DES_SECCION_POLITICA = '$strSeccionPolitica', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		if ($strArchivo)
			$strSQL .= "			DES_ARCHIVO = '" . $this->strArchivo . "', ";
		$strSQL .= "			FEC_FECHA = '" . $strFecha . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_COYUNTURA = $intCoyuntura";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Coyuntura de la Tabla Coyuntura */
	function deleteCoyuntura($intCoyuntura){

		/* Borro la tabla COYUNTURA */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	COYUNTURA ";
		$strSQL .= "		WHERE COD_COYUNTURA = $intCoyuntura";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intCoyuntura, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	COYUNTURA ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_COYUNTURA = $intCoyuntura";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getCoyunturaTotal($blnBackoffice = false, $arrCoyunturaToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(coyuntura.COD_COYUNTURA) AS NUM_COYUNTURA ";
		$strSQL .= "	FROM ";
		$strSQL .= "		COYUNTURA coyuntura ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND coyuntura.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND coyuntura.FLG_HABILITADO = 'N' ";
		}

		if ($arrCoyunturaToExclude){
			if (is_array($arrCoyunturaToExclude)){
				$strSQL .= "		AND coyuntura.COD_COYUNTURA NOT IN (";
				for ($i = 0; $i < sizeOf($arrCoyunturaToExclude); $i++){
					$strSQL .= "			$arrCoyunturaToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrCoyunturaToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND coyuntura.COD_COYUNTURA <> $arrCoyunturaToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (coyuntura.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR coyuntura.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(coyuntura.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(coyuntura.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			coyuntura.COD_COYUNTURA ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getCoyuntura($arrCoyuntura = false, $blnBackoffice = false, $arrCoyunturaToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		coyuntura.COD_COYUNTURA, ";
		$strSQL .= "		coyuntura.DES_TITULO, ";
		$strSQL .= "		coyuntura.DES_SECCION_INTERNACIONAL, ";
		$strSQL .= "		coyuntura.DES_SECCION_ECONOMIA, ";
		$strSQL .= "		coyuntura.DES_SECCION_POLITICA, ";
		$strSQL .= "		coyuntura.DES_IMAGEN, ";
		$strSQL .= "		coyuntura.DES_ARCHIVO, ";
		$strSQL .= "		DATE_FORMAT(coyuntura.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA, ";
		$strSQL .= "		DATE_FORMAT(coyuntura.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(coyuntura.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(coyuntura.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(coyuntura.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		coyuntura.FEC_FECHA_ALTA, ";
		$strSQL .= "		coyuntura.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		coyuntura.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		COYUNTURA coyuntura ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrCoyuntura){
			if (is_array($arrCoyuntura)){
				$strSQL .= "		AND coyuntura.COD_COYUNTURA IN (";
				for ($i = 0; $i < sizeOf($arrCoyuntura); $i++){
					$strSQL .= "			$arrCoyuntura[$i] ";
					$strSQL .= ($i != (sizeOf($arrCoyuntura) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND coyuntura.COD_COYUNTURA = $arrCoyuntura";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND coyuntura.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND coyuntura.FLG_HABILITADO = 'N' ";
		}

		if ($arrCoyunturaToExclude){
			if (is_array($arrCoyunturaToExclude)){
				$strSQL .= "		AND coyuntura.COD_COYUNTURA NOT IN (";
				for ($i = 0; $i < sizeOf($arrCoyunturaToExclude); $i++){
					$strSQL .= "			$arrCoyunturaToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrCoyunturaToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND coyuntura.COD_COYUNTURA <> $arrCoyunturaToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (coyuntura.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR coyuntura.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(coyuntura.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(coyuntura.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "		coyuntura.COD_COYUNTURA ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "		coyuntura.FEC_FECHA DESC, coyuntura.DES_TITULO ";

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

	function getCoyunturaRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intCoyuntura = $this->arrRecord[$intNumRecord]["COD_COYUNTURA"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strSeccionInternacional = $this->arrRecord[$intNumRecord]["DES_SECCION_INTERNACIONAL"];
			$this->strSeccionEconomia = $this->arrRecord[$intNumRecord]["DES_SECCION_ECONOMIA"];
			$this->strSeccionPolitica = $this->arrRecord[$intNumRecord]["DES_SECCION_POLITICA"];
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

}

?>