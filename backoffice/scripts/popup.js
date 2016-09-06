var arrWindows = new Array('');

function openPopup(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnCenteredX, blnCenteredY, blnShowBorders, blnScroll, blnStatus, blnResizable, strName, blnRefresh){
	if (arrWindows.length == 1)
		openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnCenteredX, blnCenteredY, blnShowBorders, blnScroll, blnStatus, blnResizable, strName, false, false);
	else{
		bolPopupFound = false;
		if (!blnRefresh){
			for (i = 1; i < arrWindows.length; i++){
				if (!arrWindows[i].closed){
					if (arrWindows[i].name == strName){
						arrWindows[i].focus();
						bolPopupFound = true;
						break;
					}
				}
			}
		}
		if (!bolPopupFound)
			openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnCenteredX, blnCenteredY, blnShowBorders, blnScroll, blnStatus, blnResizable, strName, false, arrWindows.length);
	}
}

function openPopupWindow(strURL, intAncho, intAlto, intDesfasajeAncho, intDesfasajeAlto, blnCenteredX, blnCenteredY, blnShowBorders, blnScroll, blnStatus, blnResizable, strName, blnPrintMode, intIndex){
	if (!blnPrintMode){
		/* Me fijo si tengo que centrar el eje X */
		if (blnCenteredX)
			strProperties = 'left=' + (((screen.availWidth - intAncho) / 2) + ((intDesfasajeAncho) ? intDesfasajeAncho : 0));
		else
			strProperties = 'left=' + ((intDesfasajeAncho) ? intDesfasajeAncho : 0);

		/* Me fijo si tengo que centrar el eje Y */
		if (blnCenteredY)
			strProperties += ',top=' + (((screen.availHeight - intAlto) / 2) + ((intDesfasajeAlto) ? intDesfasajeAlto : 0));
		else
			strProperties += ',top=' + ((intDesfasajeAlto) ? intDesfasajeAlto : 0);

		/* Completo las demas propiedades */
		strProperties += ',width=' + intAncho + ',height=' + intAlto;
		strProperties += (blnResizable) ? ',resizable=yes' : ',resizable=no';
		strProperties += (blnScroll) ? ',scrollbars=yes' : ',scrollbars=no';
		strProperties += (blnStatus) ? ',status=yes' : ',status=no';
		strProperties += (blnShowBorders) ? ',fullscreen=yes' : 'fullscreen=no';
		strProperties += ',maximize=no,menubar=no';
	}else
		strProperties = 'left=2000,top=2000,width=0,height=0,menubar=no,resizable=no,status=no';

	intWindow = (intIndex) ? intIndex : arrWindows.length;
	arrWindows[intWindow] = window.open(strURL, strName, strProperties);
	if (blnShowBorders || blnPrintMode){
		self.focus();
		setTimeout("arrWindows[" + intWindow + "].resizeTo(" + intAncho + "," + intAlto + ")", 50);
		setTimeout("arrWindows[" + intWindow + "].moveTo(" + (window.screen.width - intAncho) / 2 + ", " + (window.screen.height - intAlto) / 2 + ")", 50);
		setTimeout("arrWindows[" + intWindow + "].focus()", 50);
	}else
		arrWindows[intWindow].focus();
}

function openPopupPrint(strURL, strName){
	openPopupWindow(strURL, 0, 0, 0, 0, true, false, false, false, strName, true, false);
}

function checkOpenWindow(strWindowName){
	for (i = 1; i < arrWindows.length; i++){
		if (!arrWindows[i].closed){
			if (arrWindows[i].name == strWindowName){
				return i;
			}
		}
	}
	return false;
}

function focusWindow(strWindowName){
	intWindow = checkOpenWindow(strWindowName);
	if (intWindow)
		arrWindows[intWindow].focus();
}

function closeWindow(strWindowName){
	intWindow = checkOpenWindow(strWindowName);
	if (intWindow)
		arrWindows[intWindow].close();
}

function closeAllWindows(){
	for (i = 1; i < arrWindows.length; i++){
		if (!arrWindows[i].closed)
			arrWindows[i].close();
	}
}

