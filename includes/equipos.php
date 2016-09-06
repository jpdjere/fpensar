<?php

/****************************************************************************
* Class clsEquipos: Clase de Equipos                                    *
****************************************************************************/

class clsEquipos {

	var $intEquipo;
	var $strNombre;
	var $strCargo;
	var $strTexto;
	var $strImagen;
	var $intGrupo;
	var $strUsuarioTwitter;
	var $strFacebookURL;
	var $strTwitterURL;
	var $strFechaAlta;
	var $strFechaModificacion;
	var $blnHabilitado;

	var $errorImagen;

	var $arrRecord;
	var $intErrores = 0;
	var $intTotal = 0;

	var $arrGrupos = array("Equipos Técnicos", "Consejo de Administración");

	/* Chequeo una Equipo a subir */
	function chequearEquipo($strNombre, $strCargo, $strTexto, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strNombre, 3, 100, "strNombre");
		$objCheck->checkString($strCargo, 3, 100, "strCargo");
		$objCheck->checkAnyText($strTexto, 10, 500, "strTexto");
		$objCheck->checkCombo($intGrupo, "intGrupo");
		if ($strUsuarioTwitter)
			$objCheck->checkString($strUsuarioTwitter, 3, 50, "strUsuarioTwitter");
		if ($strFacebookURL)
			$objCheck->checkURL($strFacebookURL, 3, 100, "strFacebookURL");
		if ($strTwitterURL)
			$objCheck->checkURL($strTwitterURL, 3, 100, "strTwitterURL");

		$this->errorNombre = (isset($objCheck->arrErrors["strNombre"])) ? $objCheck->arrErrors["strNombre"] : "";
		$this->errorCargo = (isset($objCheck->arrErrors["strCargo"])) ? $objCheck->arrErrors["strCargo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorGrupo = (isset($objCheck->arrErrors["intGrupo"])) ? $objCheck->arrErrors["intGrupo"] : "";
		$this->errorUsuarioTwitter = (isset($objCheck->arrErrors["strUsuarioTwitter"])) ? $objCheck->arrErrors["strUsuarioTwitter"] : "";
		$this->errorFacebookURL = (isset($objCheck->arrErrors["strFacebookURL"])) ? $objCheck->arrErrors["strFacebookURL"] : "";
		$this->errorTwitterURL = (isset($objCheck->arrErrors["strTwitterURL"])) ? $objCheck->arrErrors["strTwitterURL"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagen($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen){
			$strImageName = md5(microtime());
			$this->strImagen = resizeImage(PATH_IMAGEN_EQUIPOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_EQUIPOS_GRANDE_ANCHO, IMAGEN_EQUIPOS_GRANDE_ALTO, $strImageName . ".jpg");
			$this->strImagenChica = resizeImage(PATH_IMAGEN_EQUIPOS_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_EQUIPOS_CHICA_ANCHO, IMAGEN_EQUIPOS_CHICA_ALTO, $this->strImagen);
			if (!$this->strImagen){
				if ($strImagenAnterior != IMAGEN_NO_DISPONIBLE){
					$this->strImagen = $strImagenAnterior;
				}else{
					$this->errorImagen = "La imagen no ha podido ser procesada";
					$this->intErrores++;
				}
			}else{
				// Recorto la imagen para darle la forma circular
				global $arrImageTypes;
				$strImageURL = "../" . PATH_IMAGEN_EQUIPOS . "/" . $this->strImagen;
				list($intImageWidth, $intImageHeight, $intImageType) = getImageSize($strImageURL);
				switch($arrImageTypes[$intImageType]){
					case "GIF":
						$objImageSource = imageCreateFromGIF($strImageURL);
						break;
					case "JPG":
						$objImageSource = imageCreateFromJPEG($strImageURL);
						break;
					case "PNG":
						$objImageSource = imageCreateFromPNG($strImageURL);
						imageantialias($objImageSource, true);
						break;
				}

				if ($objImageSource){
					$objImageMask = imageCreateFromPNG("../" . PATH_IMAGEN_EQUIPOS . "/_mask.png");
					$objImageBorder = imageCreateFromPNG("../" . PATH_IMAGEN_EQUIPOS . "/_border.png");
					$objImageSource = applyMaskToImage($objImageSource, $objImageMask);
					imagecopy($objImageSource, $objImageBorder, 0, 0, 0, 0, 130, 130);

					$objImageShadow = imageCreateFromPNG("../" . PATH_IMAGEN_EQUIPOS . "/_shadow.png");
					imagesavealpha($objImageShadow, true);
					imagecopy($objImageShadow, $objImageSource, 0, 0, 0, 0, 130, 130);
					imagePNG($objImageShadow, "../" . PATH_IMAGEN_EQUIPOS . "/" . $strImageName . ".png", 0);
					$this->strImagen = $strImageName . ".png";

					// Resizeo la imagen Chica
					$arrNewFoto = array(
						"name" => $strImageName . ".png",
						"type" => "image/png",
						"tmp_name" => realpath("../") . "/" . PATH_IMAGEN_EQUIPOS . "/" . $strImageName . ".png"
					);
					$this->strImagenChica = resizeImage(PATH_IMAGEN_EQUIPOS_CHICA_LOCAL, $arrNewFoto, "", IMAGEN_EQUIPOS_CHICA_ANCHO, IMAGEN_EQUIPOS_CHICA_ALTO, $this->strImagen);
				}
			}
		}else if ($strImagenAnterior != IMAGEN_NO_DISPONIBLE){
			$this->strImagen = $strImagenAnterior;
		}else{
			$this->errorImagen = "Debe elegir una imagen";
			$this->intErrores++;
		}
	}

	/* Inserta una Equipo en la Tabla EQUIPOS */
	function insertEquipo($strNombre, $strCargo, $strTexto, $strImagen, $strImagenAnterior, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL, $blnHabilitado){

		$this->chequearEquipo($strNombre, $strCargo, $strTexto, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strNombre = stringToSQL(capitalizeFirst($strNombre));
		$strCargo = stringToSQL(capitalizeFirst($strCargo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$intGrupo = intval($intGrupo);
		$strUsuarioTwitter = stringToSQL($strUsuarioTwitter);
		$strFacebookURL = stringToSQL($strFacebookURL);
		$strTwitterURL = stringToSQL($strTwitterURL);

		/* Me fijo el último orden de esta tabla */
		$strSQL = " SELECT ";
		$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		EQUIPOS ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = ($objQuery->Row) ? ($objQuery->Record[0]["NUM_ORDEN"] + 1) : 1;

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	EQUIPOS";
		$strSQL .= "		(DES_NOMBRE, ";
		$strSQL .= "		DES_CARGO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		COD_GRUPO, ";
		$strSQL .= "		DES_USUARIO_TWITTER, ";
		$strSQL .= "		DES_FACEBOOK_URL, ";
		$strSQL .= "		DES_TWITTER_URL, ";
		$strSQL .= "		NUM_ORDEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strNombre', ";
		$strSQL .= "		'$strCargo', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'" . $this->strImagen . "', ";
		$strSQL .= "		$intGrupo, ";
		$strSQL .= "		'$strUsuarioTwitter', ";
		$strSQL .= "		'$strFacebookURL', ";
		$strSQL .= "		'$strTwitterURL', ";
		$strSQL .= "		$intOrden, ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		SYSDATE(), ";
		$strSQL .= "		'" . (($blnHabilitado) ? "S" : "N") . "')";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

		$this->intEquipo = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Equipo */
	function updateEquipo($intEquipo, $strNombre, $strCargo, $strTexto, $strImagen, $strImagenAnterior, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL, $blnHabilitado){
		$intEquipo = intval($intEquipo);

		$this->chequearEquipo($strNombre, $strCargo, $strTexto, $intGrupo, $strUsuarioTwitter, $strFacebookURL, $strTwitterURL);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strNombre = stringToSQL(capitalizeFirst($strNombre));
		$strCargo = stringToSQL(capitalizeFirst($strCargo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$intGrupo = intval($intGrupo);
		$strUsuarioTwitter = stringToSQL($strUsuarioTwitter);
		$strFacebookURL = stringToSQL($strFacebookURL);
		$strTwitterURL = stringToSQL($strTwitterURL);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	EQUIPOS";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_NOMBRE = '$strNombre', ";
		$strSQL .= "			DES_CARGO = '$strCargo', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			COD_GRUPO = $intGrupo, ";
		$strSQL .= "			DES_USUARIO_TWITTER = '$strUsuarioTwitter', ";
		$strSQL .= "			DES_FACEBOOK_URL = '$strFacebookURL', ";
		$strSQL .= "			DES_TWITTER_URL = '$strTwitterURL', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_EQUIPO = $intEquipo";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Equipo de la Tabla Equipos */
	function deleteEquipo($intEquipo){
		$intEquipo = intval($intEquipo);

		/* Borro la tabla EQUIPOS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	EQUIPOS ";
		$strSQL .= "		WHERE COD_EQUIPO = $intEquipo";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intEquipo, $blnQualify = false){
		$intEquipo = intval($intEquipo);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	EQUIPOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_EQUIPO = $intEquipo";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	// Ordenar Equipos
	function orderEquipo($intEquipo, $blnOrden){
		$intEquipo = intval($intEquipo);
		if (!$intEquipo)
			return false;

		/* Obtengo el orden de la equipo actual */
		$strSQL = " SELECT ";
		$strSQL .= " 		NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		EQUIPOS ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_EQUIPO = $intEquipo ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		$intOrden = $objQuery->Record[0]["NUM_ORDEN"];

		/* Obtengo el orden de la equipo a actualizar */
		$strSQL = " SELECT ";
		if ($blnOrden > 1 || $blnOrden == -1)
			$strSQL .= " 		MAX(NUM_ORDEN) AS NUM_ORDEN";
		else
			$strSQL .= " 		MIN(NUM_ORDEN) AS NUM_ORDEN ";
		$strSQL .= " 	FROM ";
		$strSQL .= " 		EQUIPOS ";
		$strSQL .= "	WHERE ";
		if ($blnOrden == -1 || $blnOrden == -2)
			$strSQL .= " 		NUM_ORDEN < $intOrden ";
		else if ($blnOrden == 1 || $blnOrden == 2)
			$strSQL .= " 		NUM_ORDEN > $intOrden ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);
		$intOrdenNuevo = $objQuery->Record[0]["NUM_ORDEN"];

		if ($blnOrden == 1 || $blnOrden == -1){
			/* Updeteo el nuevo orden de la equipo a actualizar */
			$strSQL = " UPDATE ";
			$strSQL .= " 		EQUIPOS ";
			$strSQL .= " 	SET ";
			$strSQL .= " 		NUM_ORDEN = $intOrden ";
			$strSQL .= "	WHERE ";
			$strSQL .= "		NUM_ORDEN = $intOrdenNuevo ";
			$objQuery->query($strSQL);
		}else{
			if ($blnOrden == 2){
				for ($i = ($intOrden + 1); $i <= $intOrdenNuevo; $i++){
					/* Updeteo todas las equipos a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		EQUIPOS ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i - 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}else if ($blnOrden == -2){
				for ($i = ($intOrden - 1); $i >= $intOrdenNuevo; $i--){
					/* Updeteo todas las equipos a actualizar */
					$strSQL = " UPDATE ";
					$strSQL .= " 		EQUIPOS ";
					$strSQL .= " 	SET ";
					$strSQL .= " 		NUM_ORDEN = " . ($i + 1) . " ";
					$strSQL .= "	WHERE ";
					$strSQL .= "		NUM_ORDEN = $i ";

					/* Ejecuto SQL */
					$objQuery->query($strSQL);
				}
			}
		}

		/* Updeteo el nuevo orden de la equipo a ordenar */
		$strSQL = " UPDATE ";
		$strSQL .= " 		EQUIPOS ";
		$strSQL .= " 	SET ";
		$strSQL .= " 		NUM_ORDEN = $intOrdenNuevo ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		COD_EQUIPO = $intEquipo ";

		/* Ejecuto SQL */
		$objQuery->query($strSQL);

	}

	/* Levanto los datos de la base */
	function getEquiposTotal($intGrupo = false, $blnBackoffice = false, $arrEquiposToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){
		$intGrupo = intval($intGrupo);
		$strTextoBusqueda = stringToSQL($strTextoBusqueda);
		$intMes = intval($intMes);
		$intAnio = intval($intAnio);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(equipos.COD_EQUIPO) AS NUM_EQUIPOS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		EQUIPOS equipos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($intGrupo){
			$strSQL .= "		AND equipos.COD_GRUPO = $intGrupo ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND equipos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND equipos.FLG_HABILITADO = 'N' ";
		}

		if ($arrEquiposToExclude){
			if (is_array($arrEquiposToExclude)){
				$strSQL .= "		AND equipos.COD_EQUIPO NOT IN (";
				for ($i = 0; $i < sizeOf($arrEquiposToExclude); $i++){
					$strSQL .= "			" . intval($arrEquiposToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrEquiposToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND equipos.COD_EQUIPO <> " . intval($arrEquiposToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (equipos.DES_NOMBRE LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR equipos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			equipos.COD_EQUIPO ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getEquipos($arrEquipos = false, $intGrupo = false, $blnBackoffice = false, $arrEquiposToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){
		$intGrupo = intval($intGrupo);

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		equipos.COD_EQUIPO, ";
		$strSQL .= "		equipos.DES_NOMBRE, ";
		$strSQL .= "		equipos.DES_CARGO, ";
		$strSQL .= "		equipos.DES_TEXTO, ";
		$strSQL .= "		equipos.DES_IMAGEN, ";
		$strSQL .= "		equipos.COD_GRUPO, ";
		$strSQL .= "		equipos.DES_USUARIO_TWITTER, ";
		$strSQL .= "		equipos.DES_FACEBOOK_URL, ";
		$strSQL .= "		equipos.DES_TWITTER_URL, ";
		$strSQL .= "		DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%d/%m/%Y') AS FEC_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		equipos.FEC_FECHA_ALTA, ";
		$strSQL .= "		equipos.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		equipos.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		EQUIPOS equipos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrEquipos){
			if (is_array($arrEquipos)){
				$strSQL .= "		AND equipos.COD_EQUIPO IN (";
				for ($i = 0; $i < sizeOf($arrEquipos); $i++){
					$strSQL .= "			$arrEquipos[$i] ";
					$strSQL .= ($i != (sizeOf($arrEquipos) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND equipos.COD_EQUIPO = $arrEquipos";
		}

		if ($intGrupo){
			$strSQL .= "		AND equipos.COD_GRUPO = $intGrupo ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND equipos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND equipos.FLG_HABILITADO = 'N' ";
		}

		if ($arrEquiposToExclude){
			if (is_array($arrEquiposToExclude)){
				$strSQL .= "		AND equipos.COD_EQUIPO NOT IN (";
				for ($i = 0; $i < sizeOf($arrEquiposToExclude); $i++){
					$strSQL .= "			$arrEquiposToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrEquiposToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND equipos.COD_EQUIPO <> $arrEquiposToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (equipos.DES_NOMBRE LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR equipos.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(equipos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			equipos.COD_EQUIPO ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			equipos.NUM_ORDEN, equipos.FEC_FECHA_ALTA DESC, equipos.DES_NOMBRE ";

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

	function getEquiposRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intEquipo = $this->arrRecord[$intNumRecord]["COD_EQUIPO"];
			$this->strNombre = $this->arrRecord[$intNumRecord]["DES_NOMBRE"];
			$this->strCargo = $this->arrRecord[$intNumRecord]["DES_CARGO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->intGrupo = $this->arrRecord[$intNumRecord]["COD_GRUPO"];
			$this->strGrupo = $this->arrGrupos[$this->arrRecord[$intNumRecord]["COD_GRUPO"] - 1];
			$this->strUsuarioTwitter = $this->arrRecord[$intNumRecord]["DES_USUARIO_TWITTER"];
			$this->strFacebookURL = $this->arrRecord[$intNumRecord]["DES_FACEBOOK_URL"];
			$this->strTwitterURL = $this->arrRecord[$intNumRecord]["DES_TWITTER_URL"];
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