<?php

/****************************************************************************
* Clase clsUsuarios: Clase de Usuarios                                      *
****************************************************************************/

class clsBackOfficeUsuarios {

	var $strUsuario;
	var $intPerfil;
	var $strPerfil;
	var $strDescripcion;
	var $strApellido;
	var $strNombre;
	var $strEmail;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;
	var $errorUsuario;
	var $intAcceso;

	/* Chequeo el Usuario a Insertar o Updetear */
	function chequearUsuario($strUsuario, $strContrasenia, $strContraseniaConfirmacion, $intPerfil, $strNombre, $strApellido, $strDescripcion, $strEmail, $blnNuevoUsuario){

		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		/* Chequeo errores */
		$objCheck->arrErrors["strUsuario"] = "";
		if ($blnNuevoUsuario){
			$objCheck->checkUser($strUsuario, 3, 32, "strUsuario");
			$objCheck->checkPassword($strContrasenia, $strContraseniaConfirmacion, 4, 32, "strContrasenia", "strContraseniaConfirmacion");
		}
		$objCheck->checkNumber($intPerfil, 1, 10, "intPerfil");
		$objCheck->checkString($strNombre, 3, 50, "strNombre");
		$objCheck->checkString($strApellido, 3, 50, "strApellido");
		if ($strDescripcion)
			$objCheck->checkString($strDescripcion, 3, 255, "strDescripcion");
		$objCheck->checkEmail($strEmail, 10, 50, "strEmail");

		if ($blnNuevoUsuario){
			$this->errorUsuario = (isset($objCheck->arrErrors["strUsuario"])) ? $objCheck->arrErrors["strUsuario"] : "";
			$this->errorContrasenia = (isset($objCheck->arrErrors["strContrasenia"])) ? $objCheck->arrErrors["strContrasenia"] : "";
			$this->errorContraseniaConfirmacion = (isset($objCheck->arrErrors["strContraseniaConfirmacion"])) ? $objCheck->arrErrors["strContraseniaConfirmacion"] : "";
		}
		$this->errorPerfil = (isset($objCheck->arrErrors["intPerfil"])) ? $objCheck->arrErrors["intPerfil"] : "";
		$this->errorNombre = (isset($objCheck->arrErrors["strNombre"])) ? $objCheck->arrErrors["strNombre"] : "";
		$this->errorApellido = (isset($objCheck->arrErrors["strApellido"])) ? $objCheck->arrErrors["strApellido"] : "";
		if ($strDescripcion)
			$this->errorDescripcion = (isset($objCheck->arrErrors["strDescripcion"])) ? $objCheck->arrErrors["strDescripcion"] : "";
		else
			$this->errorDescripcion = "";
		$this->errorEmail = (isset($objCheck->arrErrors["strEmail"])) ? $objCheck->arrErrors["strEmail"] : "";

		if ($blnNuevoUsuario && !$objCheck->arrErrors["strUsuario"]){
			if ($this->checkUsuario($strUsuario, "", false))
				$this->errorUsuario = "El usuario ya se encuentra registrado";
		}

		$this->intErrores = $objCheck->errorsCount;
	}

	/* Inserta un Usuario en la Base de Datos */
	function insertUsuario($strUsuario, $strContrasenia, $strContraseniaConfirmacion, $intPerfil, 
					$strNombre, $strApellido, $strDescripcion, $strEmail, $blnEstado){

		$this->chequearUsuario($strUsuario, $strContrasenia, $strContraseniaConfirmacion, $intPerfil, 
							$strNombre, $strApellido, $strDescripcion, $strEmail, true);

		if ($this->intErrores)
			return false;

		/* Corrigo Texto Entrante */
		$strUsuario = stringToSQL(strToLower($strUsuario));
		$strContrasenia = strToLower($strContrasenia);
		$strContraseniaConfirmacion = strToLower($strContraseniaConfirmacion);
		$strNombre = stringToSQL(capitalizeAll($strNombre));
		$strApellido = stringToSQL(capitalizeAll($strApellido));
		$strDescripcion = stringToSQL(capitalizeFirst($strDescripcion));
		$strEmail = strToLower($strEmail);

		/* Inserto el Usuario en la tabla BACKOFFICE_USUARIOS */
		$strSQL = " INSERT INTO ";
		$strSQL .= "	BACKOFFICE_USUARIOS ";
		$strSQL .= "		(COD_USUARIO, ";
		$strSQL .= "		COD_PERFIL, ";
		$strSQL .= "		DES_DESCRIPCION, ";
		$strSQL .= "		DES_NOMBRE, ";
		$strSQL .= "		DES_APELLIDO, ";
		$strSQL .= "		DES_EMAIL, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO) ";
		$strSQL .= "	VALUES";
		$strSQL .= "		('$strUsuario', ";
		$strSQL .= "		$intPerfil, ";
		$strSQL .= "		'$strDescripcion', ";
		$strSQL .= "		'$strNombre', ";
		$strSQL .= "		'$strApellido', ";
		$strSQL .= "		'$strEmail', ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnEstado) ? "S" : "N") . "') ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		/* Inserto el password en la tabla BACKOFFICE_USUARIOS_CONTRASENIAS */
		$strSQL = " INSERT INTO ";
		$strSQL .= "	BACKOFFICE_USUARIOS_CONTRASENIA ";
		$strSQL .= "		(COD_USUARIO, ";
		$strSQL .= "		DES_CONTRASENIA) ";
		$strSQL .= "	VALUES";
		$strSQL .= "		('$strUsuario', ";
		$strSQL .= "		'" . md5($strContrasenia) . "')";

		$objQuery->query($strSQL);

		return true;
	}

	/* Updetea un Usuario en la Base de Datos */
	function updateUsuario($strUsuario, $intPerfil, $strNombre, $strApellido, $strDescripcion, $strEmail, $blnEstado){

		$this->chequearUsuario($strUsuario, false, false, $intPerfil, $strNombre, $strApellido, 
								$strDescripcion, $strEmail, false);

		if ($this->intErrores)
			return false;

		/* Corrigo Texto Entrante */
		$strUsuario = stringToSQL(strToLower($strUsuario));
		$strNombre = stringToSQL(capitalizeAll($strNombre));
		$strApellido = stringToSQL(capitalizeAll($strApellido));
		$strDescripcion = stringToSQL(capitalizeFirst($strDescripcion));
		$strEmail = stringToSQL(strToLower($strEmail));

		$strSQL = " UPDATE ";
		$strSQL .= "		BACKOFFICE_USUARIOS ";
		$strSQL .= "	SET ";
		$strSQL .= "		COD_PERFIL = $intPerfil, ";
		$strSQL .= "		DES_NOMBRE = '$strNombre', ";
		$strSQL .= "		DES_APELLIDO = '$strApellido', ";
		$strSQL .= "		DES_DESCRIPCION = '$strDescripcion', ";
		$strSQL .= "		DES_EMAIL = '$strEmail', ";
		$strSQL .= "		FLG_HABILITADO = '" . (($blnEstado) ? "S" : "N") . "', ";
		$strSQL .= "		FEC_FECHA_MODIFICACION = SYSDATE() ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_USUARIO = '$strUsuario'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Modifica la Contrasenia del Usuario */
	function updateContrasenia($strUsuario, $strContrasenia, $strContraseniaConfirmacion){

		/* Corrigo Texto Entrante */
		$strContrasenia = strToLower($strContrasenia);
		$strContraseniaConfirmacion = strToLower($strContraseniaConfirmacion);

		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkPassword($strContrasenia, $strContraseniaConfirmacion, 6, 32, "strContrasenia", "strContraseniaConfirmacion");
		$this->errorContrasenia = (isset($objCheck->arrErrors["strContrasenia"])) ? $objCheck->arrErrors["strContrasenia"] : "";
		$this->errorContraseniaConfirmacion = (isset($objCheck->arrErrors["strContraseniaConfirmacion"])) ? $objCheck->arrErrors["strContraseniaConfirmacion"] : "";

		$this->intErrors = $objCheck->errorsCount;

		if ($this->intErrors)
			return false;

		$strSQL = " UPDATE ";
		$strSQL .= "		BACKOFFICE_USUARIOS_CONTRASENIA ";
		$strSQL .= "	SET ";
		$strSQL .= "		DES_CONTRASENIA = '" . md5($strContrasenia) . "' ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_USUARIO = '$strUsuario' ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra un usuario de la Base de Datos que no sea el usuario actual */
	function deleteUsuario($strUsuario){

		/* Chequeo si el usuario a borrar es el usuario actual */
		global $_SESSION;
		if ($_SESSION["strUsuarioBackoffice"] == $strUsuario)
			return false;

		$this->getUsuarios($strUsuario);

		/* Borro el usuario de la tabla de Contrasenias */
		$strSQL = "DELETE FROM ";
		$strSQL .= "		BACKOFFICE_USUARIOS_CONTRASENIA";
		$strSQL .= "	WHERE";
		$strSQL .= "		COD_USUARIO = '$strUsuario'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		/* Borro el usuario de la tabla de Usuarios */
		$strSQL = "DELETE FROM ";
		$strSQL .= "		BACKOFFICE_USUARIOS";
		$strSQL .= "	WHERE";
		$strSQL .= "		COD_USUARIO = '$strUsuario'";

		$objQuery->query($strSQL);
	}

	/* Cambia el estado de un Usuario que no sea el usuario actual */
	function setEstado($strUsuario, $blnHabilitado = false){

		/* Chequeo si el usuario a modificar es el usuario actual */
		global $_SESSION;
		if ($_SESSION["strUsuarioBackoffice"] == $strUsuario)
			return false;

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	BACKOFFICE_USUARIOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_USUARIO = '$strUsuario'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Chequea que un Usuario-Password exista en Base de Datos */
	function checkUsuario($strUsuario, $strContrasenia, $blnCheckContrasenia = true){

		/* Corrigo Texto Entrante */
		$strUsuario = strToLower($strUsuario);
		$strContrasenia = strToLower($strContrasenia);

		$strSQL = " SELECT ";
		$strSQL .= "		u.COD_USUARIO, ";
		$strSQL .= "		c.DES_CONTRASENIA";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_USUARIOS u, ";
		$strSQL .= "		BACKOFFICE_USUARIOS_CONTRASENIA c ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		u.COD_USUARIO = c.COD_USUARIO";
		$strSQL .= "		AND u.COD_USUARIO = '$strUsuario'";
		$strSQL .= "		AND u.FLG_HABILITADO = 'S'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		if ($objQuery->Row){
			if ($blnCheckContrasenia){
				if ($objQuery->Record[0]["DES_CONTRASENIA"] == md5($strContrasenia))
					return true;
				else
					$this->errorUsuarioLogin = "La contraseña es incorrecta";
			}else
				return true;
		}else{
			$this->errorUsuarioLogin = "El usuario ingresado no existe";
			return false;
		}
	}

	/* Chequea que si el usuario logueado tiene acceso a una seccion */
	function checkUsuarioLogeado($strUsuario, $intSeccion){
		$strSQL = " SELECT ";
		$strSQL .= "		u.COD_USUARIO, ";
		$strSQL .= "		p.COD_PERFIL, ";
		$strSQL .= "		p.COD_ACCESO";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_USUARIOS u, ";
		$strSQL .= "		BACKOFFICE_PERFILES_SECCIONES p ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		u.COD_PERFIL = p.COD_PERFIL";
		$strSQL .= "		AND u.COD_USUARIO = '$strUsuario'";
		$strSQL .= "		AND p.COD_SECCION = $intSeccion";
		$strSQL .= "		AND FLG_HABILITADO = 'S'";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;

		$objQuery->query($strSQL);

		if ($objQuery->Row)
			$this->intAcceso = $objQuery->Record[0]["COD_ACCESO"];

		return $objQuery->Row;
	}

	/* Devuelve todos los permisos de ese usuario */
	function getPermisos($strUsuario){
		$strSQL = " SELECT ";
		$strSQL .= "		p.COD_SECCION";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_USUARIOS u, ";
		$strSQL .= "		BACKOFFICE_PERFILES_SECCIONES p ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		u.COD_PERFIL = p.COD_PERFIL";
		$strSQL .= "		AND u.COD_USUARIO = '$strUsuario'";
		$strSQL .= "		AND p.COD_ACCESO != 0";
		$strSQL .= "		AND FLG_HABILITADO = 'S'";
		$strSQL .= "	ORDER BY p.COD_SECCION";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;

		$objQuery->query($strSQL);

		$this->arrRecord = $objQuery->Record;
		$this->intTotal = $objQuery->Row;
	}

	function getPermisosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intSeccion = $this->arrRecord[$intNumRecord]["COD_SECCION"];
			return true;
		} else
			return false;
	}

	function getUsuarios($arrUsuarios = false, $arrPermisos = false){
		$strSQL = " SELECT ";
		$strSQL .= "		u.COD_USUARIO, ";
		$strSQL .= "		u.DES_DESCRIPCION, ";
		$strSQL .= "		u.DES_APELLIDO, ";
		$strSQL .= "		u.DES_NOMBRE, ";
		$strSQL .= "		DATE_FORMAT(u.FEC_FECHA_ALTA, '%d/%m/%Y %H:%i:%s') AS FEC_FECHA_ALTA, ";
		$strSQL .= "		DATE_FORMAT(u.FEC_FECHA_MODIFICACION, '%d/%m/%Y %H:%i:%s') AS FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		u.DES_EMAIL, ";
		$strSQL .= "		u.FLG_HABILITADO, ";
		$strSQL .= "		p.COD_PERFIL, ";
		$strSQL .= "		p.DES_PERFIL ";
		$strSQL .= "	FROM  ";
		$strSQL .= "		BACKOFFICE_USUARIOS u, ";
		$strSQL .= "		BACKOFFICE_PERFILES p ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		u.COD_PERFIL = p.COD_PERFIL ";

		if ($arrUsuarios){
			if (is_array($arrUsuarios)){
				$strSQL .= "		AND u.COD_USUARIO = IN (";
				for ($i = 0; $i < sizeOf($arrUsuarios); $i++){
					$strSQL .= "			$arrUsuarios[$i] ";
					$strSQL .= ($i != (sizeOf($arrUsuarios) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND u.COD_USUARIO = '$arrUsuarios'";
		}

		if ($arrPermisos){
			if (is_array($arrPermisos)){
				$strSQL .= "		AND p.COD_PERFIL = IN (";
				for ($i = 0; $i < sizeOf($arrPermisos); $i++){
					$strSQL .= "			$arrPermisos[$i] ";
					$strSQL .= ($i != (sizeOf($arrPermisos) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND p.COD_PERFIL = '$arrPermisos'";
		}

		if ($arrPermisos)
			$strSQL .= "		ORDER BY p.DES_PERFIL, u.COD_USUARIO";
		else
			$strSQL .= "		ORDER BY u.COD_USUARIO";

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

	function getUsuariosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->strUsuario = $this->arrRecord[$intNumRecord]["COD_USUARIO"];
			$this->intPerfil = $this->arrRecord[$intNumRecord]["COD_PERFIL"];
			$this->strPerfil = $this->arrRecord[$intNumRecord]["DES_PERFIL"];
			$this->strDescripcion = $this->arrRecord[$intNumRecord]["DES_DESCRIPCION"];
			$this->strApellido = $this->arrRecord[$intNumRecord]["DES_APELLIDO"];
			$this->strNombre = $this->arrRecord[$intNumRecord]["DES_NOMBRE"];
			$this->strEmail = $this->arrRecord[$intNumRecord]["DES_EMAIL"];
			$this->strFechaAlta = $this->arrRecord[$intNumRecord]["FEC_FECHA_ALTA"];
			$this->strFechaModificacion = $this->arrRecord[$intNumRecord]["FEC_FECHA_MODIFICACION"];
			$this->blnHabilitado = ($this->arrRecord[$intNumRecord]["FLG_HABILITADO"] == "S") ? true : false;
			return true;
		} else
			return false;
	}

}
?>