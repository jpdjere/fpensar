<?php

/****************************************************************************
* Class clsDocumentos: Clase de Documentos                                    *
****************************************************************************/

class clsDocumentos {

	var $intDocumento;
	var $strTitulo;
	var $strTexto;
	var $intAutor;
	var $strTags;
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

	/* Chequeo una Documento a subir */
	function chequearDocumento($intAutor, $strTitulo, $strTexto, $strTags, $strFecha){

		/* Instancio el objeto clsChecker */
		if (!isset($objCheck))
			$objCheck = new clsChecker();
		else
			global $objCheck;

		$objCheck->checkCombo($intAutor, "intAutor");
		$objCheck->checkString($strTitulo, 3, 100, "strTitulo");
		$objCheck->checkAnyText($strTexto, 10, 1000, "strTexto");
		$objCheck->checkAnyText($strTags, 5, 1000, "strTags");
		$objCheck->checkDateSpecific($strFecha, 6, 10, "strFecha");

		$this->errorAutor = (isset($objCheck->arrErrors["intAutor"])) ? $objCheck->arrErrors["intAutor"] : "";
		$this->errorTitulo = (isset($objCheck->arrErrors["strTitulo"])) ? $objCheck->arrErrors["strTitulo"] : "";
		$this->errorTexto = (isset($objCheck->arrErrors["strTexto"])) ? $objCheck->arrErrors["strTexto"] : "";
		$this->errorTags = (isset($objCheck->arrErrors["strTags"])) ? $objCheck->arrErrors["strTags"] : "";
		$this->errorFecha = (isset($objCheck->arrErrors["strFecha"])) ? $objCheck->arrErrors["strFecha"] : "";

		$this->intErrors = $objCheck->errorsCount;
	}

	function checkProcessImagen($strImagen, $strImagenAnterior){
		$this->strImagen = "";
		$this->errorImagen = "";
		if ($strImagen && $strImagen["name"]){
			$strImageName = md5(microtime());
			$this->strImagen = resizeImage(PATH_IMAGEN_DOCUMENTOS_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_DOCUMENTOS_GRANDE_ANCHO, IMAGEN_DOCUMENTOS_GRANDE_ALTO, $strImageName . ".jpg");
			$this->strImagenChica = resizeImage(PATH_IMAGEN_DOCUMENTOS_CHICA_LOCAL, $strImagen, $strImagenAnterior, IMAGEN_DOCUMENTOS_CHICA_ANCHO, IMAGEN_DOCUMENTOS_CHICA_ALTO, $this->strImagen);
			if (!$this->strImagen){
				$this->strImagen = $strImagenAnterior;
				$this->errorImagen = "La imagen no ha podido ser procesada";
				$this->intErrores++;
			}else{
				// Recorto la imagen para darle la forma circular
				global $arrImageTypes;
				$strImageURL = "../" . PATH_IMAGEN_DOCUMENTOS . "/" . $this->strImagen;
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
					$objImageMask = imageCreateFromPNG("../" . PATH_IMAGEN_DOCUMENTOS . "/_mask.png");
					$objImageBorder = imageCreateFromPNG("../" . PATH_IMAGEN_DOCUMENTOS . "/_border.png");
					$objImageSource = applyMaskToImage($objImageSource, $objImageMask);
					imagecopy($objImageSource, $objImageBorder, 0, 0, 0, 0, 257, 257);

					$objImageShadow = imageCreateFromPNG("../" . PATH_IMAGEN_DOCUMENTOS . "/_shadow.png");
					imagesavealpha($objImageShadow, true);
					imagecopy($objImageShadow, $objImageSource, 0, 0, 0, 0, 257, 257);
					imagePNG($objImageShadow, "../" . PATH_IMAGEN_DOCUMENTOS . "/" . $strImageName . ".png", 0);
					$this->strImagen = $strImageName . ".png";

					// Resizeo la imagen Chica
					$arrNewFoto = array(
						"name" => $strImageName . ".png",
						"type" => "image/png",
						"tmp_name" => realpath("../") . "/" . PATH_IMAGEN_DOCUMENTOS . "/" . $strImageName . ".png"
					);
					$this->strImagenChica = resizeImage(PATH_IMAGEN_DOCUMENTOS_CHICA_LOCAL, $arrNewFoto, "", IMAGEN_DOCUMENTOS_CHICA_ANCHO, IMAGEN_DOCUMENTOS_CHICA_ALTO, $this->strImagen);
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

	/* Inserta una Documento en la Tabla DOCUMENTOS */
	function insertDocumento($intAutor, $strTitulo, $strTexto, $strTags, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearDocumento($intAutor, $strTitulo, $strTexto, $strTags, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_DOCUMENTOS_LOCAL);
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
		$intAutor = intval($intAutor);
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strTags = stringToSQL(capitalizeFirst($strTags));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " INSERT INTO ";
		$strSQL .= " 	DOCUMENTOS";
		$strSQL .= "		(COD_AUTOR, ";
		$strSQL .= "		DES_TITULO, ";
		$strSQL .= "		DES_TEXTO, ";
		$strSQL .= "		DES_TAGS, ";
		$strSQL .= "		DES_IMAGEN, ";
		$strSQL .= "		DES_ARCHIVO, ";
		$strSQL .= "		FEC_FECHA, ";
		$strSQL .= "		FEC_FECHA_ALTA, ";
		$strSQL .= "		FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		FLG_HABILITADO)";
		$strSQL .= "	VALUES ";
		$strSQL .= "		($intAutor, ";
		$strSQL .= "		'$strTitulo', ";
		$strSQL .= "		'$strTexto', ";
		$strSQL .= "		'$strTags', ";
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

		$this->intDocumento = mysql_insert_id();

		return true;
	}

	/* Hago Update de la Documento */
	function updateDocumento($intDocumento, $intAutor, $strTitulo, $strTexto, $strTags, $strImagen, $strImagenAnterior, $strArchivo, $strArchivoAnterior, $strFecha, $blnHabilitado){

		$this->chequearDocumento($intAutor, $strTitulo, $strTexto, $strTags, $strFecha);
		$this->checkProcessImagen($strImagen, $strImagenAnterior);

		// Process Archivo
		$this->strArchivo = "";
		$this->errorArchivo = "";
		if ($strArchivo && $strArchivo["name"]){
			$this->strArchivo = checkUploadedAttachment($strArchivo, PATH_IMAGEN_DOCUMENTOS_LOCAL);
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
		$intAutor = intval($intAutor);
		$strTitulo = stringToSQL(capitalizeFirst($strTitulo));
		$strTexto = stringToSQL(capitalizeFirst($strTexto));
		$strTags = stringToSQL(capitalizeFirst($strTags));
		$strFecha = dateToSQL($strFecha);

		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= " 	DOCUMENTOS";
		$strSQL .= "		SET ";
		$strSQL .= "			COD_AUTOR = $intAutor, ";
		$strSQL .= "			DES_TITULO = '$strTitulo', ";
		$strSQL .= "			DES_TEXTO = '$strTexto', ";
		$strSQL .= "			DES_TAGS = '$strTags', ";
		if ($this->strImagen)
			$strSQL .= "			DES_IMAGEN = '" . $this->strImagen . "', ";
		if ($strArchivo)
			$strSQL .= "			DES_ARCHIVO = '" . $this->strArchivo . "', ";
		$strSQL .= "			FEC_FECHA = '" . $strFecha . "', ";
		$strSQL .= "			FEC_FECHA_MODIFICACION = SYSDATE(), ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnHabilitado) ? "S": "N") . "'";
		$strSQL .= "		WHERE ";
		$strSQL .= "			COD_DOCUMENTO = $intDocumento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return true;
	}

	/* Borra una Documento de la Tabla Documentos */
	function deleteDocumento($intDocumento){

		/* Borro la tabla DOCUMENTOS */
		$strSQL = " DELETE FROM ";
		$strSQL .= "	DOCUMENTOS ";
		$strSQL .= "		WHERE COD_DOCUMENTO = $intDocumento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	function setEstado($intDocumento, $blnQualify = false){
		/* Escribo SQL */
		$strSQL = " UPDATE ";
		$strSQL .= "	DOCUMENTOS ";
		$strSQL .= "		SET ";
		$strSQL .= "			FLG_HABILITADO = '" . (($blnQualify) ? "S": "N") . "' ";
		$strSQL .= "		WHERE COD_DOCUMENTO = $intDocumento";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);
	}

	/* Levanto los datos de la base */
	function getDocumentosTotal($blnBackoffice = false, $arrDocumentosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false){
		$arrTextoBusqueda = explode(" ", $strTextoBusqueda);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		COUNT(documentos.COD_DOCUMENTO) AS NUM_DOCUMENTOS ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DOCUMENTOS documentos ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		1 ";

		if (!$blnBackoffice){
			$strSQL .= "		AND documentos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND documentos.FLG_HABILITADO = 'N' ";
		}

		if ($arrDocumentosToExclude){
			if (is_array($arrDocumentosToExclude)){
				$strSQL .= "		AND documentos.COD_DOCUMENTO NOT IN (";
				for ($i = 0; $i < sizeOf($arrDocumentosToExclude); $i++){
					$strSQL .= "			$arrDocumentosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrDocumentosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND documentos.COD_DOCUMENTO <> $arrDocumentosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (MATCH(documentos.DES_TAGS) AGAINST('";
			for ($i = 0; $i < sizeOf($arrTextoBusqueda); $i++){
				$strSQL .= "+" . stringToSQL($arrTextoBusqueda[$i]) . "*";
			}
			$strSQL .= "' IN BOOLEAN MODE) ";
			$strSQL .= " OR DES_TITULO LIKE '%$strTextoBusqueda%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(documentos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(documentos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			documentos.COD_DOCUMENTO ";

		/* Ejecuto SQL */
		if (!isset($objQuery))
			$objQuery = new DB_Sql();
		else
			global $objQuery;
		$objQuery->query($strSQL);

		return $objQuery->Row;
	}

	/* Levanto los datos de la base */
	function getDocumentos($arrDocumentos = false, $blnBackoffice = false, $arrDocumentosToExclude = false, $strTextoBusqueda = false, $intMes = false, $intAnio = false, $intPagina = false, $intPaginado = 20){

		$intPagina = intval($intPagina);
		$intPaginado = intval($intPaginado);
		if ($intPaginado <= 0) $intPaginado = 20;

		$arrTextoBusqueda = explode(" ", $strTextoBusqueda);

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		documentos.COD_DOCUMENTO, ";
		$strSQL .= "		documentos.COD_AUTOR, ";
		$strSQL .= "		autores.DES_AUTOR, ";
		$strSQL .= "		documentos.DES_TITULO, ";
		$strSQL .= "		documentos.DES_TEXTO, ";
		$strSQL .= "		documentos.DES_TAGS, ";
		$strSQL .= "		documentos.DES_IMAGEN, ";
		$strSQL .= "		documentos.DES_ARCHIVO, ";
		if ($strTextoBusqueda){
			$strSQL .= "		MATCH(documentos.DES_TAGS) AGAINST('";
			for ($i = 0; $i < sizeOf($arrTextoBusqueda); $i++){
				$strSQL .= "+" . stringToSQL($arrTextoBusqueda[$i]) . "*";
			}
			$strSQL .= "') AS RELEVANCE, ";
		}
		$strSQL .= "		DATE_FORMAT(documentos.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA, ";
		$strSQL .= "		DATE_FORMAT(documentos.FEC_FECHA, '%d/%m/%Y') AS DES_FECHA_LISTADO, ";
		$strSQL .= "		DATE_FORMAT(documentos.FEC_FECHA, '%d') AS FEC_FECHA_DIA, ";
		$strSQL .= "		DATE_FORMAT(documentos.FEC_FECHA, '%m') AS FEC_FECHA_MES, ";
		$strSQL .= "		DATE_FORMAT(documentos.FEC_FECHA, '%Y') AS FEC_FECHA_ANIO, ";
		$strSQL .= "		documentos.FEC_FECHA_ALTA, ";
		$strSQL .= "		documentos.FEC_FECHA_MODIFICACION, ";
		$strSQL .= "		documentos.FLG_HABILITADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DOCUMENTOS documentos, ";
		$strSQL .= "		NOTAS_AUTORES autores ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		documentos.COD_AUTOR = autores.COD_AUTOR ";

		if ($arrDocumentos){
			if (is_array($arrDocumentos)){
				$strSQL .= "		AND documentos.COD_DOCUMENTO IN (";
				for ($i = 0; $i < sizeOf($arrDocumentos); $i++){
					$strSQL .= "			$arrDocumentos[$i] ";
					$strSQL .= ($i != (sizeOf($arrDocumentos) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND documentos.COD_DOCUMENTO = $arrDocumentos";
		}

		if (!$blnBackoffice){
			$strSQL .= "		AND documentos.FLG_HABILITADO = 'S' ";
		}else if ($blnBackoffice === "restricted"){
			$strSQL .= "		AND documentos.FLG_HABILITADO = 'N' ";
		}

		if ($arrDocumentosToExclude){
			if (is_array($arrDocumentosToExclude)){
				$strSQL .= "		AND documentos.COD_DOCUMENTO NOT IN (";
				for ($i = 0; $i < sizeOf($arrDocumentosToExclude); $i++){
					$strSQL .= "			$arrDocumentosToExclude[$i] ";
					$strSQL .= ($i != (sizeOf($arrDocumentosToExclude) - 1)) ? ", " : ") ";
				}
			}else
				$strSQL .= "		AND documentos.COD_DOCUMENTO <> $arrDocumentosToExclude";
		}

		if ($strTextoBusqueda){
			$strSQL .= "		AND (MATCH(documentos.DES_TAGS) AGAINST('";
			for ($i = 0; $i < sizeOf($arrTextoBusqueda); $i++){
				$strSQL .= "+" . stringToSQL($arrTextoBusqueda[$i]) . "*";
			}
			$strSQL .= "' IN BOOLEAN MODE) ";
			$strSQL .= " OR DES_TITULO LIKE '%$strTextoBusqueda%') ";
		}

		if ($intMes){
			$strSQL .= "		AND DATE_FORMAT(documentos.FEC_FECHA_ALTA, '%m') = $intMes ";
		}

		if ($intAnio){
			$strSQL .= "		AND DATE_FORMAT(documentos.FEC_FECHA_ALTA, '%Y') = $intAnio ";
		}

		$strSQL .= " 	GROUP BY ";
		$strSQL .= "			documentos.COD_DOCUMENTO ";

		$strSQL .= " 	ORDER BY ";
		$strSQL .= "			documentos.FEC_FECHA DESC, documentos.DES_TITULO ";

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

	function getDocumentosRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intDocumento = $this->arrRecord[$intNumRecord]["COD_DOCUMENTO"];
			$this->intAutor = $this->arrRecord[$intNumRecord]["COD_AUTOR"];
			$this->strAutor = $this->arrRecord[$intNumRecord]["DES_AUTOR"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strTags = $this->arrRecord[$intNumRecord]["DES_TAGS"];
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

	/* Levanto los datos de la base */
	function getDocumentoHome(){

		/* Escribo SQL */
		$strSQL = " SELECT ";
		$strSQL .= "		n.COD_DOCUMENTO, ";
		$strSQL .= "		n.COD_AUTOR, ";
		$strSQL .= "		a.DES_AUTOR, ";
		$strSQL .= "		n.DES_TITULO, ";
		$strSQL .= "		n.DES_TEXTO, ";
		$strSQL .= "		DATE_FORMAT(n.FEC_FECHA_ALTA, '%d/%m/%Y') AS FEC_FECHA_LISTADO ";
		$strSQL .= "	FROM ";
		$strSQL .= "		DOCUMENTOS n, ";
		$strSQL .= "		NOTAS_AUTORES a ";
		$strSQL .= "	WHERE ";
		$strSQL .= "		n.COD_AUTOR = a.COD_AUTOR ";
		$strSQL .= "		n.FLG_HABILITADO = 'S' ";
		$strSQL .= "	ORDER BY ";
		$strSQL .= "		n.FEC_FECHA DESC ";
		$strSQL .= " 	LIMIT 1 ";

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

	function getDocumentoHomeRow($intNumRecord = 0){
		if ($intNumRecord < $this->intTotal){
			$this->intDocumento = $this->arrRecord[$intNumRecord]["COD_DOCUMENTO"];
			$this->strTitulo = $this->arrRecord[$intNumRecord]["DES_TITULO"];
			$this->strTexto = $this->arrRecord[$intNumRecord]["DES_TEXTO"];
			$this->strFechaListado = $this->arrRecord[$intNumRecord]["FEC_FECHA_LISTADO"];
			return true;
		} else
			return false;
	}

}

?>