<?php

/****************************************************************************
* Class cPerfil: Clase de Perfiles de Usuarios de Backoffice                *
****************************************************************************/

class clsPerfil {

	var $intPerfil;
	var $strPerfil;
	var $strDescripcion;
	var $arrAccesoTotal;
	var $arrSoloLectura;

	var $intErrores = 0;
	var $intTotal = 0;
	var $arrRecord;

	/* Chequeo el Perfil a Insertar o Updetear */
	function chequearPerfil($strPerfil, $strPerfilAnterior, $strDescripcion, $arrAccesoTotal, $arrSoloLectura, $blnNuevoPerfil = false){

		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		/* Chequeo errores */
		$objCheck->checkString($strPerfil, 3, 50, "strPerfil");
		if ($strDescripcion)
			$objCheck->checkString($strDescripcion, 3, 255, "strDescripcion");

		$this->errorPerfil = $objCheck->arrErrors["strPerfil"];
		$this->errorDescripcion = ($strDescripcion) ? $objCheck->arrErrors["strDescripcion"] : "";
		if ((!$objCheck->arrErrors["strPerfil"] && $blnNuevoPerfil) || 
			(!$objCheck->arrErrors["strPerfil"] && !$blnNuevoPerfil && $strPerfil != $strPerfilAnterior)){
			if ($this->checkPerfil($strPerfil)){
				$this->errorPerfil = "El perfil ya existe";
				$objCheck->errorsCount++;
			}
		}
		/* Chequeo si selecciono Secciones */
		if (!$arrAccesoTotal && !$arrSoloLectura){
			$this->errorSecciones = "Debe seleccionar por lo menos una sección";
			$objCheck->errorsCount++;
		}

		$this->intErrores = $objCheck->errorsCount;
	}

	/* Inserta un perfil en la Base de Datos */
	function insertPerfil($strPerfil, $strDescripcion, $arrAccesoTotal, $arrSoloLectura){

		$this->chequearPerfil($strPerfil, "", $strDescripcion, $arrAccesoTotal, $arrSoloLectura, true);

		if ($this->intErrores)
			return false;

		/* Corrigo Texto Entrante */
		$strPerfil = stringToSQL(strToLower($strPerfil));
		$strDescripcion = stringToSQL(capitalizeFirst($strDescripcion));

		/* Inserto el perfil en la tabla BACKOFFICE_PERFILES */
		$strSQL = " INSERT INTO ";
		$strSQL .= "	BACKOFFICE_PERFILES ";
		$strSQL .= "		(DES_PERFIL, ";
		$strSQL .= "		DES_DESCRIPCION) ";
		$strSQL .= "	VALUES";
		$strSQL .= "		('$strPerfil', ";
		$strSQL .= "		'$strDescripcion') ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->intPerfil = mysql_insert_id();
		/* Grabo las secciones disponibles para el perfil */
		$this->updateSecciones($this->intPerfil, $arrAccesoTotal, $arrSoloLectura);

		return true;
	}

	/* Updetea un usuario en la Base de Datos */
	function updatePerfil($intPerfil, $strPerfil, $strPerfilAnterior, $strDescripcion, $arrAccesoTotal, $arrSoloLectura){

		$this->chequearPerfil($strPerfil, $strPerfilAnterior, $strDescripcion, $arrAccesoTotal, $arrSoloLectura, false);

		if ($this->intErrores)
			return false;

		/* Corrigo Texto Entrante */
		$strPerfil = stringToSQL(strToLower($strPerfil));
		$strDescripcion = stringToSQL(capitalizeFirst($strDescripcion));

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "		BACKOFFICE_PERFILES ";
		$strSQL .= "	SET ";
		$strSQL .= "		DES_PERFIL = '$strPerfil', ";
		$strSQL .= "		DES_DESCRIPCION = '$strDescripcion' ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_PERFIL = $intPerfil";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;
		$objQuery->query($strSQL);

		/* Grabo las secciones disponibles para el perfil */
		$this->updateSecciones($intPerfil, $arrAccesoTotal, $arrSoloLectura);
		return true;
	}

	function deletePerfil($intPerfil){

		/* Chequeo si tiene usuarios asignados */
		$strSQL = " SELECT ";
		$strSQL .= "		COD_USUARIO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		BACKOFFICE_USUARIOS ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_PERFIL = $intPerfil";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;
		$objQuery->query($strSQL);

		if (!$objQuery->Row){
			$strSQL = " DELETE FROM ";
			$strSQL .= "		BACKOFFICE_PERFILES_SECCIONES ";
			$strSQL .= "	WHERE ";
			$strSQL .= "		COD_PERFIL = $intPerfil";

			$objQuery->query($strSQL);

			$strSQL = " DELETE FROM ";
			$strSQL .= "		BACKOFFICE_PERFILES ";
			$strSQL .= "	WHERE ";
			$strSQL .= "		COD_PERFIL = $intPerfil";

			$objQuery->query($strSQL);

			return true;
		}else
			return false;
	}

	function updateSecciones($intPerfil, $arrAccesoTotal, $arrSoloLectura){

		/* Borro todos los registros anteriores */
		$strSQL = " DELETE FROM ";
		$strSQL .= "		BACKOFFICE_PERFILES_SECCIONES ";
		$strSQL .= "	WHERE  ";
		$strSQL .= "		COD_PERFIL = $intPerfil";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;
		$objQuery->query($strSQL);

		if ($arrAccesoTotal){
			for ($i = 0; $i < sizeOf($arrAccesoTotal); $i++){
				/* Escribo SQL */
				$strSQL = " INSERT INTO";
				$strSQL .= "	BACKOFFICE_PERFILES_SECCIONES ";
				$strSQL .= "			(COD_PERFIL, COD_SECCION, COD_ACCESO) ";
				$strSQL .= "		VALUES ";
				$strSQL .= "			($intPerfil, $arrAccesoTotal[$i], 2) ";

				$objQuery->query($strSQL);
			}
		}

		if ($arrSoloLectura){
			for ($i = 0; $i < sizeOf($arrSoloLectura); $i++){
				/* Escribo SQL */
				$strSQL = " INSERT INTO";
				$strSQL .= "	BACKOFFICE_PERFILES_SECCIONES ";
				$strSQL .= "			(COD_PERFIL, COD_SECCION, COD_ACCESO) ";
				$strSQL .= "		VALUES ";
				$strSQL .= "			($intPerfil, $arrSoloLectura[$i], 1) ";

				$objQuery->query($strSQL);
			}
		}
	}

	/* Chequea que un Perfil exista en Base de Datos */
	function checkPerfil($strPerfil){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		DES_PERFIL ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_PERFILES ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		DES_PERFIL = '$strPerfil'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Devuelve todos los perfiles */
	function getPerfiles($arrPerfiles = "", $arrSecciones = "", $blnAgrupadosPorUsuario = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		if ($blnAgrupadosPorUsuario)
			$strSQL .= "		SUM(IF(u.COD_USUARIO IS NOT NULL, 1, 0)) AS NUM_USUARIOS, ";
		else
		$strSQL .= "		u.COD_USUARIO, ";
		$strSQL .= "		p.COD_PERFIL, ";
		$strSQL .= "		p.DES_PERFIL, ";
		$strSQL .= "		p.DES_DESCRIPCION ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_PERFILES p ";
		$strSQL .= "		LEFT OUTER JOIN BACKOFFICE_USUARIOS u ";
		$strSQL .= "		ON (p.COD_PERFIL = u.COD_PERFIL) ";

		if ($arrPerfiles){
			$strSQL .= "	WHERE ";
			if (is_array($arrPerfiles)){
				$strSQL .= "		p.COD_PERFIL = IN (";
				for ($i = 0; $i < sizeOf($arrPerfiles); $i++){
					$strSQL .= "			$arrPerfiles[$i] ";
					$strSQL .= ($i != (sizeOf($arrPerfiles) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		p.COD_PERFIL = '$arrPerfiles'";
		}


		if ($arrSecciones){
			if ($arrPerfiles)
				$strSQL .= "	AND ";
			else
				$strSQL .= "	WHERE ";
			if (is_array($arrSecciones)){
				$strSQL .= "		s.COD_SECCION = IN (";
				for ($i = 0; $i < sizeOf($arrSecciones); $i++){
					$strSQL .= "			$arrSecciones[$i] ";
					$strSQL .= ($i != (sizeOf($arrSecciones) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		s.COD_SECCION = '$arrSecciones'";
		}

		if ($blnAgrupadosPorUsuario){
			$strSQL .= "	GROUP BY ";
			$strSQL .= "		p.COD_PERFIL";
		}

		$strSQL .= "	ORDER BY p.DES_PERFIL";

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

	function getPerfilesRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intPerfil = $this->arrRecord[$intNumRecord]["COD_PERFIL"];
			$this->strPerfil = $this->arrRecord[$intNumRecord]["DES_PERFIL"];
			$this->strDescripcion = isset($this->arrRecord[$intNumRecord]["DES_DESCRIPCION"]) ? $this->arrRecord[$intNumRecord]["DES_DESCRIPCION"] : "";
			$this->intUsuarios = isset($this->arrRecord[$intNumRecord]["NUM_USUARIOS"]) ? $this->arrRecord[$intNumRecord]["NUM_USUARIOS"] : "";
			$this->strUsuario = isset($this->arrRecord[$intNumRecord]["COD_USUARIO"]) ? $this->arrRecord[$intNumRecord]["COD_USUARIO"] : "";
			$this->intSeccion = isset($this->arrRecord[$intNumRecord]["COD_SECCION"]) ? $this->arrRecord[$intNumRecord]["COD_SECCION"] : "";
			$this->strSeccion = isset($this->arrRecord[$intNumRecord]["DES_SECCION"]) ? $this->arrRecord[$intNumRecord]["DES_SECCION"] : "";
			$this->intAcceso = isset($this->arrRecord[$intNumRecord]["COD_ACCESO"]) ? $this->arrRecord[$intNumRecord]["COD_ACCESO"] : "";
			return true;
		} else
			return false;
	}

	function getSecciones($intPerfil, $blnOrder = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		p.COD_PERFIL, p.DES_PERFIL, ";
		$strSQL .= "		s.COD_SECCION, s.DES_SECCION, ps.COD_ACCESO ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_SECCIONES s ";
		$strSQL .= "		LEFT OUTER JOIN BACKOFFICE_PERFILES p ";
		$strSQL .= "			ON (p.COD_PERFIL = '$intPerfil') ";
		$strSQL .= "		LEFT OUTER JOIN BACKOFFICE_PERFILES_SECCIONES ps ";
		$strSQL .= "			ON (p.COD_PERFIL = ps.COD_PERFIL ";
		$strSQL .= "			AND s.COD_SECCION = ps.COD_SECCION) ";

		/* Ordeno por Codigo o Alfabeticamente */
		if ($blnOrder)
			$strSQL .= "	ORDER BY ps.COD_ACCESO, ps.COD_SECCION ";
		else
			$strSQL .= "	ORDER BY ps.COD_ACCESO, s.DES_SECCION ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;

		$objQuery->query($strSQL);
		$this->intTotal = $objQuery->Row;
		$this->arrRecord = $objQuery->Record;

		return $objQuery->Row;
	}
}
?>