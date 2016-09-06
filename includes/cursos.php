<?php

/****************************************************************************
* Class clsCursos: Clase de Cursos                                    *
****************************************************************************/

class clsCursos {

	var $intCurso;
	var $strCurso;
	var $strTexto;
	var $strFechaInicioInscripcion;
	var $strFechaFinInscripcion;
	var $strFecha;
	var $intCupos;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	/* Chequeo una Curso a subir */
	function chequearCurso($strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strCurso, 3, 100, "strCurso");
		$objCheck->checkAnyText($strTexto, 10, 1000, "strTexto");
		$objCheck->checkDateSpecific($strFechaInicioInscripcion, 6, 10, "strFechaInicioInscripcion");
		$objCheck->checkDateSpecific($strFechaFinInscripcion, 6, 10, "strFechaFinInscripcion");
		$objCheck->checkDateSpecific($strFecha, 6, 10, "strFecha");
		$objCheck->checkSpecificNumber($intCupos, 1, 2, 1, 99, "intCupos");

		$this->errorCurso = (isset($objCheck->arrErrors["strCurso"])) ? $objCheck->arrErrors["strCurso"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorFechaInicioInscripcion = (isset($objCheck->arrErrors["strFechaInicioInscripcion"])) ? $objCheck->arrErrors["strFechaInicioInscripcion"] : "";
		$this->errorFechaFinInscripcion = (isset($objCheck->arrErrors["strFechaFinInscripcion"])) ? $objCheck->arrErrors["strFechaFinInscripcion"] : "";
		$this->errorFecha = (isset($objCheck->arrErrors["strFecha"])) ? $objCheck->arrErrors["strFecha"] : "";
		$this->errorCupos = (isset($objCheck->arrErrors["intCupos"])) ? $objCheck->arrErrors["intCupos"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	/* Inserta una Curso en la Tabla CURSOS */
	function insertCurso($strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos, $blnHabilitado){

		$this->chequearCurso($strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strCurso = stringToSQL(capitalizeFirst($strCurso));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strFecha = dateToSQL($strFecha);
		$strFechaInicioInscripcion = dateToSQL($strFechaInicioInscripcion);
		$strFechaFinInscripcion = dateToSQL($strFechaFinInscripcion);
		$intCupos = intval($intCupos);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	CURSOS";
		$strSQL .= "		(DES_CURSO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		FEC_FECHA_INICIO_INSCRIPCION, ";
		$strSQL .= "		FEC_FECHA_FIN_INSCRIPCION, ";
		$strSQL .= "		FEC_FECHA, ";
		$strSQL .= "		NUM_CUPOS, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strCurso', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'$strFechaInicioInscripcion', ";
		$strSQL .= "		'$strFechaFinInscripcion', ";
		$strSQL .= "		'$strFecha', ";
		$strSQL .= "		$intCupos, ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->intCurso = mysql_insert_id();
		return $this->intCurso;
	}

	/* Hago Update de la Curso */
	function updateCurso($intCurso, $strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos, $blnHabilitado){
		$intCurso = intval($intCurso);

		$this->chequearCurso($strCurso, $strTexto, $strFechaInicioInscripcion, $strFechaFinInscripcion, $strFecha, $intCupos);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strCurso = stringToSQL(capitalizeFirst($strCurso));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strFecha = dateToSQL($strFecha);
		$strFechaInicioInscripcion = dateToSQL($strFechaInicioInscripcion);
		$strFechaFinInscripcion = dateToSQL($strFechaFinInscripcion);
		$intCupos = intval($intCupos);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	CURSOS";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_CURSO = '$strCurso', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		$strSQL .= "			FEC_FECHA_INICIO_INSCRIPCION = '" . $strFechaInicioInscripcion . "', ";
		$strSQL .= "			FEC_FECHA_FIN_INSCRIPCION = '" . $strFechaFinInscripcion . "', ";
		$strSQL .= "			FEC_FECHA = '" . $strFecha . "', ";
		$strSQL .= "			NUM_CUPOS = " . $intCupos . ", ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_CURSO = $intCurso";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Curso de la Tabla Cursos */
	function deleteCurso($intCurso){

		/* Borro la tabla CURSOS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	CURSOS ";
		$strSQL .= "		WHERE COD_CURSO = $intCurso";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intCurso, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	CURSOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_CURSO = $intCurso";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getCursosTotal($blnBackoffice = false, $arrCursosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(cursos.COD_CURSO) AS NUM_CURSOS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		CURSOS cursos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND cursos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND cursos.FLG_HABILITADO = 'N' ";
		}

		if ($arrCursosToExclude){
			if (is_array($arrCursosToExclude)){
				$strSQL .= "		AND cursos.COD_CURSO NOT IN (";
				for ($i = 0; $i < sizeOf($arrCursosToExclude); $i++){
					$strSQL .= "			$arrCursosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrCursosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND cursos.COD_CURSO <> $arrCursosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (cursos.DES_CURSO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR cursos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(cursos.FEC_FECHA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(cursos.FEC_FECHA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			cursos.COD_CURSO ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getCursos($arrCursos = false, $blnBackoffice = false, $arrCursosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		cursos.COD_CURSO, ";
		$strSQL .= "		cursos.DES_CURSO, ";
		$strSQL .= "		cursos.DES_TEXTO, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_INICIO_INSCRIPCION, '%d/%m/%Y') AS FEC_FECHA_INICIO_INSCRIPCION, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_INICIO_INSCRIPCION, '%d/%m') AS FEC_FECHA_INICIO_INSCRIPCION_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_FIN_INSCRIPCION, '%d/%m/%Y') AS FEC_FECHA_FIN_INSCRIPCION, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_FIN_INSCRIPCION, '%d/%m') AS FEC_FECHA_FIN_INSCRIPCION_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m/%Y') AS FEC_FECHA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m') AS FEC_FECHA_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		IF(FEC_FECHA_INICIO_INSCRIPCION <= CURDATE() AND FEC_FECHA_FIN_INSCRIPCION >= CURDATE(), 'S', 'N') AS INSCRIPCION_ACTIVA, ";
		$strSQL .= "		IF(FEC_FECHA_FIN_INSCRIPCION < CURDATE(), 'S', 'N') AS INSCRIPCION_FINALIZADA, ";
		$strSQL .= "		cursos.NUM_CUPOS, ";
		$strSQL .= "		COUNT(inscriptos.COD_INSCRIPTO) AS NUM_INSCRIPTOS, ";
		$strSQL .= "		cursos.FEC_FECHA_ALTA, ";
		$strSQL .= "		cursos.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		cursos.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		CURSOS cursos ";
		$strSQL .= "		LEFT OUTER JOIN CURSOS_INSCRIPTOS inscriptos ";
		$strSQL .= "			ON (cursos.COD_CURSO = inscriptos.COD_CURSO) ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrCursos){
			if (is_array($arrCursos)){
				$strSQL .= "		AND cursos.COD_CURSO IN (";
				for ($i = 0; $i < sizeOf($arrCursos); $i++){
					$strSQL .= "			$arrCursos[$i] ";
					$strSQL .= ($i != (sizeOf($arrCursos) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND cursos.COD_CURSO = $arrCursos";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND cursos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND cursos.FLG_HABILITADO = 'N' ";
		}

		if ($arrCursosToExclude){
			if (is_array($arrCursosToExclude)){
				$strSQL .= "		AND cursos.COD_CURSO NOT IN (";
				for ($i = 0; $i < sizeOf($arrCursosToExclude); $i++){
					$strSQL .= "			$arrCursosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrCursosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND cursos.COD_CURSO <> $arrCursosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (cursos.DES_CURSO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR cursos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(cursos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(cursos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			cursos.COD_CURSO ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			cursos.FEC_FECHA_ALTA DESC, cursos.DES_CURSO ";

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

	/* Levanto los datos de la base */
	function getCursosActivos($intCurso = false, $blnInscripcionAbierta = false){
		$intCurso = intval($intCurso);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		cursos.COD_CURSO, ";
		$strSQL .= "		cursos.DES_CURSO, ";
		$strSQL .= "		cursos.DES_TEXTO, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_INICIO_INSCRIPCION, '%d/%m/%Y') AS FEC_FECHA_INICIO_INSCRIPCION, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_INICIO_INSCRIPCION, '%d/%m') AS FEC_FECHA_INICIO_INSCRIPCION_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_FIN_INSCRIPCION, '%d/%m/%Y') AS FEC_FECHA_FIN_INSCRIPCION, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA_FIN_INSCRIPCION, '%d/%m') AS FEC_FECHA_FIN_INSCRIPCION_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m/%Y') AS FEC_FECHA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m') AS FEC_FECHA_CORTA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(cursos.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		IF(cursos.FEC_FECHA_INICIO_INSCRIPCION <= CURDATE() AND FEC_FECHA_FIN_INSCRIPCION >= CURDATE(), 'S', 'N') AS INSCRIPCION_ACTIVA, ";
		$strSQL .= "		IF(cursos.FEC_FECHA_FIN_INSCRIPCION < CURDATE(), 'S', 'N') AS INSCRIPCION_FINALIZADA, ";
		$strSQL .= "		cursos.NUM_CUPOS, ";
		$strSQL .= "		COUNT(inscriptos.COD_INSCRIPTO) AS NUM_INSCRIPTOS, ";
		$strSQL .= "		cursos.FEC_FECHA_ALTA, ";
		$strSQL .= "		cursos.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		cursos.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		CURSOS cursos ";
		$strSQL .= "		LEFT OUTER JOIN CURSOS_INSCRIPTOS inscriptos ";
		$strSQL .= "			ON (cursos.COD_CURSO = inscriptos.COD_CURSO) ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intCurso){
			$strSQL .= "		AND cursos.COD_CURSO = $intCurso ";
		}
		if ($blnInscripcionAbierta){
			$strSQL .= "		AND cursos.FEC_FECHA_INICIO_INSCRIPCION <= CURDATE() ";
			$strSQL .= "		AND cursos.FEC_FECHA_FIN_INSCRIPCION >= CURDATE() ";
		}else{
			$strSQL .= "		AND cursos.FEC_FECHA >= SYSDATE() ";
		}
		$strSQL .= "		AND cursos.FLG_HABILITADO = 'S' ";
		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			cursos.COD_CURSO ";
		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			cursos.FEC_FECHA DESC, cursos.DES_CURSO ";
		$strSQL .= " 	LIMIT 2";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->arrRecord = $objQuery->Record;
		$this->intTotal = $objQuery->Row;

		return $this->intTotal;
	}

	function getCursosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intCurso = $this->arrRecord[$intNumRecord]["COD_CURSO"];
			$this->strCurso = $this->arrRecord[$intNumRecord]["DES_CURSO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strFechaInicioInscripcion = $this->arrRecord[$intNumRecord]["FEC_FECHA_INICIO_INSCRIPCION"];
			$this->strFechaInicioInscripcionCorta = $this->arrRecord[$intNumRecord]["FEC_FECHA_INICIO_INSCRIPCION_CORTA"];
			$this->strFechaFinInscripcion = $this->arrRecord[$intNumRecord]["FEC_FECHA_FIN_INSCRIPCION"];
			$this->strFechaFinInscripcionCorta = $this->arrRecord[$intNumRecord]["FEC_FECHA_FIN_INSCRIPCION_CORTA"];
			$this->strFecha = $this->arrRecord[$intNumRecord]["FEC_FECHA"];
			$this->strFechaCorta = $this->arrRecord[$intNumRecord]["FEC_FECHA_CORTA"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["FEC_FECHA_LISTADO"];
			$this->strFechaDia = $this->arrRecord[$intNumRecord]["FEC_FECHA_DIA"];
			$this->strFechaMes = $this->arrRecord[$intNumRecord]["FEC_FECHA_MES"];
			$this->strFechaAnio = $this->arrRecord[$intNumRecord]["FEC_FECHA_ANIO"];
			$this->blnInscripcionActiva = ($this->arrRecord[$intNumRecord]["INSCRIPCION_ACTIVA"] == 'S');
			$this->blnInscripcionFinalizada = ($this->arrRecord[$intNumRecord]["INSCRIPCION_FINALIZADA"] == 'S');
			$this->intCupos = $this->arrRecord[$intNumRecord]["NUM_CUPOS"];
			$this->intInscriptos = $this->arrRecord[$intNumRecord]["NUM_INSCRIPTOS"];
			$this->intCuposDisponibles = $this->intCupos - $this->intInscriptos;
			$this->strFechaAlta = $this->arrRecord[$intNumRecord]["FEC_FECHA_ALTA"];
			$this->strFechaModificacion = $this->arrRecord[$intNumRecord]["FEC_FECHA_MODIFICACION"];
			$this->blnHabilitado = ($this->arrRecord[$intNumRecord]["FLG_HABILITADO"] == "S") ? true : false;
			return true;
		} else
			return false;
	}

	function getCursosMensaje(){
		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		cursos.DES_MENSAJE ";
		$strSQL .= "	FROM ";
		$strSQL .= "		CURSOS_MENSAJE cursos ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return (($objQuery->Row) ? $objQuery->Record[0]['DES_MENSAJE'] : '');
	}

	function updateCursoMensaje($strTexto){
		/* Corrigo Texto Entrante */
		$strTexto = stringToSQL(capitalizeFirst($strTexto));

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	CURSOS_MENSAJE ";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_MENSAJE = '$strTexto' ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	function inscriptUsuario($intCurso, $strEmail){
		$intCurso = intval($intCurso);
		$strEmail = stringToSQL($strEmail);

		// Registro al Usuario
		$strSQL = "INSERT INTO ";
		$strSQL .= "	 CURSOS_INSCRIPTOS ";
		$strSQL .= "	 	(COD_CURSO, ";
		$strSQL .= "	 	DES_EMAIL, ";
		$strSQL .= "	 	FEC_FECHA) ";
		$strSQL .= "	 VALUES ";
		$strSQL .= "	 	(" . $intCurso . ", ";
		$strSQL .= "	 	'" . $strEmail . "', ";
		$strSQL .= "	 	SYSDATE()) ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;

		$objQuery->query($strSQL);

		$this->intInscripto = mysql_insert_id();
		return $this->intInscripto;
	}

	function inscriptNuevoUsuario($intCurso, $strNombre, $strApellido, $strDNI, $strEmail, $strProvincia, $strTelefono, $strContrasenia){
		$intCurso = intval($intCurso);
		$strNombre = stringToSQL($strNombre);
		$strApellido = stringToSQL($strApellido);
		$strDNI = stringToSQL($strDNI);
		$strEmail = stringToSQL($strEmail);
		$strProvincia = stringToSQL($strProvincia);
		$strTelefono = stringToSQL($strTelefono);
		$strContrasenia = md5($strContrasenia);

		// Registro al Usuario
		$strSQL = "INSERT INTO ";
		$strSQL .= "	 CURSOS_INSCRIPTOS ";
		$strSQL .= "	 	(COD_CURSO, ";
		$strSQL .= "	 	DES_NOMBRE, ";
		$strSQL .= "	 	DES_APELLIDO, ";
		$strSQL .= "	 	DES_DNI, ";
		$strSQL .= "	 	DES_EMAIL, ";
		$strSQL .= "	 	DES_PROVINCIA, ";
		$strSQL .= "	 	DES_TELEFONO, ";
		$strSQL .= "	 	DES_CONTRASENIA, ";
		$strSQL .= "	 	FEC_FECHA) ";
		$strSQL .= "	 VALUES ";
		$strSQL .= "	 	(" . $intCurso . ", ";
		$strSQL .= "	 	'" . $strNombre . "', ";
		$strSQL .= "	 	'" . $strApellido . "', ";
		$strSQL .= "	 	'" . $strDNI . "', ";
		$strSQL .= "	 	'" . $strEmail . "', ";
		$strSQL .= "	 	'" . $strProvincia . "', ";
		$strSQL .= "	 	'" . $strTelefono . "', ";
		$strSQL .= "	 	'" . $strContrasenia . "', ";
		$strSQL .= "	 	SYSDATE()) ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;

		$objQuery->query($strSQL);

		$this->intInscripto = mysql_insert_id();
		return $this->intInscripto;
	}

	function getInscripto($intCurso, $strEmail){
		$intCurso = intval($intCurso);
		$strEmail = stringToSQL($strEmail);

		$strSQL = " SELECT ";
		$strSQL .= "	 	COD_INSCRIPTO ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		CURSOS_INSCRIPTOS ";
		$strSQL .= "	WHERE  ";
		$strSQL .= "		COD_CURSO = $intCurso ";
		$strSQL .= "		AND DES_EMAIL = '" . $strEmail . "' ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;

		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	function getInscriptos($intCurso, $intInscripto = false, $intOrden = false, $intDireccion = false, $strBusqueda = ""){
		$intCurso = intval($intCurso);
		$intInscripto = intval($intInscripto);
		$intOrden = intval($intOrden);
		$intDireccion = intval($intDireccion);
		$strBusqueda = stringToSQL($strBusqueda);

		$strSQL = " SELECT ";
		$strSQL .= "	 	i.COD_INSCRIPTO, ";
		$strSQL .= "	 	i.DES_NOMBRE, ";
		$strSQL .= "	 	i.DES_APELLIDO, ";
		$strSQL .= "	 	i.DES_DNI, ";
		$strSQL .= "	 	i.DES_EMAIL, ";
		$strSQL .= "	 	i.DES_PROVINCIA, ";
		$strSQL .= "	 	i.DES_TELEFONO, ";
		$strSQL .= "	 	c.COD_CURSO, ";
		$strSQL .= "	 	c.DES_CURSO, ";
		$strSQL .= "	 	i.FEC_FECHA, ";
		$strSQL .= "	 	DATE_FORMAT(i.FEC_FECHA, '%d/%m/%Y %H:%i:%s') AS DES_FECHA, ";
		$strSQL .= "	 	DATE_FORMAT(i.FEC_FECHA, '%d/%m/%Y') DES_FECHA_LISTADO ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		CURSOS c ";
		$strSQL .= "		LEFT OUTER JOIN CURSOS_INSCRIPTOS i ";
		$strSQL .= "			ON (c.COD_CURSO = i.COD_CURSO ";

		if ($intInscripto){
			$strSQL .= "		AND i.COD_INSCRIPTO = $intInscripto ";
		}

		$strSQL .= "			) ";
		if ($strBusqueda){
			$strSQL .= "		AND (LOWER(i.DES_NOMBRE) LIKE '%" . strToLower($strBusqueda) . "%' ";
			$strSQL .= "			OR LOWER(i.DES_APELLIDO) LIKE '%" . strToLower($strBusqueda) . "%') ";
		}

		$strSQL .= "	WHERE ";
		$strSQL .= "		c.COD_CURSO = $intCurso ";

		$strSQL .= "	ORDER BY ";
		if ($intOrden){
			switch($intOrden){
				case "1":
					$strSQL .= "		i.DES_NOMBRE ";
					break;
				case "2":
					$strSQL .= "		i.DES_APELLIDO ";
					break;
				case "3":
					$strSQL .= "		i.DES_EMAIL ";
					break;
				case "4":
					$strSQL .= "		i.COD_PROVINCIA ";
					break;
				case "4":
					$strSQL .= "		i.FEC_FECHA ";
					break;
			}

			switch($intDireccion){
				case "1":
					$strSQL .= "		ASC ";
					break;
				case "2":
					$strSQL .= "		DESC ";
					break;
			}

		}else{
			$strSQL .= "		i.FEC_FECHA DESC ";
		}

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;

		$objQuery->query($strSQL);

		$this->intTotal = $objQuery->Row;
		$this->arrRecord = $objQuery->Record;

		return $this->intTotal;

	}

	function getInscriptosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intInscripto = $this->arrRecord[$intNumRecord]["COD_INSCRIPTO"];
			$this->strNombre = $this->arrRecord[$intNumRecord]["DES_NOMBRE"];
			$this->strApellido = $this->arrRecord[$intNumRecord]["DES_APELLIDO"];
			$this->strDNI = $this->arrRecord[$intNumRecord]["DES_DNI"];
			$this->strEmail = $this->arrRecord[$intNumRecord]["DES_EMAIL"];
			$this->strProvincia = $this->arrRecord[$intNumRecord]["DES_PROVINCIA"];
			$this->strTelefono = $this->arrRecord[$intNumRecord]["DES_TELEFONO"];
			$this->intCurso = $this->arrRecord[$intNumRecord]["COD_CURSO"];
			$this->strCurso = $this->arrRecord[$intNumRecord]["DES_CURSO"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["DES_FECHA_LISTADO"];
			$this->strFecha = $this->arrRecord[$intNumRecord]["DES_FECHA"];
			return true;
		} else
			return false;
	}

}

?>