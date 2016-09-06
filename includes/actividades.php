<?php

/****************************************************************************
* Class clsActividades: Clase de Actividades                                    *
****************************************************************************/

class clsActividades {

	var $intActividad;
	var $strTitulo;
	var $strTexto;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	/* Chequeo una Actividad a subir */
	function chequearActividad($strTitulo, $strTexto){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strTitulo, 3, 100, "strTitulo");
		$objCheck->checkAnyText($strTexto, 10, 500, "strTexto");

		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	/* Inserta una Actividad en la Tabla ACTIVIDADES */
	function insertActividad($strTitulo, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearActividad($strTitulo, $strTexto);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageWidth(PATH_IMAGEN_ACTIVIDADES_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_ACTIVIDADES_ANCHO, IMAGEN_ACTIVIDADES_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));

		/* Me fijo el último orden de esta tabla */
		$strSQL = " SELECT ";
		$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		ACTIVIDADES ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = ($objQuery->Row) ? ($objQuery->Record[0]["NUM_ORDEN"] + 1) : 1;

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	ACTIVIDADES";
		$strSQL .= "		(DES_TITULO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		NUM_ORDEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strTitulo', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'" . $this->strImagen . "', ";
		$strSQL .= "		$intOrden, ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

		$this->intActividad = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Actividad */
	function updateActividad($intActividad, $strTitulo, $strTexto, $strImagen, $strImagenAnterior, $blnHabilitado){
		$intActividad = intval($intActividad);

		$this->chequearActividad($strTitulo, $strTexto);

		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$this->strImagen = resizeImageWidth(PATH_IMAGEN_ACTIVIDADES_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_ACTIVIDADES_ANCHO, IMAGEN_ACTIVIDADES_ALTO);
			if (!$this->strImagen){
				$this->errorImagen = "Debe elegir una imagen";
				$this->intErrores++;
			}
		}

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	ACTIVIDADES";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_ACTIVIDAD = $intActividad";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Actividad de la Tabla Actividades */
	function deleteActividad($intActividad){
		$intActividad = intval($intActividad);

		/* Borro la tabla ACTIVIDADES */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	ACTIVIDADES ";
		$strSQL .= "		WHERE COD_ACTIVIDAD = $intActividad";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intActividad, $blnQualify = false){
		$intActividad = intval($intActividad);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	ACTIVIDADES ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_ACTIVIDAD = $intActividad";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	// Ordenar Actividades
	function orderActividad($intActividad, $blnOrden){
		$intActividad = intval($intActividad);
		if (!$intActividad)
			return false;

		/* Obtengo el orden de la actividad actual */
		$strSQL = " SELECT ";
		$strSQL .= " 		NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		ACTIVIDADES ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_ACTIVIDAD = $intActividad ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = $objQuery->Record[0]["NUM_ORDEN"];

		/* Obtengo el orden de la actividad a actualizar */
		$strSQL = " SELECT ";
		if ($blnOrden > 1 || $blnOrden == -1)
			$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		else
			$strSQL .= " 		MIN(NUM_ORDEN) AS NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		ACTIVIDADES ";
		$strSQL .= "	WHERE ";
		if ($blnOrden == -1 || $blnOrden == -2)
			$strSQL .= " 		NUM_ORDEN < $intOrden ";
		else if ($blnOrden == 1 || $blnOrden == 2)
			$strSQL .= " 		NUM_ORDEN > $intOrden ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);
		$intOrdenNuevo = $objQuery->Record[0]["NUM_ORDEN"];

		if ($blnOrden == 1 || $blnOrden == -1){
			/* Updeteo el nuevo orden de la actividad a actualizar */
			$strSQL = " UPDATE ";
			$strSQL .= " 		ACTIVIDADES ";
			$strSQL .= " 	SET ";
			$strSQL .= " 		NUM_ORDEN = $intOrden ";
			$strSQL .= "	WHERE ";
			$strSQL .= "		NUM_ORDEN = $intOrdenNuevo ";
			$objQuery->query($strSQL);
		}else{
			if ($blnOrden == 2){
				for ($i = ($intOrden + 1); $i <= $intOrdenNuevo; $i++){
					/* Updeteo todas las actividades a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		ACTIVIDADES ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i - 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}else if ($blnOrden == -2){
				for ($i = ($intOrden - 1); $i >= $intOrdenNuevo; $i--){
					/* Updeteo todas las actividades a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		ACTIVIDADES ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i + 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}
		}

		/* Updeteo el nuevo orden de la actividad a ordenar */
		$strSQL = " UPDATE ";
		$strSQL .= " 		ACTIVIDADES ";
		$strSQL .= " 	SET ";
		$strSQL .= " 		NUM_ORDEN = $intOrdenNuevo ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_ACTIVIDAD = $intActividad ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

	}

	/* Levanto los datos de la base */
	function getActividadesTotal($blnBackoffice = false, $arrActividadesToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){
		$strTextoBusqueda = stringToSQL($strTextoBusqueda);
		$intMes = intval($intMes);
		$intAnio = intval($intAnio);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(actividades.COD_ACTIVIDAD) AS NUM_ACTIVIDADES ";
		$strSQL .= "	FROM ";
		$strSQL .= "		ACTIVIDADES actividades ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND actividades.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND actividades.FLG_HABILITADO = 'N' ";
		}

		if ($arrActividadesToExclude){
			if (is_array($arrActividadesToExclude)){
				$strSQL .= "		AND actividades.COD_ACTIVIDAD NOT IN (";
				for ($i = 0; $i < sizeOf($arrActividadesToExclude); $i++){
					$strSQL .= "			" . intval($arrActividadesToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrActividadesToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND actividades.COD_ACTIVIDAD <> " . intval($arrActividadesToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (actividades.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR actividades.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			actividades.COD_ACTIVIDAD ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getActividades($arrActividades = false, $blnBackoffice = false, $arrActividadesToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		actividades.COD_ACTIVIDAD, ";
		$strSQL .= "		actividades.DES_TITULO, ";
		$strSQL .= "		actividades.DES_TEXTO, ";
		$strSQL .= "		actividades.DES_IMAGEN, ";
		$strSQL .= "		DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		actividades.FEC_FECHA_ALTA, ";
		$strSQL .= "		actividades.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		actividades.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		ACTIVIDADES actividades ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrActividades){
			if (is_array($arrActividades)){
				$strSQL .= "		AND actividades.COD_ACTIVIDAD IN (";
				for ($i = 0; $i < sizeOf($arrActividades); $i++){
					$strSQL .= "			$arrActividades[$i] ";
					$strSQL .= ($i != (sizeOf($arrActividades) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND actividades.COD_ACTIVIDAD = $arrActividades";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND actividades.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND actividades.FLG_HABILITADO = 'N' ";
		}

		if ($arrActividadesToExclude){
			if (is_array($arrActividadesToExclude)){
				$strSQL .= "		AND actividades.COD_ACTIVIDAD NOT IN (";
				for ($i = 0; $i < sizeOf($arrActividadesToExclude); $i++){
					$strSQL .= "			$arrActividadesToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrActividadesToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND actividades.COD_ACTIVIDAD <> $arrActividadesToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (actividades.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR actividades.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(actividades.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			actividades.COD_ACTIVIDAD ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			actividades.NUM_ORDEN, actividades.FEC_FECHA_ALTA DESC, actividades.DES_TITULO ";

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

	function getActividadesRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intActividad = $this->arrRecord[$intNumRecord]["COD_ACTIVIDAD"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
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

}

?>