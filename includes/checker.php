<?php

/****************************************************************************
*          Class clsChecker: Clase que maneja Errores en Campos             *
****************************************************************************/

class clsChecker {

	var $arrErrors;
	var $errorsCount = 0;

	var $arrErrorsTextos = array(
		"campo_vacio" => "El campo no debe estar vac&iacute;o",
		"campo_incompleto_1" => "El campo debe contener entre ",
		"campo_incompleto_2" => " y ",
		"campo_incompleto_3" => " caracteres",
		"campo_error" => "El campo posee caracteres inv&aacute;lidos",
		"usuario" => "El campo s&oacute;lo debe contener letras y n&uacute;meros",
		"numero" => "Debe colocar un valor entre {MIN} y {MAX}",
		"email" => "Debe colocar un email v&aacute;lido",
		"url" => "Debe colocar una URL v&aacute;lida",
		"codigo_postal" => "Debe colocar un C&oacute;digo Postal v&aacute;lido",
		"contrasenia" => "Las contrase&ntilde;as deben ser iguales",
		"fecha" => "Debe ingresar una fecha correcta",
		"combo" => "Debe elegir una opci&oacute;n",
		"imagen_vacia" => "Debe elegir una imagen",
		"imagen_error" => "Hubo un error al subir el archivo",
		"imagen_error_tipo" => "No es un archivo de imagen",
		"imagen_error_tipo_flash" => "El archivo debe ser JPEG",
		"imagen_error_tamanio" => "El archivo es demasiado grande ({ACTUAL} Kb.). El m&aacute;ximo es: {MAX} Kb.",
		"imagen_error_ancho_alto" => "El tamaño del archivo es inválido ({ANCHO_ACTUAL} x {ALTO_ACTUAL}).<br> Debe ser de {ANCHO} x {ALTO} p&iacute;xeles.",
		"imagen_error_ancho_alto_variable" => "El tamaño del archivo es inválido ({ANCHO_ACTUAL} x {ALTO_ACTUAL}).<br> Debe estar entre {ANCHO_MIN} x {ALTO_MIN} y {ANCHO_MAX} x {ALTO_MAX} p&iacute;xeles.",
		"youtube_url_vacia" => "Debe colocar una URL válida de YouTube",
		"youtube_url_invalida" => "La URL ingresada no es una URL válida de YouTube",
		"youtube_url_incompleta" => "la URL ingresada de YouTube es incompleta"
	);

	function check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName){
		if (Trim($strString) == ""){
			if ($intMinLength > 0){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["campo_vacio"];
				$this->errorsCount++;
				return false;
			}else
				return true;
		}else if (strLen($strString) > $intMaxLength || strLen($strString) < $intMinLength){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["campo_incompleto_1"] . $intMinLength . $this->arrErrorsTextos["campo_incompleto_2"] . $intMaxLength . $this->arrErrorsTextos["campo_incompleto_3"];
			$this->errorsCount++;
			return false;
		}else if (preg_match($strRegExp, $strString))
			return true;
		else{
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["campo_error"];
			$this->errorsCount++;
			return false;
		}
	}

	function checkNoRegExp($strString, $intMinLength, $intMaxLength, $strFieldName){
		if (Trim($strString) == ""){
			if ($intMinLength > 0){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["campo_vacio"];
				$this->errorsCount++;
				return false;
			}else
				return true;
		}else if (strLen($strString) > $intMaxLength || strLen($strString) < $intMinLength){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["campo_incompleto_1"] . $intMinLength . $this->arrErrorsTextos["campo_incompleto_2"] . $intMaxLength . $this->arrErrorsTextos["campo_incompleto_3"];
			$this->errorsCount++;
			return false;
		}else
			return true;
	}

	function checkString($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[a-zA-Z_0-9\-~,áéíóúñäëïöüàèìòùñÑÁÉÍÓÚÖ´ßã\.:\/\-¡!¿?&$#=+*@ªº%\\\''· °\"“”–—| ()]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkNumber($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[0-9., \-()]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkNumberNoSpaces($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[0-9.,]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkPhoneNumber($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[0-9]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkSpecificNumber($strString, $intMinLength, $intMaxLength, $intMinNumber, $intMaxNumber, $strFieldName){
		$strRegExp = "/^[0-9.,\-]+$/";
		if ($strString >= $intMinNumber && $strString <= $intMaxNumber)
			return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
		else{
			$this->arrErrors[$strFieldName] = str_replace("{MIN}", $intMinNumber, $this->arrErrorsTextos["numero"]);
			$this->arrErrors[$strFieldName] = str_replace("{MAX}", $intMaxNumber, $this->arrErrors[$strFieldName]);
			$this->errorsCount++;
			return false;
		}
	}

	function checkText($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[a-zA-Z_0-9~áéíóúñäëïöüàèìòùñÑÁÉÍÓÚÖ´ß.·,:;\/\-¡!¿?&$#=+*@ªº%\\\''°\"“”–—| ()\r\t\n]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkTextHTML($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[a-zA-Z_0-9~áéíóúñäëïöüàèìòùñÑÁÉÍÓÚÖ´·ß.,:;\/\-¡!¿?&$#=+*@ªº%\\\'°\"“”–—| ()<>\r\t\n]+$/";
		return $this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkAnyText($strString, $intMinLength, $intMaxLength, $strFieldName){
		return $this->checkNoRegExp($strString, $intMinLength, $intMaxLength, $strFieldName);
	}

	function checkEmail($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[0-9a-zA-Z_]([_\.-]?[0-9a-zA-Z_\.-])*@[0-9a-zA-Z][0-9a-zA-Z\.-]*\.[a-zA-Z]{2,4}\.?$/";
		if (!$this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName)) {
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["email"];
			return false;
		} else
			return true;
	}

	/* Valida el Codigo Postal Argentino */
	function checkCPA($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^([A-Z][0-9]{4,4}[A-Z]{3,3})|([0-9]{4,4})$/";
		if (!$this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName))
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["codigo_postal"];
	}

	function checkCodPostal($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[a-zA-Z_0-9]{4,8}$/";
		if (!$this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName))
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["codigo_postal"];
	}

	function checkURL($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie";
		if (!$this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName))
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["url"];
	}

	function checkUser($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strRegExp = "/^[a-zA-Z_0-9 ]+$/";
		if (!$this->check($strString, $strRegExp, $intMinLength, $intMaxLength, $strFieldName))
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["usuario"];
	}

	function checkPassword($strPassword, $strPasswordConfirm, $intMinLength, $intMaxLength, $strFieldName, $strFieldName2){
		$strRegExp = "/^[a-zA-Z_0-9]+$/";
		$this->check($strPassword, $strRegExp, $intMinLength, $intMaxLength, $strFieldName);
		$this->check($strPasswordConfirm, $strRegExp, $intMinLength, $intMaxLength, $strFieldName2);
		if ($strPassword != $strPasswordConfirm){
			$this->arrErrors[$strFieldName2] = $this->arrErrorsTextos["contrasenia"];
			$this->errorsCount++;
		}
	}

	function checkDateSpecific($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strString = dateToSQL($strString);
		$arrDate = explode("/", $strString);

		if (sizeOf($arrDate) < 3){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}
		if (!checkDate(intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0]))){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}else
			return true;
	}

	function checkFutureDate($strString, $intMinLength, $intMaxLength, $intDaysFromNow, $strFieldName){
		$strString = dateToSQL($strString);
		$arrDate = explode("/", $strString);
		$intDaysFromNow = intval($intDaysFromNow);

		if (sizeOf($arrDate) < 3){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}
		if (!checkDate(intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0]))){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}else if (((mkTime(0, 0, 0, intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0])) - strToTime("now")) / 86400) < ($intDaysFromNow - 1)){
			$this->arrErrors[$strFieldName] = "Debe elegir una fecha por lo menos " . $intDaysFromNow . " " . (($intDaysFromNow > 1) ? "días" : "día") . " posterior al día de hoy.";
			$this->errorsCount++;
			return false;
		}else
			return true;
	}

	function checkPastDate($strString, $intMinLength, $intMaxLength, $strFieldName, $intPastYears = 0, $strErrorPastYears = ""){
		$strString = dateToSQL($strString);
		$intPastYears = intval($intPastYears);
		$arrDate = explode("/", $strString);

		if (sizeOf($arrDate) < 3){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}
		if ($arrDate[0] < 1900 || $arrDate[0] > 3000){
			$this->arrErrors[$strFieldName] = "El año ingresado es inválido";
			$this->errorsCount++;
			return false;
		}
		if (!checkDate(intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0]))){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}else if (((strToTime("now") - mkTime(0, 0, 0, intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0]))) / 86400) < (($intPastYears * 365.25))){
			$this->arrErrors[$strFieldName] = ($strErrorPastYears) ? $strErrorPastYears : "La fecha debe ser por lo menos anterior al día de hoy.";
			$this->errorsCount++;
			return false;
		}else
			return true;
	}

	function checkPastDateYesterday($strString, $intMinLength, $intMaxLength, $strFieldName){
		$strString = dateToSQL($strString);
		$arrDate = explode("/", $strString);
		$intDaysToNow = 1;

		if (sizeOf($arrDate) < 3){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}
		if (!checkDate(intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0]))){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["fecha"];
			$this->errorsCount++;
			return false;
		}else if (((mkTime(0, 0, 0, intval($arrDate[1]), intval($arrDate[2]), intval($arrDate[0])) - strToTime("now")) / 86400) > 0){
			$this->arrErrors[$strFieldName] = "Debe elegir una fecha anterior a hoy.";
			$this->errorsCount++;
			return false;
		}else
			return true;
	}

	function checkCombo($strString, $strFieldName){
		if ($strString && $this->checkSpecificNumber($strString, 1, 10, -1, 9999999999, $strFieldName)){
			return true;
		}else{
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["combo"];
			$this->errorsCount++;
			return false;
		}
	}

	function checkImage($arrImage, $intMinHeight, $intMaxHeight, $intMinWidth, $intMaxWidth, $intMaxSize, $strFieldName, $blnFlash = false){
		if (is_array($arrImage)){
			if (!$arrImage["name"]){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["imagen_vacia"];
				$this->errorsCount++;
				return false;
			}else if ($arrImage["error"] != 0){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["imagen_error"];
				$this->errorsCount++;
				return false;
			}else if ($arrImage["size"] == 0){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["imagen_error"];
				$this->errorsCount++;
				return false;
			}else if ($arrImage["size"] > $intMaxSize){
				$this->arrErrors[$strFieldName] = str_replace("{ACTUAL}", (intval(($arrImage["size"] / 1024) * 10) / 10), $this->arrErrorsTextos["imagen_error_tamanio"]);
				$this->arrErrors[$strFieldName] = str_replace("{MAX}", (intval(($intMaxSize / 1024) * 10) / 10), $this->arrErrors[$strFieldName]);
				$this->errorsCount++;
				return false;
			}else if (substr($arrImage["type"], 0, 5) != "image"){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["imagen_error_tipo"];
				$this->errorsCount++;
				return false;
			}else if ($blnFlash && !(strpos($arrImage["type"], "jpeg"))){
				$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["imagen_error_tipo_flash"];
				$this->errorsCount++;
				return false;
			}else{
				$arrSize = getImageSize($arrImage["tmp_name"]); 
				if (($arrSize[0] < $intMinWidth) || ($arrSize[0] > $intMaxWidth) || (($arrSize[1] < $intMinHeight) || ($arrSize[1] > $intMaxHeight))) {
					if ($intMinWidth == $intMaxWidth && $intMinHeight == $intMaxHeight){
						$this->arrErrors[$strFieldName] = str_replace("{ANCHO_ACTUAL}", $arrSize[0], $this->arrErrorsTextos["imagen_error_ancho_alto"]);
						$this->arrErrors[$strFieldName] = str_replace("{ALTO_ACTUAL}", $arrSize[1], $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ANCHO}", $intMinWidth, $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ALTO}", $intMinHeight, $this->arrErrors[$strFieldName]);
					}else{
						$this->arrErrors[$strFieldName] = str_replace("{ANCHO_ACTUAL}", $arrSize[0], $this->arrErrorsTextos["imagen_error_ancho_alto_variable"]);
						$this->arrErrors[$strFieldName] = str_replace("{ALTO_ACTUAL}", $arrSize[1], $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ANCHO_MIN}", $intMinWidth, $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ANCHO_MAX}", $intMaxWidth, $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ALTO_MIN}", $intMinHeight, $this->arrErrors[$strFieldName]);
						$this->arrErrors[$strFieldName] = str_replace("{ALTO_MAX}", $intMaxHeight, $this->arrErrors[$strFieldName]);
					}

					$this->errorsCount++;
					return false;
				}else
					return true;
			}
		}else
			return $this->checkText($arrImage, 0, 36, $strFieldName);
	}

	function checkYouTubeVideo($strURL, $strFieldName){
		if (!$strURL){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["youtube_url_vacia"];
			$this->errorsCount++;
			return false;
		}

		if (strToLower(substr($strURL, 0, 31)) != "http://www.youtube.com/watch?v="){
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["youtube_url_invalida"];
			$this->errorsCount++;
			return false;
		}

		$strYouTubeCode = "";
		$intInicio = strpos($strURL, "?v=");
		$intCasiFin = strpos($strURL, "&", $intInicio + 3);
		$intFin = ($intCasiFin !== false) ? $intCasiFin - $intInicio - 3 : 0;

		$strYouTubeCode = ($intCasiFin !== false) ? substr($strURL, $intInicio + 3, $intFin) : substr($strURL, $intInicio + 3);

		if ($strYouTubeCode)
			return true;
		else{
			$this->arrErrors[$strFieldName] = $this->arrErrorsTextos["youtube_url_incompleta"];
			$this->errorsCount++;
			return false;
		}
	}

}
?>