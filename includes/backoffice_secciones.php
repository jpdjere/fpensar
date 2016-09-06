<?php

/****************************************************************************
 Clase clsBackOfficeSecciones: Clase de Secciones de BackOffice
****************************************************************************/

class clsBackOfficeSecciones {

	var $intSeccion;
	var $strSeccion;
	var $strLink;
	var $strIcono;
	var $intOrden;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	var $arrTiposCarpetas = array(
		"users" => "users",
		"dashboard" => "dash",
		"infrastructure" => "infra",
		"alerts" => "alerts",
		"reports" => "reports",
		"news" => "news",
		"mail" => "mail",
		"stats" => "stats",
		"config" => "config",
		"communication" => "comm",
		"help" => "help",
		"logout" => "logout"
	);

	function getSecciones($intSeccion = false, $strUsuario = false){
		$intSeccion = intval($intSeccion);
		$strUsuario = stringToSQL($strUsuario);

		$strSQL = " SELECT ";
		$strSQL .= "		s.COD_SECCION, ";
		$strSQL .= "		s.DES_SECCION, ";
		$strSQL .= "		s.DES_LINK, ";
		$strSQL .= "		s.DES_ICONO, ";
		$strSQL .= "		s.NUM_ORDEN, ";
		$strSQL .= "		p.COD_PERFIL, ";
		$strSQL .= "		p.COD_ACCESO ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_SECCIONES s ";
		$strSQL .= "			LEFT OUTER JOIN BACKOFFICE_PERFILES_SECCIONES p ";
		$strSQL .= "				ON ( ";
		$strSQL .= "					s.COD_SECCION = p.COD_SECCION ";
		$strSQL .= "					AND p.COD_PERFIL = (SELECT COD_PERFIL FROM BACKOFFICE_USUARIOS WHERE COD_USUARIO = 'administrador' AND FLG_HABILITADO = 'S') ";
		$strSQL .= "				) ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";
		if ($intSeccion){
			$strSQL .= "		AND s.COD_SECCION = $intSeccion ";
		}

		$strSQL .= "		ORDER BY s.NUM_ORDEN ASC ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$this->intTotal = $objQuery->Row;
		$this->arrRecord = $objQuery->Record;

		return $this->intTotal;
	}

	function getSeccionesRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intSeccion = $this->arrRecord[$intNumRecord]["COD_SECCION"];
			$this->strSeccion = $this->arrRecord[$intNumRecord]["DES_SECCION"];
			$this->strLink = $this->arrRecord[$intNumRecord]["DES_LINK"];
			$this->strIcono = $this->arrRecord[$intNumRecord]["DES_ICONO"];
			$this->intOrden = $this->arrRecord[$intNumRecord]["NUM_ORDEN"];
			$this->intPerfil = $this->arrRecord[$intNumRecord]["COD_PERFIL"];
			$this->intAcceso = $this->arrRecord[$intNumRecord]["COD_ACCESO"];
			return true;
		} else
			return false;
	}

}
?>