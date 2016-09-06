<?php

/****************************************************************************
* Class clsNotas: Clase de Notas                                    *
****************************************************************************/

class clsNotas {

	var $intNota;
	var $intAutor;
	var $intMedio;
	var $strTitulo;
	var $strTexto;
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

	/* Chequeo un Nota a subir */
	function chequearNota($intAutor, $strTitulo, $strTexto, $intMedio, $strLinkURL, $strFecha){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkCombo($intAutor, "intAutor");
		$objCheck->checkString($strTitulo, 3, 150, "strTitulo");
		$objCheck->checkAnyText($strTexto, 10, 1000, "strTexto");
		$objCheck->checkCombo($intMedio, "intMedio");
		$objCheck->checkURL($strLinkURL, 10, 1000, "strLinkURL");
		$objCheck->checkDateSpecific($strFecha, 6, 10, "strFecha");

		$this->errorAutor = (isset($objCheck->arrErrors["intAutor"])) ? $objCheck->arrErrors["intAutor"] : "";
		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorMedio = (isset($objCheck->arrErrors["intMedio"])) ? $objCheck->arrErrors["intMedio"] : "";
		$this->errorLinkURL = (isset($objCheck->arrErrors["strLinkURL"])) ? $objCheck->arrErrors["strLinkURL"] : "";
		$this->errorFecha = (isset($objCheck->arrErrors["strFecha"])) ? $objCheck->arrErrors["strFecha"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	/* Inserta un Nota en la Tabla NOTAS */
	function insertNota($intAutor, $strTitulo, $strTexto, $strArchivo, $strArchivoAnterior, $intMedio, $strLinkURL, $strFecha, $blnHabilitado){

		$this->chequearNota($intAutor, $strTitulo, $strTexto, $intMedio, $strLinkURL, $strFecha);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		/*if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_NOTAS_LOCAL);
			if (!$this->strArchivo || $this->strArchivo == "ERROR"){
				$this->strArchivo = $strArchivoAnterior;
				$this->errorArchivo = "El archivo no ha podido ser procesado";
				$this->intErrores++;
			}
		}else if ($strArchivoAnterior){
			$this->strArchivo = $strArchivoAnterior;
		}else{
			$this->errorArchivo = "Debe elegir un archivo válido";
			$this->intErrores++;
		}*/

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$intAutor = intval($intAutor);
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$intMedio = intval($intMedio);
		$strLinkURL = stringToSQL($strLinkURL);
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	NOTAS";
		$strSQL .= "		(COD_AUTOR, ";
		$strSQL .= "		DES_TITULO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_ARCHIVO, ";
		$strSQL .= "		COD_MEDIO, ";
		$strSQL .= "		DES_LINK, ";
		$strSQL .= "		FEC_FECHA, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		($intAutor, ";
		$strSQL .= "		'$strTitulo', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'" . $this->strArchivo . "', ";
		$strSQL .= "		$intMedio, ";
		$strSQL .= "		'$strLinkURL', ";
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

		$this->intNota = mysql_insert_id();
		return $this->intNota;
	}

	/* Hago Update del Nota */
	function updateNota($intNota, $intAutor, $strTitulo, $strTexto, $strArchivo, $strArchivoAnterior, $intMedio, $strLinkURL, $strFecha, $blnHabilitado){

		$this->chequearNota($intAutor, $strTitulo, $strTexto, $intMedio, $strLinkURL, $strFecha);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		/*if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_NOTAS_LOCAL);
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
		}*/

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$intAutor = intval($intAutor);
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$intMedio = intval($intMedio);
		$strLinkURL = stringToSQL($strLinkURL);
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	NOTAS";
		$strSQL .= "		SET ";
		$strSQL .= "			COD_AUTOR = $intAutor, ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		if ($strArchivo)
			$strSQL .= "			DES_ARCHIVO = '" . $this->strArchivo . "', ";
		$strSQL .= "			COD_MEDIO = '" . $intMedio . "', ";
		$strSQL .= "			DES_LINK = '" . $strLinkURL . "', ";
		$strSQL .= "			FEC_FECHA = '" . $strFecha . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_NOTA = $intNota";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra un Nota de la Tabla Notas */
	function deleteNota($intNota){

		/* Borro la tabla NOTAS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	NOTAS ";
		$strSQL .= "		WHERE COD_NOTA = $intNota";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intNota, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	NOTAS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_NOTA = $intNota";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getNotasTotal($blnBackoffice = false, $arrNotasToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(notas.COD_NOTA) AS NUM_NOTAS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS notas ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND notas.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND notas.FLG_HABILITADO = 'N' ";
		}

		if ($arrNotasToExclude){
			if (is_array($arrNotasToExclude)){
				$strSQL .= "		AND notas.COD_NOTA NOT IN (";
				for ($i = 0; $i < sizeOf($arrNotasToExclude); $i++){
					$strSQL .= "			$arrNotasToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrNotasToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND notas.COD_NOTA <> $arrNotasToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (notas.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR notas.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(notas.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(notas.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			notas.COD_NOTA ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getNotas($arrNotas = false, $blnBackoffice = false, $arrNotasToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		notas.COD_NOTA, ";
		$strSQL .= "		notas.COD_AUTOR, ";
		$strSQL .= "		autores.DES_AUTOR, ";
		$strSQL .= "		notas.DES_TITULO, ";
		$strSQL .= "		notas.DES_TEXTO, ";
		$strSQL .= "		autores.DES_IMAGEN, ";
		$strSQL .= "		notas.DES_ARCHIVO, ";
		$strSQL .= "		notas.COD_MEDIO, ";
		$strSQL .= "		medios.DES_MEDIO, ";
		$strSQL .= "		medios.DES_IMAGEN AS DES_IMAGEN_MEDIO, ";
		$strSQL .= "		notas.DES_LINK, ";
		$strSQL .= "		DATE_FORMAT(notas.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA, ";
		$strSQL .= "		DATE_FORMAT(notas.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(notas.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(notas.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(notas.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		notas.FEC_FECHA_ALTA, ";
		$strSQL .= "		notas.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		notas.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS notas, ";
		$strSQL .= "		NOTAS_AUTORES autores, ";
		$strSQL .= "		NOTAS_MEDIOS medios ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		notas.COD_AUTOR = autores.COD_AUTOR ";
		$strSQL .= "		AND notas.COD_MEDIO = medios.COD_MEDIO ";

		if ($arrNotas){
			if (is_array($arrNotas)){
				$strSQL .= "		AND notas.COD_NOTA IN (";
				for ($i = 0; $i < sizeOf($arrNotas); $i++){
					$strSQL .= "			$arrNotas[$i] ";
					$strSQL .= ($i != (sizeOf($arrNotas) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND notas.COD_NOTA = $arrNotas";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND notas.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND notas.FLG_HABILITADO = 'N' ";
		}

		if ($arrNotasToExclude){
			if (is_array($arrNotasToExclude)){
				$strSQL .= "		AND notas.COD_NOTA NOT IN (";
				for ($i = 0; $i < sizeOf($arrNotasToExclude); $i++){
					$strSQL .= "			$arrNotasToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrNotasToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND notas.COD_NOTA <> $arrNotasToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (notas.DES_TITULO LIKE '%" . $strTextoBusqueda . "%' ";
			$strSQL .= "		OR notas.DES_TEXTO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(notas.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(notas.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			notas.COD_NOTA ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			notas.FEC_FECHA DESC, notas.DES_TITULO ";

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

	function getNotasRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intNota = $this->arrRecord[$intNumRecord]["COD_NOTA"];
			$this->intAutor = $this->arrRecord[$intNumRecord]["COD_AUTOR"];
			$this->strAutor = $this->arrRecord[$intNumRecord]["DES_AUTOR"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->strArchivo = $this->arrRecord[$intNumRecord]["DES_ARCHIVO"];
			$this->intMedio = $this->arrRecord[$intNumRecord]["COD_MEDIO"];
			$this->strMedio = $this->arrRecord[$intNumRecord]["DES_MEDIO"];
			$this->strImagenMedio = $this->arrRecord[$intNumRecord]["DES_IMAGEN_MEDIO"];
			$this->strLinkURL = $this->arrRecord[$intNumRecord]["DES_LINK"];
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

	/***********************************************************************************************/
	/* Autores */
	/***********************************************************************************************/

	/* Chequeo un Autor a subir */
	function chequearAutor($strAutor){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strAutor, 3, 100, "strAutor");
		$this->errorAutor = (isset($objCheck->arrErrors["strAutor"])) ? $objCheck->arrErrors["strAutor"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagenAutor($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen && $strImagen["name"]){
			$strImageName = md5(microtime());
			$this->strImagen = resizeImage(PATH_IMAGEN_NOTAS_AUTORES_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_NOTAS_AUTORES_GRANDE_ANCHO, IMAGEN_NOTAS_AUTORES_GRANDE_ALTO, $strImageName . ".jpg");
			$this->strImagenChica = resizeImage(PATH_IMAGEN_NOTAS_AUTORES_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_NOTAS_AUTORES_CHICA_ANCHO, IMAGEN_NOTAS_AUTORES_CHICA_ALTO, $this->strImagen);
			if (!$this->strImagen){
				$this->strImagen = $strImagenAnterior;
				$this->errorImagen = "La imagen no ha podido ser procesada";
				$this->intErrores++;
			}else{
				// Recorto la imagen para darle la forma circular
				global $arrImageTypes;
				$strImageURL = "../" . PATH_IMAGEN_NOTAS_AUTORES . "/" . $this->strImagen;
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
					$objImageMask = imageCreateFromPNG("../" . PATH_IMAGEN_NOTAS_AUTORES . "/_mask.png");
					$objImageBorder = imageCreateFromPNG("../" . PATH_IMAGEN_NOTAS_AUTORES . "/_border.png");
					$objImageSource = applyMaskToImage($objImageSource, $objImageMask);
					imagecopy($objImageSource, $objImageBorder, 0, 0, 0, 0, 257, 257);

					$objImageShadow = imageCreateFromPNG("../" . PATH_IMAGEN_NOTAS_AUTORES . "/_shadow.png");
					imagesavealpha($objImageShadow, true);
					imagecopy($objImageShadow, $objImageSource, 0, 0, 0, 0, 257, 257);
					imagePNG($objImageShadow, "../" . PATH_IMAGEN_NOTAS_AUTORES . "/" . $strImageName . ".png", 0);
					$this->strImagen = $strImageName . ".png";

					// Resizeo la imagen Chica
					$arrNewFoto = array(
						"name" => $strImageName . ".png",
						"type" => "image/png",
						"tmp_name" => realpath("../") . "/" . PATH_IMAGEN_NOTAS_AUTORES . "/" . $strImageName . ".png"
					);
					$this->strImagenChica = resizeImage(PATH_IMAGEN_NOTAS_AUTORES_CHICA_LOCAL, $arrNewFoto, "", IMAGEN_NOTAS_AUTORES_CHICA_ANCHO, IMAGEN_NOTAS_AUTORES_CHICA_ALTO, $this->strImagen);
				}
			}
		}else if ($strImagenAnterior != IMAGEN_NO_DISPONIBLE){
			$this->strImagen = $strImagenAnterior;
		}else{
			$this->strImagen = $strImagenAnterior;
			$this->errorImagen = "Debe elegir una imagen";
			$this->intErrores++;
		}
	}

	/* Inserta un Autor en la Tabla NOTAS */
	function insertAutor($strAutor, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearAutor($strAutor);
		$this->checkProcessImagenAutor($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strAutor = stringToSQL(capitalizeFirst($strAutor));

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	NOTAS_AUTORES ";
		$strSQL .= "		(DES_AUTOR, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strAutor', ";
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

		$this->intAutor = mysql_insert_id();
		return $this->intAutor;
	}

	/* Hago Update del Autor */
	function updateAutor($intAutor, $strAutor, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearAutor($strAutor);
		$this->checkProcessImagenAutor($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strAutor = stringToSQL(capitalizeFirst($strAutor));

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	NOTAS_AUTORES";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_AUTOR = '$strAutor', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_AUTOR = $intAutor";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra un Autor de la Tabla Notas */
	function deleteAutor($intAutor){

		/* Borro la tabla NOTAS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	NOTAS_AUTORES ";
		$strSQL .= "		WHERE COD_AUTOR = $intAutor";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstadoAutor($intAutor, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	NOTAS_AUTORES ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_AUTOR = $intAutor";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getAutoresTotal($blnBackoffice = false, $arrAutoresToExclude = false, $strTextoBusqueda = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(a.COD_AUTOR) AS NUM_NOTAS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS_AUTORES a ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND a.FLG_HABILITADO = 'S' ";
		}

		if ($arrAutoresToExclude){
			if (is_array($arrAutoresToExclude)){
				$strSQL .= "		AND a.COD_AUTOR NOT IN (";
				for ($i = 0; $i < sizeOf($arrAutoresToExclude); $i++){
					$strSQL .= "			" . intval($arrAutoresToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrAutoresToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_AUTOR <> " . intval($arrNotasToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (a.DES_AUTOR LIKE '%" . $strTextoBusqueda . "%') ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			a.DES_AUTOR DESC ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getAutores($arrAutores = false, $blnBackoffice = false, $arrAutoresToExclude = false, $strTextoBusqueda = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		a.COD_AUTOR, ";
		$strSQL .= "		a.DES_AUTOR, ";
		$strSQL .= "		a.DES_IMAGEN, ";
		$strSQL .= "		a.FEC_FECHA_ALTA, ";
		$strSQL .= "		a.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		a.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS_AUTORES a ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrAutores){
			if (is_array($arrAutores)){
				$strSQL .= "		AND a.COD_AUTOR IN (";
				for ($i = 0; $i < sizeOf($arrAutores); $i++){
					$strSQL .= "			" . intval($arrAutores[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrAutores) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_AUTOR = " . intval($arrAutores) . " ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND a.FLG_HABILITADO = 'S' ";
		}

		if ($arrAutoresToExclude){
			if (is_array($arrAutoresToExclude)){
				$strSQL .= "		AND a.COD_AUTOR NOT IN (";
				for ($i = 0; $i < sizeOf($arrAutoresToExclude); $i++){
					$strSQL .= "			" . intval($arrAutoresToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrAutoresToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_AUTOR <> " . intval($arrAutoresToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (a.DES_AUTOR LIKE '%" . $strTextoBusqueda . "%') ";
		}

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			a.DES_AUTOR DESC ";

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

	function getAutoresRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intAutor = $this->arrRecord[$intNumRecord]["COD_AUTOR"];
			$this->strAutor = $this->arrRecord[$intNumRecord]["DES_AUTOR"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->strFechaAlta = $this->arrRecord[$intNumRecord]["FEC_FECHA_ALTA"];
			$this->strFechaModificacion = $this->arrRecord[$intNumRecord]["FEC_FECHA_MODIFICACION"];
			$this->blnHabilitado = ($this->arrRecord[$intNumRecord]["FLG_HABILITADO"] == "S") ? true : false;
			return true;
		} else
			return false;
	}

	/***********************************************************************************************/
	/* Medios */
	/***********************************************************************************************/

	/* Chequeo un Medio a subir */
	function chequearMedio($strMedio){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkString($strMedio, 3, 100, "strMedio");
		$this->errorMedio = (isset($objCheck->arrErrors["strMedio"])) ? $objCheck->arrErrors["strMedio"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagenMedio($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen && $strImagen["name"]){
			$this->strImagen = resizeImageWidth(PATH_IMAGEN_NOTAS_MEDIOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_NOTAS_MEDIOS_GRANDE_ANCHO, IMAGEN_NOTAS_MEDIOS_GRANDE_ALTO);
			$this->strImagenChica = resizeImageWidth(PATH_IMAGEN_NOTAS_MEDIOS_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_NOTAS_MEDIOS_CHICA_ANCHO, IMAGEN_NOTAS_MEDIOS_CHICA_ALTO, $this->strImagen);
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

	/* Inserta un Medio en la Tabla NOTAS */
	function insertMedio($strMedio, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearMedio($strMedio);
		$this->checkProcessImagenMedio($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strMedio = stringToSQL(capitalizeFirst($strMedio));

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	NOTAS_MEDIOS ";
		$strSQL .= "		(DES_MEDIO, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		('$strMedio', ";
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

		$this->intMedio = mysql_insert_id();
		return $this->intMedio;
	}

	/* Hago Update del Medio */
	function updateMedio($intMedio, $strMedio, $strImagen, $strImagenAnterior, $blnHabilitado){

		$this->chequearMedio($strMedio);
		$this->checkProcessImagenMedio($strImagen, $strImagenAnterior);

		if ($this->intErrors)
			return false;

		/* Corrigo Texto Entrante */
		$strMedio = stringToSQL(capitalizeFirst($strMedio));

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	NOTAS_MEDIOS";
		$strSQL .= "		SET ";
		$strSQL .= "			DES_MEDIO = '$strMedio', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_MEDIO = $intMedio";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra un Medio de la Tabla Notas */
	function deleteMedio($intMedio){

		/* Borro la tabla NOTAS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	NOTAS_MEDIOS ";
		$strSQL .= "		WHERE COD_MEDIO = $intMedio";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstadoMedio($intMedio, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	NOTAS_MEDIOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_MEDIO = $intMedio";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getMediosTotal($blnBackoffice = false, $arrMediosToExclude = false, $strTextoBusqueda = false){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(a.COD_MEDIO) AS NUM_NOTAS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS_MEDIOS a ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND a.FLG_HABILITADO = 'S' ";
		}

		if ($arrMediosToExclude){
			if (is_array($arrMediosToExclude)){
				$strSQL .= "		AND a.COD_MEDIO NOT IN (";
				for ($i = 0; $i < sizeOf($arrMediosToExclude); $i++){
					$strSQL .= "			" . intval($arrMediosToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrMediosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_MEDIO <> " . intval($arrNotasToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (a.DES_MEDIO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			a.DES_MEDIO DESC ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getMedios($arrMedios = false, $blnBackoffice = false, $arrMediosToExclude = false, $strTextoBusqueda = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		a.COD_MEDIO, ";
		$strSQL .= "		a.DES_MEDIO, ";
		$strSQL .= "		a.DES_IMAGEN, ";
		$strSQL .= "		a.FEC_FECHA_ALTA, ";
		$strSQL .= "		a.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		a.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		NOTAS_MEDIOS a ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if ($arrMedios){
			if (is_array($arrMedios)){
				$strSQL .= "		AND a.COD_MEDIO IN (";
				for ($i = 0; $i < sizeOf($arrMedios); $i++){
					$strSQL .= "			" . intval($arrMedios[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrMedios) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_MEDIO = " . intval($arrMedios) . " ";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND a.FLG_HABILITADO = 'S' ";
		}

		if ($arrMediosToExclude){
			if (is_array($arrMediosToExclude)){
				$strSQL .= "		AND a.COD_MEDIO NOT IN (";
				for ($i = 0; $i < sizeOf($arrMediosToExclude); $i++){
					$strSQL .= "			" . intval($arrMediosToExclude[$i]) . " ";
					$strSQL .= ($i != (sizeOf($arrMediosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND a.COD_MEDIO <> " . intval($arrMediosToExclude) . " ";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (a.DES_MEDIO LIKE '%" . $strTextoBusqueda . "%') ";
		}

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			a.DES_MEDIO DESC ";

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

	function getMediosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intMedio = $this->arrRecord[$intNumRecord]["COD_MEDIO"];
			$this->strMedio = $this->arrRecord[$intNumRecord]["DES_MEDIO"];
			$this->strImagen = $this->arrRecord[$intNumRecord]["DES_IMAGEN"];
			$this->strFechaAlta = $this->arrRecord[$intNumRecord]["FEC_FECHA_ALTA"];
			$this->strFechaModificacion = $this->arrRecord[$intNumRecord]["FEC_FECHA_MODIFICACION"];
			$this->blnHabilitado = ($this->arrRecord[$intNumRecord]["FLG_HABILITADO"] == "S") ? true : false;
			return true;
		} else
			return false;
	}

}

?>