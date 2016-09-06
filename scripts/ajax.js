function AjaxCustom(){
	// Define XML HTTP Object
	var objXMLHTTP = false;
	try{
		objXMLHTTP = new ActiveXObject("Msxml2.XMLHTTP");
	}catch (e){
		try{
			objXMLHTTP = new ActiveXObject("Microsoft.XMLHTTP");
		}catch (E){
			objXMLHTTP = false;
		}
	}

	// Define XML HTTP Object for not IE Browsers
	if (!objXMLHTTP && typeof XMLHttpRequest!='undefined') {
		objXMLHTTP = new XMLHttpRequest();
	}
	return objXMLHTTP;
}