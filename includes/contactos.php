<?php

/****************************************************************************
* Clase clsContactos: Clase de Contactos                                *
****************************************************************************/

class clsContactos {

	var $strContacto;
	var $strNombre;
	var $strApellido;
	var $strEmail;
	var $strLocalidad;
	var $strTelefono;
	var $strAsunto;
	var $strMensaje;
	var $strFecha;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	function insertContacto($strNombre, $strEmail, $strAsunto, $strMensaje){
		$strNombre = stringToSQL($strNombre);
		$strEmail = stringToSQL($strEmail);
		$strAsunto = stringToSQL($strAsunto);
		$strMensaje = stringToSQL($strMensaje);

		// Registro al Usuario
		$strSQL = "INSERT INTO ";
		$strSQL .= "	 CONTACTOS ";
		$strSQL .= "	 	(DES_NOMBRE, ";
		$strSQL .= "	 	DES_EMAIL, ";
		$strSQL .= "	 	DES_ASUNTO, ";
		$strSQL .= "	 	DES_MENSAJE, ";
		$strSQL .= "	 	FEC_FECHA) ";
		$strSQL .= "	 VALUES ";
		$strSQL .= "	 	('" . $strNombre . "', ";
		$strSQL .= "	 	'" . $strEmail . "', ";
		$strSQL .= "	 	'" . $strAsunto . "', ";
		$strSQL .= "	 	'" . $strMensaje . "', ";
		$strSQL .= "	 	SYSDATE()) ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql;
		else
			global $objQuery;

		$objQuery->query($strSQL);

		$this->intContacto = mysql_insert_id();
		return $this->intContacto;
	}

	function getContactos($intContacto = false, $intOrden = false, $intDireccion = false, $strBusqueda = ""){
		$strSQL = " SELECT ";
		$strSQL .= "	 	COD_CONTACTO, ";
		$strSQL .= "	 	DES_NOMBRE, ";
		$strSQL .= "	 	DES_EMAIL, ";
		$strSQL .= "	 	DES_ASUNTO, ";
		$strSQL .= "	 	DES_MENSAJE, ";
		$strSQL .= "	 	FEC_FECHA, ";
		$strSQL .= "	 	DATE_FORMAT(FEC_FECHA, '%d/%m/%Y %H:%i:%s') AS DES_FECHA, ";
		$strSQL .= "	 	DATE_FORMAT(FEC_FECHA, '%d/%m/%Y') DES_FECHA_LISTADO ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		CONTACTOS ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intContacto){
			$strSQL .= "		AND COD_CONTACTO = $intContacto ";
		}

		if ($strBusqueda){
			$strSQL .= "		AND (LOWER(DES_NOMBRE) LIKE '%" . strToLower($strBusqueda) . "%' ";
			$strSQL .= "			OR LOWER(DES_APELLIDO) LIKE '%" . strToLower($strBusqueda) . "%') ";
		}

		$strSQL .= "	ORDER BY ";
		if ($intOrden){
			switch($intOrden){
				case "1":
					$strSQL .= "		DES_NOMBRE ";
					break;
				case "2":
					$strSQL .= "		DES_EMAIL ";
					break;
				case "3":
					$strSQL .= "		FEC_FECHA ";
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
			$strSQL .= "		FEC_FECHA DESC ";
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

	function getContactosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intContacto = $this->arrRecord[$intNumRecord]["COD_CONTACTO"];
			$this->strNombre = $this->arrRecord[$intNumRecord]["DES_NOMBRE"];
			$this->strEmail = $this->arrRecord[$intNumRecord]["DES_EMAIL"];
			$this->strAsunto = $this->arrRecord[$intNumRecord]["DES_ASUNTO"];
			$this->strMensaje = $this->arrRecord[$intNumRecord]["DES_MENSAJE"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["DES_FECHA_LISTADO"];
			$this->strFecha = $this->arrRecord[$intNumRecord]["DES_FECHA"];
			return true;
		} else
			return false;
	}

}

?>