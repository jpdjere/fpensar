<?php

/****************************************************************************/
/*                 BEGIN FUNCIONES DE MENU Y DE PAGINA                      */
/****************************************************************************/

function initBackOfficeMenu(){
	global $objTemplate;

	// Defino Bloques de Secciones
	$objTemplate->set_block("MENU", "MENU_ITEM_PRINCIPAL", "menu_item_principal");
	$objTemplate->set_block("MENU_ITEM_PRINCIPAL", "MENU_ITEM_PRINCIPAL_SUBITEMS", "menu_item_principal_subitems");
	$objTemplate->set_block("HEADER", "MENU_HEADER_ITEM", "menu_header_item");
	$objTemplate->set_block("MENU", "MENU_CARPETA", "menu_carpeta");
	$objTemplate->set_block("MENU_CARPETA", "MENU_CARPETA_ITEMS", "menu_carpeta_items");
	$objTemplate->set_block("MENU", "MENU_ITEM", "menu_item");
}

function setBackofficeMenu(){
	// Base de datos
	if (!isset($objQuery))
		$objQuery = new DB_Sql();
	else
		global $objQuery;

	global $objBackOfficeUsuarios;
	global $objBackOfficeSecciones;
	global $objTemplate;
	global $strUsuarioLogueadoBackoffice;
	global $intSeccionBackOffice;

	// Levanto Datos del Usuario Logeado
	$objBackOfficeUsuarios->getUsuarios($strUsuarioLogueadoBackoffice);
	$objBackOfficeUsuarios->getUsuariosRow();
	$objTemplate->set_var(array(
		"strUsuarioBackoffice" => $objBackOfficeUsuarios->strUsuario,
		"strUsuarioBackofficeTitulo" => capitalize($objBackOfficeUsuarios->strUsuario),
		"strNombreUsuarioBackoffice" => $objBackOfficeUsuarios->strNombre,
		"strApellidoUsuarioBackoffice" => $objBackOfficeUsuarios->strApellido,
	));

	for ($i = 0; $i < $objBackOfficeSecciones->intTotal; $i++){
		$objBackOfficeSecciones->getSeccionesRow($i);
		if ($objBackOfficeSecciones->intAcceso){
			$objTemplate->set_var(array(
				"strSeccion" => $objBackOfficeSecciones->strSeccion,
				"strLink" => $objBackOfficeSecciones->strLink,
				"strClass" => $objBackOfficeSecciones->strIcono,
				"strClassActive" => ($intSeccionBackOffice && $intSeccionBackOffice == $objBackOfficeSecciones->intSeccion) ? "exp active" : "exp"
			));

			$objTemplate->parse("menu_item_principal", "MENU_ITEM_PRINCIPAL", true);
			$objTemplate->parse("menu_header_item", "MENU_HEADER_ITEM", true);

			if ($intSeccionBackOffice == $objBackOfficeSecciones->intSeccion && $objTemplate->get_var("menu_item_principal_subitems")){
				$objTemplate->parse("menu_item_principal", "menu_item_principal_subitems", true);
			}

		}
	}
}

function addBackofficeMenuCarpeta($strNombreCarpeta, $strLink, $blnActive = false, $intTipo = "dashboard", $intItems = ""){
	global $intSeccionBackOffice;
	global $objTemplate;
	global $objBackOfficeSecciones;
	global $strBackOfficeMenuSeccion;

	$arrBackOfficeMenuTiposCarpetas = $objBackOfficeSecciones->arrTiposCarpetas;

	$objTemplate->set_var(array(
		"textoCarpeta" => HTMLEntitiesFixed(html_entity_decode($strNombreCarpeta)),
		"textoCarpetaReal" => HTMLEntitiesFixed(html_entity_decode($strNombreCarpeta)),
		"linkCarpeta" => $strLink,
		"iconCarpeta" => (isset($arrBackOfficeMenuTiposCarpetas[$intTipo])) ? $arrBackOfficeMenuTiposCarpetas[$intTipo] : "dashboard",
		"strClassActive" => ($blnActive) ? "exp active" : "exp",
		"itemsCarpeta" => ($intItems) ? $intItems : ""
	));
	if ($intItems){
		$objTemplate->parse("menu_carpeta_items", "MENU_CARPETA_ITEMS");
	}else{
		$objTemplate->set_var("menu_carpeta_items", "");
	}

	$objTemplate->parse(strToLower("menu_item_principal_subitems"), "MENU_CARPETA", true);
	$strBackOfficeMenuSeccion .= $objTemplate->get_var("menu_item_principal_subitems");
}

function addBackofficeMenuItem($strItem, $strLink = "#", $blnActive = false, $intTipo = "dashboard"){
	global $objTemplate;
	global $objBackOfficeSecciones;
	global $strBackOfficeMenuSeccion;

	$arrBackOfficeMenuTiposCarpetas = $objBackOfficeSecciones->arrTiposCarpetas;

	$objTemplate->set_var(array(
		"textoItem" => HTMLEntitiesFixed(html_entity_decode($strItem)),
		"linkItem" => $strLink,
		"iconItem" => (isset($arrBackOfficeMenuTiposCarpetas[$intTipo])) ? $arrBackOfficeMenuTiposCarpetas[$intTipo] : "dashboard",
		"strClassActive", ($blnActive) ? ' class="active"' : ''
	));
	$objTemplate->parse(strToLower("menu_item_principal_subitems"), "MENU_ITEM", true);
	$strBackOfficeMenuSeccion .= $objTemplate->get_var("menu_item_principal_subitems");
}

function setBackofficeEncabezado($strTitulo, $strDetalle, $strTexto){
	global $objTemplate;

	$objTemplate->set_var("PAGINA_TITULO", $strTitulo);
	$objTemplate->set_var("PAGINA_TITULO_DETALLE", ($strDetalle) ? $strDetalle : "");
	$objTemplate->set_var("PAGINA_DESCRIPCION", $strTexto);
}

/****************************************************************************/
/*                       END FUNCIONES DE MENU                              */
/****************************************************************************/

/****************************************************************************/
/*                     BEGIN FUNCIONES DE IMAGES                            */
/****************************************************************************/
$arrImageTypes = array(
	1 => 'GIF',
	2 => 'JPG',
	3 => 'PNG',
	4 => 'SWF',
	5 => 'PSD',
	6 => 'BMP',
	7 => 'TIFF(intel byte order)',
	8 => 'TIFF(motorola byte order)',
	9 => 'JPC',
	10 => 'JP2',
	11 => 'JPX',
	12 => 'JB2',
	13 => 'SWC',
	14 => 'IFF',
	15 => 'WBMP',
	16 => 'XBM'
);

function convertImageName($strFoto){
	$intPosicion = strrpos($strFoto, ".");

	if ($intPosicion)
		$strExtension = strToLower(substr($strFoto, $intPosicion + 1));
	else
		$strExtension = strToLower(substr($strFoto, strlen($strFoto) - 3));

	if ($strExtension == "jpe")
		$strExtension = "jpg";

	return md5($strFoto . microtime()) . "." . $strExtension;
}

function checkUploadedImage($strFoto, $strFotoAnterior, $strPath, $strError){
	if ((is_array($strFoto)) && !$strError){
		if ($strFoto["name"] != "") {
			// Chequeo si en realidad es una imagen
			if (file_exists($strFoto["tmp_name"])){
				$arrImageData = @getImageSize($strFoto["tmp_name"]);
				if ($arrImageData && ($arrImageData[2] == IMAGETYPE_GIF || $arrImageData[2] == IMAGETYPE_JPEG || 
					$arrImageData[2] == IMAGETYPE_PNG || $arrImageData[2] == IMAGETYPE_BMP)){

					$strFotoNombre = convertImageName($strFoto["name"]);
					if (move_uploaded_file($strFoto["tmp_name"], $strPath . "/" . $strFotoNombre)){
						chmod($strPath . "/" . $strFotoNombre, 0755);
						deleteImagen($strPath, $strFotoAnterior);
					}else
						$strFotoNombre = $strFotoAnterior;
				}else
					$strFotoNombre = $strFotoAnterior;
			}else
				$strFotoNombre = $strFotoAnterior;
		}else
			$strFotoNombre = "";
	}else if (is_array($strFoto))
		$strFotoNombre = $strFotoAnterior;
	else
		$strFotoNombre = $strFotoAnterior;

	return $strFotoNombre;
}

function deleteImagen($strPath, $strImagen){
	/* Me fijo que la imagen no sea una imagen por defecto */
	if ($strImagen && $strImagen != IMAGEN_NO_DISPONIBLE && $strImagen != IMAGEN_NO_DISPONIBLE_PARRAFO){
		/* Me fijo que la imagen exista */
		if (file_exists($strPath . "/".$strImagen))
			unlink($strPath . "/".$strImagen);
	}
}

function checkUploadedAttachment($strFile, $strPath){
	if ((is_array($strFile))){
		if ($strFile["name"] != ""){
			$strFileName = convertImageName($strFile["name"]);
			if (move_uploaded_file($strFile["tmp_name"], $strPath . "/" . $strFileName)){
				chmod($strPath . "/" . $strFileName, 0755);
			}else
				$strFileName = "ERROR";
		}else
			$strFileName = "";
	}

	return $strFileName;
}

function checkUploadedFlashVideo($strFile, $strPath){
	if ((is_array($strFile))){
		if ($strFile["name"] != ""){
			// Chequeo que sea FLV
			$strFileName = $strFile["name"];
			$intPosicion = strrpos($strFileName, ".");
			$strExtension = ($intPosicion) ? strToLower(substr($strFileName, $intPosicion + 1)) : strToLower(substr($strFileName, strlen($strFileName) - 3));
			if (strtolower($strExtension) == "flv"){
				$strFileName = convertImageName($strFile["name"]);
				if (move_uploaded_file($strFile["tmp_name"], $strPath . "/" . $strFileName)){
					chmod($strPath . "/" . $strFileName, 0755);
				}else
					$strFileName = "ERROR";
			}else
				$strFileName = "NOT_FLV";
		}else
			$strFileName = "";
	}

	return $strFileName;
}

function checkUploadedVideo($strFile, $strPath){
	if ((is_array($strFile))){
		if ($strFile["name"] != ""){
			// Chequeo que sea FLV o MP4
			$strFileName = $strFile["name"];
			$intPosicion = strrpos($strFileName, ".");
			$strExtension = ($intPosicion) ? strToLower(substr($strFileName, $intPosicion + 1)) : strToLower(substr($strFileName, strlen($strFileName) - 3));
			if (strtolower($strExtension) == "flv" || strtolower($strExtension) == "mp4"){
				$strFileName = convertImageName($strFile["name"]);
				if (move_uploaded_file($strFile["tmp_name"], $strPath . "/" . $strFileName)){
					chmod($strPath . "/" . $strFileName, 0755);
				}else
					$strFileName = "ERROR";
			}else
				$strFileName = "NOT_VALID_EXT";
		}else
			$strFileName = "";
	}

	return $strFileName;
}

function deleteFile($strPath, $strArchivo){
	/* Me fijo que sea un archivo */
	if ($strArchivo){
		/* Me fijo que la imagen exista */
		if (file_exists($strPath . "/". $strArchivo))
			unlink($strPath . "/". $strArchivo);
	}
}

function getFileType($strFile){
	$strFileType = "";

	// Obtengo la Extension
	$arrExtension = explode(".", $strFile);
	$strExtension = $arrExtension[sizeOf($arrExtension) - 1];

	// Me fijo las mas conocidas
	switch ($strExtension){
		case "ai";
			$strFileType = "application/postscript";
			break;
		case "avi";
			$strFileType = "video/x-msvideo";
			break;
		case "bmp";
			$strFileType = "image/bmp";
			break;
		case "css";
			$strFileType = "text/css";
			break;
		case "doc";
			$strFileType = "application/msword";
			break;
		case "exe";
			$strFileType = "application/octet-stream";
			break;
		case "gif";
			$strFileType = "image/gif";
			break;
		case "htm";
		case "html";
			$strFileType = "text/html";
			break;
		case "jpe";
		case "jpeg";
		case "jpg";
			$strFileType = "image/jpeg";
			break;
		case "mp3";
			$strFileType = "audio/mpeg";
			break;
		case "mpe";
		case "mpeg";
		case "mpg";
			$strFileType = "video/mpeg";
			break;
		case "wav";
			$strFileType = "audio/x-wav";
			break;
		case "mov";
			$strFileType = "video/quicktime";
			break;
		case "mp3";
			$strFileType = "audio/mpeg";
			break;
		case "txt";
			$strFileType = "text/plain";
			break;
		case "xls";
			$strFileType = "application/vnd.ms-excel";
			break;
		case "zip";
			$strFileType = "application/zip";
			break;
		default:
			$strFileType = "application/octet-stream";
			break;
	}

	return $strFileType;
}

// Resizeo la imagen a un tamaño especifico
function resizeImage($strPath, $strFoto, $strFotoAnterior, $intAncho, $intAlto, $strNombre = ""){
	global $arrImageTypes;

	if (is_array($strFoto)){
		if ($strFoto["name"] != "") {
			$strFotoNombre = ($strNombre) ? $strNombre : convertImageName($strFoto["name"]);

			// Levanto el tamaño de la foto subida
			list($intWidth, $intHeight, $intType) = getImageSize($strFoto["tmp_name"]);
			if (!$intWidth || !$intHeight)
				return $strFotoAnterior;

			$intAnchoImage = $intAncho;
			$intAltoImage = round($intAncho * $intHeight / $intWidth);
			$intX = 0;
			$intY = 0;
			$intXResta = 0;
			$intYResta = 0;

			// Si el alto de la foto es menor al alto estipulado, hago cropping del ancho
			if ($intAltoImage < $intAlto){
				$intAnchoImage = round($intWidth * $intAlto / $intHeight);
				$intAltoImage = $intAlto;
				// Si el ancho es mayor, cropeo en el medio
				if ($intAnchoImage > $intAncho){
					$intXResta = ($intAncho - $intAnchoImage);
					$intX = floor(($intAncho - $intAnchoImage) / 2);
					$intAnchoImage = $intAncho;
				}
			}else{
				// Cropeo el Alto
				if ($intAltoImage > $intAlto){
					$intYResta = ($intAlto - $intAltoImage);
					$intY = floor(($intAlto - $intAltoImage) / 2);
					$intAltoImage = $intAlto;
				}
			}

			// Genero las fotos chicas y medianas
			switch($arrImageTypes[$intType]){
				case "GIF":
					$objSourceImage = imageCreateFromGIF($strFoto["tmp_name"]);
					break;
				case "JPG":
					$objSourceImage = imageCreateFromJPEG($strFoto["tmp_name"]);
					break;
				case "PNG":
					$objSourceImage = imageCreateFromPNG($strFoto["tmp_name"]);
					imageantialias($objSourceImage, true);
					break;
				default:
					return $strFotoNombre = "";
					break;
			}

			$objImagen = imageCreateTrueColor($intAnchoImage, $intAltoImage);
			$background = imagecolorallocate($objImagen, 0, 0, 0);
			imagecolortransparent($objImagen, $background);
			imagealphablending($objImagen, false);
			imagesavealpha($objImagen, true); 

			// Resize
			imageCopyResampled($objImagen, $objSourceImage, $intX, $intY, 0, 0, $intAnchoImage - $intXResta, $intAltoImage - $intYResta, $intWidth, $intHeight);

			// Output
			switch($arrImageTypes[$intType]){
				case "GIF":
					imageGIF($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "JPG":
					imageJPEG($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "PNG":
					imagePNG($objImagen, $strPath . "/" . $strFotoNombre, 7);
					break;
			}
			imageDestroy($objImagen);

		}else
			$strFotoNombre = "";
	}else{
		$strFotoNombre = $strFotoAnterior;
	}

	return $strFotoNombre;
}

// Resizeo la imagen a un ancho especifico
function resizeImageWidth($strPath, $strFoto, $strFotoAnterior, $intAncho, $intAlto, $strNombre = ""){
	global $arrImageTypes;

	if (is_array($strFoto)){
		if ($strFoto["name"] != "") {
			$strFotoNombre = ($strNombre) ? $strNombre : convertImageName($strFoto["name"]);

			// Levanto el tamaï¿½o de la foto subida
			list($intWidth, $intHeight, $intType) = getImageSize($strFoto["tmp_name"]);
			if (!$intWidth || !$intHeight)
				return $strFotoAnterior;

			$intAnchoImage = $intAncho;
			$intAltoImage = round($intAncho * $intHeight / $intWidth);
			$intX = 0;
			$intY = 0;
			$intXResta = 0;
			$intYResta = 0;

			// Si el alto de la foto es menor al alto estipulado, hago cropping del ancho
			if ($intAltoImage < $intAlto){
				$intAnchoImage = round($intWidth * $intAlto / $intHeight);
				$intAltoImage = $intAlto;
				// Si el ancho es mayor, cropeo en el medio
				if ($intAnchoImage > $intAncho){
					$intXResta = ($intAncho - $intAnchoImage);
					$intX = floor(($intAncho - $intAnchoImage) / 2);
					$intAnchoImage = $intAncho;
				}
			}

			// Genero las fotos chicas y medianas
			switch($arrImageTypes[$intType]){
				case "GIF":
					$objSourceImage = imageCreateFromGIF($strFoto["tmp_name"]);
					break;
				case "JPG":
					$objSourceImage = imageCreateFromJPEG($strFoto["tmp_name"]);
					break;
				case "PNG":
					$objSourceImage = imageCreateFromPNG($strFoto["tmp_name"]);
					break;
				default:
					return $strFotoNombre = "";
					break;
			}

			$objImagen = imageCreateTrueColor($intAnchoImage, $intAltoImage);
			$background = imagecolorallocate($objImagen, 0, 0, 0);
			imagecolortransparent($objImagen, $background);
			imagealphablending($objImagen, false);
			imagesavealpha($objImagen, true); 

			// Resize
			imageCopyResampled($objImagen, $objSourceImage, $intX, $intY, 0, 0, $intAnchoImage - $intXResta, $intAltoImage - $intYResta, $intWidth, $intHeight);

			// Output
			switch($arrImageTypes[$intType]){
				case "GIF":
					imageGIF($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "JPG":
					imageJPEG($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "PNG":
					imagePNG($objImagen, $strPath . "/" . $strFotoNombre, 0);
					break;
			}
			imageDestroy($objImagen);

		}else
			$strFotoNombre = $strFotoAnterior;
	}else{
		$strFotoNombre = $strFotoAnterior;
	}

	return $strFotoNombre;
}

// Resizeo la imagen a un alto especifico
function resizeImageHeight($strPath, $strFoto, $strFotoAnterior, $intAncho, $intAlto){
	global $arrImageTypes;

	if (is_array($strFoto)){
		if ($strFoto["name"] != "") {
			$strFotoNombre = convertImageName($strFoto["name"]);

			// Levanto el tamaï¿½o de la foto subida
			list($intWidth, $intHeight, $intType) = getImageSize($strFoto["tmp_name"]);
			if (!$intWidth || !$intHeight)
				return $strFotoAnterior;

			$intAnchoImage = round($intWidth * $intAlto / $intHeight);
			$intAltoImage = $intAlto;
			$intX = 0;
			$intY = 0;
			$intXResta = 0;
			$intYResta = 0;

			// Si el ancho de la foto es mayor al ancho maximo, hago cropping del ancho
			if ($intAnchoImage > $intAncho){
				$intAnchoImage = $intAncho;
				$intAltoImage = round($intAncho * $intHeight / $intWidth);
			}

			// Genero las fotos chicas y medianas
			switch($arrImageTypes[$intType]){
				case "GIF":
					$objSourceImage = imageCreateFromGIF($strFoto["tmp_name"]);
					break;
				case "JPG":
					$objSourceImage = imageCreateFromJPEG($strFoto["tmp_name"]);
					break;
				case "PNG":
					$objSourceImage = imageCreateFromPNG($strFoto["tmp_name"]);
					break;
				default:
					return $strFotoNombre = "";
					break;
			}

			$objImagen = imageCreateTrueColor($intAnchoImage, $intAltoImage);
			$background = imagecolorallocate($objImagen, 0, 0, 0);
			imagecolortransparent($objImagen, $background);
			imagealphablending($objImagen, false);
			imagesavealpha($objImagen, true); 

			// Resize
			imageCopyResampled($objImagen, $objSourceImage, $intX, $intY, 0, 0, $intAnchoImage - $intXResta, $intAltoImage - $intYResta, $intWidth, $intHeight);

			// Output
			switch($arrImageTypes[$intType]){
				case "GIF":
					imageGIF($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "JPG":
					imageJPEG($objImagen, $strPath . "/" . $strFotoNombre, 90);
					break;
				case "PNG":
					imagePNG($objImagen, $strPath . "/" . $strFotoNombre, 0);
					break;
			}
			imageDestroy($objImagen);

		}else
			$strFotoNombre = $strFotoAnterior;
	}else{
		$strFotoNombre = $strFotoAnterior;
	}

	return $strFotoNombre;
}

// Resizeo la imagen solo si es mas grande de un tamaño
function resizeImageSpecial($strPath, $strFoto, $strFotoAnterior, $intAncho, $intAlto, $strNombre = ""){
	global $arrImageTypes;
	if (is_array($strFoto)){
		if ($strFoto["name"] != "") {
			$strFotoNombre = ($strNombre) ? $strNombre : convertImageName($strFoto["name"]);

			// Levanto el tamaño de la foto subida
			list($intWidth, $intHeight, $intType) = getImageSize($strFoto["tmp_name"]);

			$blnModifyImage = false;
			if ($intWidth > $intAncho){
				$intAnchoImage = $intAncho;
				$intAltoImage = round($intAncho * $intHeight / $intWidth);
				$intX = 0;
				$intY = 0;
				$blnModifyImage = true;
			}
			if ($intHeight > $intAlto){
				$intAltoImage = $intAlto;
				$intAnchoImage = round($intAlto * $intWidth / $intHeight);
				$intX = 0;
				$intY = 0;
				$blnModifyImage = true;
			}

			if ($blnModifyImage){
				// Genero las fotos chicas y medianas
				switch($arrImageTypes[$intType]){
					case "GIF":
						$objSourceImage = imageCreateFromGIF($strFoto["tmp_name"]);
						break;
					case "JPG":
						$objSourceImage = imageCreateFromJPEG($strFoto["tmp_name"]);
						break;
					case "PNG":
						$objSourceImage = imageCreateFromPNG($strFoto["tmp_name"]);
						break;
					default:
						return $strFotoNombre = "";
						break;
				}

				imageantialias($objSourceImage, true);
				$objImagen = imageCreateTrueColor($intAnchoImage, $intAltoImage);

				if ($arrImageTypes[$intType] == "PNG"){
					imagesavealpha($objImagen, true);
					$objTransparentColor = imagecolorallocatealpha($objImagen, 255, 255, 255, 127);
					imagefill($objImagen, 0, 0, $objTransparentColor);
				}

				// Resize
				imageCopyResampled($objImagen, $objSourceImage, $intX, $intY, 0, 0, $intAnchoImage, $intAltoImage, $intWidth, $intHeight);

				// Output
				switch($arrImageTypes[$intType]){
					case "GIF":
						imageGIF($objImagen, $strPath . "/" . $strFotoNombre, 90);
						break;
					case "JPG":
						imageJPEG($objImagen, $strPath . "/" . $strFotoNombre, 90);
						break;
					case "PNG":
						imagePNG($objImagen, $strPath . "/" . $strFotoNombre, 8);
						break;
				}
				imageDestroy($objImagen);
			}else{
				if (move_uploaded_file($strFoto["tmp_name"], $strPath . "/" . $strFotoNombre)){
					chmod($strPath . "/" . $strFotoNombre, 0755);
					deleteImagen($strPath, $strFotoAnterior);
				}else{
					$strFotoNombre = $strFotoAnterior;
				}
			}

		}else
			$strFotoNombre = "";
	}else{
		$strFotoNombre = $strFotoAnterior;
	}

	return $strFotoNombre;
}

function imageAlphaMask(&$picture, $mask){
    // Get sizes and set up new picture
    $xSize = imagesx($picture);
    $ySize = imagesy($picture);
    $newPicture = imagecreatetruecolor($xSize, $ySize);
    imagesavealpha($newPicture, true);
    imagefill($newPicture, 0, 0, imagecolorallocatealpha($newPicture, 0, 0, 0, 127));

    // Resize mask if necessary
    if ($xSize != imagesx($mask) || $ySize != imagesy($mask)){
        $tempPic = imagecreatetruecolor($xSize, $ySize);
        imagecopyresampled($tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx($mask), imagesy($mask));
        imagedestroy($mask);
        $mask = $tempPic;
    }

    // Perform pixel-based alpha map application
    for ($x = 0; $x < $xSize; $x++){
        for ($y = 0; $y < $ySize; $y++){
            $alpha = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
            $color = imagecolorsforindex($picture, imagecolorat($picture, $x, $y));
			$alpha = 127 - floor((127 - $color['alpha']) * ($alpha['red'] / 255));
            imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha($newPicture, $color['red'], $color['green'], $color['blue'], $alpha));
        }
    }

    // Copy back to original picture
    imagedestroy( $picture );
    $picture = $newPicture;
	return $newPicture;
}

// Apply Mask to Image
function applyMaskToImage($picture, $mask){
	/*// Get sizes and set up new picture
    $xSize = imagesx( $picture );
    $ySize = imagesy( $picture );
    $newPicture = imagecreatetruecolor( $xSize, $ySize );
    imagesavealpha( $newPicture, true );
    imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

    // Resize mask if necessary
    if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
        $tempPic = imagecreatetruecolor( $xSize, $ySize );
        imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
        imagedestroy( $mask );
        $mask = $tempPic;
    }

    // Perform pixel-based alpha map application
    for( $x = 0; $x < $xSize; $x++ ) {
        for( $y = 0; $y < $ySize; $y++ ) {
            $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
			$color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
			$alpha = 127 - floor((127-$color['alpha']) * ($alpha[ 'red' ]/255));
            imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
        }
    }*/

    // Get sizes and set up new picture
    $xSize = imagesx( $picture );
    $ySize = imagesy( $picture );
    $newPicture = imagecreatetruecolor( $xSize, $ySize );
    imagesavealpha( $newPicture, true );
    imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

    // Resize mask if necessary
    if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
        $tempPic = imagecreatetruecolor( $xSize, $ySize );
        imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
        imagedestroy( $mask );
        $mask = $tempPic;
    }

    // Perform pixel-based alpha map application
    for( $x = 0; $x < $xSize; $x++ ) {
        for( $y = 0; $y < $ySize; $y++ ) {
            $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
            //small mod to extract alpha, if using a black(transparent) and white
            //mask file instead change the following line back to Jules's original:
            //$alpha = 127 - floor($alpha['red'] / 2);
            //or a white(transparent) and black mask file:
            //$alpha = floor($alpha['red'] / 2);
            $alpha = $alpha['alpha'];
            $color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
            //preserve alpha by comparing the two values
            if ($color['alpha'] > $alpha)
                $alpha = $color['alpha'];
            //kill data for fully transparent pixels
            if ($alpha == 127) {
                $color['red'] = 0;
                $color['blue'] = 0;
                $color['green'] = 0;
            }
            imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
        }
    }

	return $newPicture;
}

// Scrap URL
function getURLResponse($strURL){
	if (!$strURL)
		return "";

	// cURL for URL
	$objCurl = curl_init();
	$strUserAgent = 'Mozilla/5.0 (X11; U; SunOS sun4u; en-US; rv:1.0.1) Gecko/20020921 Netscape/7.0';

	// Start Scraping
	curl_setopt($objCurl, CURLOPT_URL, $strURL);
	curl_setopt($objCurl, CURLOPT_USERAGENT, $strUserAgent);
	curl_setopt($objCurl, CURLOPT_HEADER, 0);
	curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

	// End Scraping
	$strPageContent = curl_exec ($objCurl);
	curl_close ($objCurl);
	unset($objCurl);

	return $strPageContent;
}

// Get Video Code from YouTube URL
function getYouTubeCode($strURL){
	if (!$strURL)
		return "1";

	if (strToLower(substr($strURL, 0, 31)) != "http://www.youtube.com/watch?v=")
		return false;

	$strYouTubeCode = "";
	$intInicio1 = strpos($strURL, "?v=");
	$intCasifin = strpos($strURL, "&", $intInicio1 + 3);
	$intFin = ($intCasifin !== false) ? $intCasifin - $intInicio1 - 3 : 0;

	$strYouTubeCode = ($intCasifin !== false) ? substr($strURL, $intInicio1 + 3, $intFin) : substr($strURL, $intInicio1 + 3);

	return $strYouTubeCode;
}

// Get Video FLV URL from YouTube URL
function getYouTubeFLV($strURL){
	if (!$strURL)
		return "1";

	if (strToLower(substr($strURL, 0, 31)) != "http://www.youtube.com/watch?v=")
		return false;

	$strArgumentoV = getYouTubeCode($strURL);

	// Scrap for 2do Parameter
	$strPageContent = getURLResponse($strURL);

	$strArgumentoT = "";
	$intInicio = strpos($strPageContent, "swfArgs");
	$intIinicio1 = strpos($strPageContent, '"t": ', $intInicio);
	$intCasifin = strpos($strPageContent, '",', $intIinicio1 + 6);
	$intFin = ($intCasifin !== false) ? $intCasifin - $intIinicio1 - 6: 0;

	$strArgumentoT = substr($strPageContent, $intIinicio1 + 6, $intFin);

	if ($strArgumentoV && $strArgumentoT){
		$strFinalYoutubeURL = "http://www.youtube.com/get_video?video_id=" . $strArgumentoV ."&t=" . $strArgumentoT . "&el=detailpage&ps=&fmt=34";
		return $strFinalYoutubeURL;
	}else{
		return false;
	}
}

function getYouTubeImagenVideo($strURL){
	if (!$strURL)
		return "1";

	if (strToLower(substr($strURL, 0, 31)) != "http://www.youtube.com/watch?v=")
		return false;

	// Get Video Code
	$strYouTubeCode = getYouTubeCode($strURL);

	// Scrap for YouTube API
	$strYouTubeXML = getURLResponse("http://gdata.youtube.com/feeds/api/videos/" . $strYouTubeCode);

	// Read Response as XML
	$objXMLReader = new XMLReader();
	$blnXMLReaded = $objXMLReader->XML($strYouTubeXML);
	//$blnXMLReaded = $objXMLReader->open("http://gdata.youtube.com/feeds/api/videos/" . $strYouTubeCode);

	// Get Image from XML
	$strImageVideo = "";
	if ($blnXMLReaded){
		while ($objXMLReader->read() && !$strImageVideo){
			if ($objXMLReader->name == "media:thumbnail"){
				$strImageVideo = $objXMLReader->getAttribute("url");
			}
		}
	}

	return $strImageVideo;
}

/****************************************************************************/
/*                       END FUNCIONES DE IMAGES                            */
/****************************************************************************/

/****************************************************************************/
/*                       BEGIN FUNCIONES DE TEXTO                           */
/****************************************************************************/

function HTMLEntitiesFixed($strText){
	return HTMLEntities($strText, ENT_QUOTES, 'ISO-8859-1');
}

function capitalize($strText){
	return mb_strToUpper(substr($strText, 0, 1)) . mb_strToLower(substr($strText, 1));
}

function capitalizeFirst($strText){
	return mb_strToUpper(substr($strText, 0, 1)) . substr($strText, 1);
}

function capitalizeAll($strText){
	$arrText = explode(" ", $strText);
	$strFinalText = "";
	for ($i = 0; $i < sizeOf($arrText); $i++){
		$strFinalText .= mb_strToUpper(substr($arrText[$i], 0, 1)) . mb_strToLower(substr($arrText[$i], 1)) . " ";
	}
	return trim($strFinalText);
}

function cutText($strText, $intCaracteres){
	/* Me fijo si el texto tiene mas de x lineas */
	$intLineasACortar = 4;
	$intCaracteresACortar = 0;
	$intBreaks = 0;

	/* Me fijo cuantos fin de linea hay */
	if (substr_count($strText, "<br>") > $intLineasACortar){
		for ($i = 0; $i < $intLineasACortar; $i++){
			$intCaracteresLinea = strpos($strText, "<br>", ($i != 0) ? ($intCaracteresACortar + 1) : 0);
			$intCaracteresACortar = ($intCaracteresLinea) ? $intCaracteresLinea : $intCaracteresACortar;
			$intBreaks++;
		}

		if ($intBreaks >= $intLineasACortar && $intCaracteresACortar)
			$strText = substr($strText, 0, $intCaracteresACortar) . " ...";
	}

	/* Si el Texto es mas largo que la cantidad de caracteres a mostrar, lo corto */
	if ((strLen($strText) > $intCaracteres + 2) && $strText && $intCaracteres){
		/* Me fijo si existe un punto dentro del rango del corte */
		$intRango = (intval($intCaracteres / 10) >= 2) ? intval($intCaracteres / 10) : 0;
		$intCorte = strpos($strText, ".", $intCaracteres - $intRango);

		if ((!$intCorte) || (($intCorte > $intCaracteres) && ($intCorte - $intCaracteres) > $intRango)){
			/* Me fijo si existe un espacio dentro del rango del corte */
			$intCorte = strpos($strText, " ", ($intCaracteres) ? ($intCaracteres - $intRango): 0);
			if ((!$intCorte) || (($intCorte > $intCaracteres) && ($intCorte - $intCaracteres) > $intRango))
				$strTextCortado = substr($strText, 0, $intCaracteres);
			else
				$strTextCortado = substr($strText, 0, $intCorte);

		}else
			$strTextCortado = substr($strText, 0, $intCorte);

		$strTextCortado .= " ...";
	}else
		$strTextCortado = $strText;

	return trim($strTextCortado);
}

function stringToSQL($strString){
	$strString = stripslashes($strString);
	return str_replace("'", "''", $strString);
}

function showTextBreaks($strText, $blnMode = false){
	if ($blnMode)
		return str_replace("\n", "{NEWLINE}", $strText);
	else
		return str_replace("\n", "<br>", $strText);
}

function removeSpecialChars($strString){
	$strString = str_replace("á", "a", $strString);
	$strString = str_replace("é", "e", $strString);
	$strString = str_replace("í", "i", $strString);
	$strString = str_replace("ó", "o", $strString);
	$strString = str_replace("ú", "u", $strString);
	$strString = str_replace("Á", "A", $strString);
	$strString = str_replace("É", "E", $strString);
	$strString = str_replace("Í", "I", $strString);
	$strString = str_replace("Ó", "O", $strString);
	$strString = str_replace("Ú", "U", $strString);
	return $strString;
}

function cleanText($strTexto){
	$strTexto = str_replace("’", "'", $strTexto);
	$strTexto = str_replace("`", "'", $strTexto);
	$strTexto = str_replace("”", "\"", $strTexto);
	$strTexto = str_replace("“", "\"", $strTexto);

	return $strTexto;
}

function convertToValidURL($strURL){
	if (substr($strURL, 0, 4) == "www."){
		$strURL = "http://" . $strURL;
	}

	return $strURL;
}

function convertUrlToLink($strString){
	$strString = preg_replace("((http://|ftp://)?([a-zA-Z0-9\-\_\.]+\.[a-zA-Z0-9\-\_]+\.[a-zA-Z0-9\-\_\.\?=&\/%]{2,}(/[a-z\-\_/\.\?=&]+)?))", "<a href=\"\\1\\2\" class=\"texto\" id=\"bold\" target=\"_blank\">\\0</a>", $strString);
	return $strString;
}

function replaceTextForImageMailing($strText){
	// Convert UTF-8 string to HTML entities
	$strText = mb_convert_encoding($strText, 'HTML-ENTITIES', "UTF-8");

	// Convert HTML entities into ISO-8859-1
	$strText = html_entity_decode($strText, ENT_NOQUOTES, "ISO-8859-1");

	$strTextOut = "";
	// Convert characters > 127 into their hexidecimal equivalents
	for ($i = 0; $i < strlen($strText); $i++){
		$strLetter = $strText[$i];
		$intChar = ord($strLetter);
		if ($intChar > 127){
			$strTextOut .= "&#$num;";
		}else{
			$strTextOut .=  $strLetter;
		}
	}

	return $strTextOut;
}

function makeTextBlock($text, $fontfile, $fontsize, $width){
	$breaks = explode("\n", $text);
	$linesFinal = "";

	for ($j = 0; $j < sizeOf($breaks); $j++){
		$words = explode(' ', $breaks[$j]);
		$lines = array($words[0]);
		$currentLine = 0;

		for ($i = 1; $i < sizeOf($words); $i++){
			$lineSize = imagettfbbox($fontsize, 0, $fontfile, $lines[$currentLine] . ' ' . $words[$i]);
			if ($lineSize[2] - $lineSize[0] < $width){
				$lines[$currentLine] .= ' ' . $words[$i];
			}else{
				$currentLine++;
				$lines[$currentLine] = $words[$i];
			}
		}
		$strLines = implode("\n", $lines);

		$linesFinal .= (($linesFinal) ? "\n" : "") . $strLines;
	}

	return $linesFinal;
}

function getWidthOfText($strText, $strPathToFont, $intFontSize, $intFontAngle = 0){
	$arrDimensions = imagettfbbox($intFontSize, $intFontAngle, $strPathToFont, $strText);
	$intWidth = $arrDimensions[2] - $arrDimensions[0];
	return $intWidth;
}

function perfect_base_convert($numstring, $frombase, $tobase){

   $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
   $tostring = substr($chars, 0, $tobase);

   $length = strlen($numstring);
   $result = '';
   for ($i = 0; $i < $length; $i++) {
       $number[$i] = strpos($chars, $numstring{$i});
   }
   do {
       $divide = 0;
       $newlen = 0;
       for ($i = 0; $i < $length; $i++) {
           $divide = $divide * $frombase + $number[$i];
           if ($divide >= $tobase) {
               $number[$newlen++] = (int)($divide / $tobase);
               $divide = $divide % $tobase;
           } elseif ($newlen > 0) {
               $number[$newlen++] = 0;
           }
       }
       $length = $newlen;
       $result = $tostring{$divide} . $result;
   }
   while ($newlen != 0);
   return $result;
}

function encodeNumber($intNumber){
	$arrNumbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$arrLetters = array("b", "z", "f", "h", "q", "r", "y", "m", "p", "d");

	$strNumber = str_replace("0", "9", perfect_base_convert(substr(md5($intNumber), 25), 36, 10)) . "0" . $intNumber;
	$strNumber = strToUpper(perfect_base_convert($strNumber, 10, 17));
	$strNumberFinal = str_replace($arrNumbers, $arrLetters, $strNumber);
	$strNumberFinal = strrev($strNumberFinal);

	return $strNumberFinal;
}

function decodeNumber($strNumber){
	$arrNumbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$arrLetters = array("b", "z", "f", "h", "q", "r", "y", "m", "p", "d");

	$strNumber = strrev($strNumber);
	$strNumberFinal = str_replace($arrLetters, $arrNumbers, $strNumber);
	$strNumberFinal = perfect_base_convert(strToLower($strNumberFinal), 17, 10);
	$intPosCero = strpos($strNumberFinal, "0");
	$strNumberFinal = substr($strNumberFinal, $intPosCero + 1);

	return $strNumberFinal;
}

function getUserIP(){
	$strIP = $_SERVER['REMOTE_ADDR'];

	return $strIP;
}

function formatNumber($intNumber, $intDecimals, $blnPuntuacion = false){
	$strNumber = number_format($intNumber, $intDecimals, ",", ($blnPuntuacion) ? "." : "");
	return $strNumber;
}

function getUniqueKeys($strArray){
	if (!$strArray)
		return array();

	$arrOrigen = explode(";", $strArray);
	if (!is_array($arrOrigen))
		return array($strArray);

	$arrOrigen = array_unique($arrOrigen);
	$arrDestino = array();

	for ($i = 0; $i < sizeOf($arrOrigen); $i++){
		if (isset($arrOrigen[$i]) && $arrOrigen[$i]){
			$arrDestino[] = $arrOrigen[$i];
		}
	}
	return $arrDestino;
}

function sortArrayMulti($array, $index, $order, $natsort = false, $case_sensitive = false){
	if (is_array($array) && count($array) > 0){
		foreach(array_keys($array) as $key){
			$temp[$key] = $array[$key][$index];
		}

		if (!$natsort){
			if ($order == 'asc')
				asort($temp);
			else
				arsort($temp);
		}else{
			if ($case_sensitive === true)
				natsort($temp);
			else
				natcasesort($temp);

			if ($order != 'asc')
				$temp = array_reverse($temp, true);
		}

		foreach(array_keys($temp) as $key){
			if (is_numeric($key))
				$sorted[] = $array[$key];
			else
				$sorted[$key] = $array[$key];
		}

		return $sorted;
	}

	return $array;
}

/****************************************************************************/
/*                        END FUNCIONES DE TEXTO                            */
/****************************************************************************/

function checkReferer($strPage){
	global $_SERVER;
	$arrReferer = explode("/", (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : "");
	$strHost = (sizeOf($arrReferer) >= 3) ? $arrReferer[2] : "";
	$arrReferer = explode("?", $arrReferer[sizeOf($arrReferer) - 1]);
	return (($arrReferer[0] == $strPage) && ($strHost == "uv9206.us22.toservers.com" || $strHost == "www.sentituvitalidad.com.ar" || $strHost == $_SERVER["HTTP_HOST"]));
}

function checkActualPage($strPage){
	global $_SERVER;
	$arrReferer = explode("/", (isset($_SERVER["PHP_SELF"])) ? $_SERVER["PHP_SELF"] : "");
	$strHost = (sizeOf($arrReferer) >= 3) ? $arrReferer[2] : "";
	$arrReferer = explode("?", $arrReferer[sizeOf($arrReferer) - 1]);
	return ($arrReferer[0] == $strPage);
}

function getActualURL(){
	global $_SERVER;
	$strPage = basename($_SERVER["PHP_SELF"]);
	$strQueryString = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : "";
	return ($strPage . (($strQueryString) ? "?" . $strQueryString : ""));
}

function redirect($strLink){
	Header("Location: " . $strLink);
	die();
}

function getFecha(){
	global $arrMeses;
	$arrTime = getDate(time());
	return substr("00" . $arrTime[ "mday"], -2) . " de " . $arrMeses[$arrTime["mon"] - 1] . " de " . $arrTime[ "year"];
}

function convertFechaToSpanish($strFecha){
	global $arrDias;
	global $arrMeses;

	$strFecha = str_replace("Monday", $arrDias[1], $strFecha);
	$strFecha = str_replace("Tuesday", $arrDias[2], $strFecha);
	$strFecha = str_replace("Wednesday", $arrDias[3], $strFecha);
	$strFecha = str_replace("Thursday", $arrDias[4], $strFecha);
	$strFecha = str_replace("Friday", $arrDias[5], $strFecha);
	$strFecha = str_replace("Saturday", $arrDias[6], $strFecha);
	$strFecha = str_replace("Sunday", $arrDias[0], $strFecha);

	$strFecha = str_replace("January", $arrMeses[0], $strFecha);
	$strFecha = str_replace("February", $arrMeses[1], $strFecha);
	$strFecha = str_replace("March", $arrMeses[2], $strFecha);
	$strFecha = str_replace("April", $arrMeses[3], $strFecha);
	$strFecha = str_replace("May", $arrMeses[4], $strFecha);
	$strFecha = str_replace("June", $arrMeses[5], $strFecha);
	$strFecha = str_replace("July", $arrMeses[6], $strFecha);
	$strFecha = str_replace("August", $arrMeses[7], $strFecha);
	$strFecha = str_replace("September", $arrMeses[8], $strFecha);
	$strFecha = str_replace("October", $arrMeses[9], $strFecha);
	$strFecha = str_replace("November", $arrMeses[10], $strFecha);
	$strFecha = str_replace("December", $arrMeses[11], $strFecha);

	return $strFecha;
}

function dateToSQL($strDate, $blnEndDay = false){
	$arrDate = explode("/", $strDate);
	if (sizeOf($arrDate) == 3){
		if (strLen($arrDate[2]) < 4){
			if (strLen($arrDate[2]) == 2)
				$strAnio = (($arrDate[2] < 10) ? "20" : "19") . $arrDate[2];
			else
				$strAnio = (($arrDate[2] < 100) ? "2" : "1") . $arrDate[2];
		}else
			$strAnio = $arrDate[2];
		$strMes = subStr("00" . $arrDate[1], -2);
		$strDia = subStr("00" . $arrDate[0], -2);
		return $strAnio . "/" . $strMes . "/" . $strDia . (($blnEndDay) ? " 23:59:59" : " 00:00:00");
	}else{
		return false;
	}
}

// Obtengo la version del Browser
$blnIsExplorer = (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false);

?>
