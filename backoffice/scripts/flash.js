function getSWFObject(strFile, strWidth, strHeight, strAlign, strVersion, strMenu, strBgColor, strId, strWindowMode, strClass){
	strFlashObject = "";
	strFlashObject += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	strFlashObject += 'codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=' + strVersion + '" ';
	strFlashObject += 'width="' + strWidth + '" height="' + strHeight + '" ';
	if (strAlign)
		strFlashObject += 'align="' + strAlign + '" ';
	if (strId != false)
		strFlashObject += 'id="' + strId + '" name="' + strId + '"';
	if (strClass)
		strFlashObject += 'class="' + strClass + '" ';
	strFlashObject += '>\n';
	strFlashObject += '<param name="movie" value="' + strFile + '">\n';
	strFlashObject += '<param name="quality" value="best">\n';
	strFlashObject += '<param name="menu" value="' + strMenu + '">\n';
	strFlashObject += '<param name="allowScriptAccess" value="always">\n';
	strFlashObject += '<param name="allowFullscreen" value="true">\n';
	if (strBgColor != false)
		strFlashObject += '<param name="bgcolor" value=' + strBgColor + '>\n';
	if (strWindowMode != false)
		strFlashObject += '<param name="wmode" value=' + strWindowMode + '>\n';
	strFlashObject += '	<embed src="' + strFile + '" menu="' + strMenu + '" allowScriptAccess="always" ';
	strFlashObject += '	quality="high" pluginspage="https://www.macromedia.com/go/getflashplayer" ';
	strFlashObject += '	width="' + strWidth + '" height="' + strHeight + '" ';
	if (strBgColor != false)
		strFlashObject += '	bgcolor="' + strBgColor + '" ';
	if (strWindowMode != false)
		strFlashObject += ' wmode="' + strWindowMode + '" ';
	if (strId != false)
		strFlashObject += '	id="' + strId + 'Embed" name="' + strId + 'Embed" ';
	strFlashObject += '	type="application/x-shockwave-flash" swLiveConnect="true" allowFullscreen="true"></embed>\n';
	strFlashObject += '</object>\n';

	return strFlashObject;
}

function createSWFObject(strFile, strWidth, strHeight, strAlign, strVersion, strMenu, strBgColor, strId, strWindowMode, strClass){
	strFlashObject = getSWFObject(strFile, strWidth, strHeight, strAlign, strVersion, strMenu, strBgColor, strId, strWindowMode, strClass);
	document.write(strFlashObject);
}
