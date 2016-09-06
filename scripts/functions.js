isDOM = (document.getElementById ? true : false);
isIE = (document.all ? true : false);
isDOMnotIE = (isDOM && !isIE);

function MM_swapImgRestore(){
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages(){
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d){
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage(){
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_showHideLayers() {
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}

function redirect(strURL){
	document.location.href = strURL;
}

function setStatus(strText){
	window.status = (strText) ? strText : "";
	return true;
}

function checkEmail(strEmail){
	var strRegExp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/;
	return strRegExp.test(strEmail);
}

function trim(strText){
	for (i = 0; i < strText.length; i++){
		if (strText.charAt(i) == " ")
			strText = strText.substring(i + 1, strText.length);
		else
			break;
	}
	
	for (i = strText.length - 1; i >= 0; i = strText.length - 1){
		if (strText.charAt(i) == " ")
			strText = strText.substring(0, i);
		else
			break;
	}
	return strText;
}

function escapeRegExp(str) {
	return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}
function replaceAll(find, replace, strString){
	return strString.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function shuffle(arrArray){
	for (var j, x, i = arrArray.length; i; j = parseInt(Math.random() * i), x = arrArray[--i], arrArray[i] = arrArray[j], arrArray[j] = x);
	return arrArray;
}

function URLEncode(clearString){
  var output = '';
  var x = 0;
  clearString = clearString.toString();
  var regex = /(^[a-zA-Z0-9_.]*)/;
  while (x < clearString.length) {
    var match = regex.exec(clearString.substr(x));
    if (match != null && match.length > 1 && match[1] != '') {
    	output += match[1];
      x += match[1].length;
    } else {
      if (clearString[x] == ' ')
        output += '+';
      else {
        var charCode = clearString.charCodeAt(x);
        var hexVal = charCode.toString(16);
        output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
      }
      x++;
    }
  }
  return output;
}

function URLDecode(encodedString){
	var output = encodedString;
	var binVal, thisString;
	var myregexp = /(%[^%]{2})/;
	while ((match = myregexp.exec(output)) != null
		&& match.length > 1
		&& match[1] != '') {
		binVal = parseInt(match[1].substr(1),16);
		thisString = String.fromCharCode(binVal);
		output = output.replace(match[1], thisString);
	}
		
	return output;
}

function shareOnFacebook(strURL, strTitle){
	strFacebookURL = "http://www.facebook.com/sharer.php?u=" + strURL + "&t=" + escape(UTF8.encode(strTitle));
	openPopup(strFacebookURL, 660, 400, 200, 200, false, false, false, true, true, false, "FacebookShare", true);
}

function shareOnTwitter(strURL, strTitle){
	strTwitterURL = "http://twitter.com/intent/tweet?url=" + strURL + "&text=" + escape(UTF8.encode(strTitle));
	openPopup(strTwitterURL, 780, 500, 100, 100, false, false, false, true, true, false, "TwitterShare", true);
}

function focusFieldOnForm(strForm, strField){
	var objForm = document.getElementById(strForm);
	if (objForm){
		var objField = objForm[strField];
		if (objField){
			objField.focus();
		}
	}
}

UTF8 = {
	encode: function(s){
		for(var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
			s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
		);
		return s.join("");
	},
	decode: function(s){
		for(var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
			((a = s[i][c](0)) & 0x80) &&
			(s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
			o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
		);
		return s.join("");
	}
};

function setCookie(strName, strValue){
	var objExpirationDate = new Date();
	document.cookie = strName + "=" + escape(strValue) + "; path=/; expires=" + (objExpirationDate.getTime() + (1000 * 60 * 60 * 24 * 30));
}

/**
 * HSV to RGB color conversion
 *
 * H runs from 0 to 360 degrees
 * S and V run from 0 to 100
 * 
 * Ported from the excellent java algorithm by Eugene Vishnevsky at:
 * http://www.cs.rit.edu/~ncs/color/t_convert.html
 */
function hsvToRgb(h, s, v) {
	var r, g, b;
	var i;
	var f, p, q, t;
 
	// Make sure our arguments stay in-range
	h = Math.max(0, Math.min(360, h));
	s = Math.max(0, Math.min(100, s));
	v = Math.max(0, Math.min(100, v));
 
	// We accept saturation and value arguments from 0 to 100 because that's
	// how Photoshop represents those values. Internally, however, the
	// saturation and value are calculated from a range of 0 to 1. We make
	// That conversion here.
	s /= 100;
	v /= 100;
 
	if(s == 0) {
		// Achromatic (grey)
		r = g = b = v;
		return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
	}
 
	h /= 60; // sector 0 to 5
	i = Math.floor(h);
	f = h - i; // factorial part of h
	p = v * (1 - s);
	q = v * (1 - s * f);
	t = v * (1 - s * (1 - f));
 
	switch(i) {
		case 0:
			r = v;
			g = t;
			b = p;
			break;
 
		case 1:
			r = q;
			g = v;
			b = p;
			break;
 
		case 2:
			r = p;
			g = v;
			b = t;
			break;
 
		case 3:
			r = p;
			g = q;
			b = v;
			break;
 
		case 4:
			r = t;
			g = p;
			b = v;
			break;
 
		default: // case 5:
			r = v;
			g = p;
			b = q;
	}
 
	return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
}

function rgb2hex(arrRGB){
	var r = arrRGB[0];
	var g = arrRGB[1];
	var b = arrRGB[2];
	var rgb = [r.toString(16),g.toString(16),b.toString(16)]
	for (var i = 0; i < 3; i++){
		if (rgb[i].length == 1){
			rgb[i] = rgb[i] + rgb[i];
		}
	}

	if (rgb[0][0] == rgb[0][1] && rgb[1][0] == rgb[1][1] && rgb[2][0] == rgb[2][1]){
		return '#' + rgb[0][0] + rgb[1][0] + rgb[2][0];
	}

	return '#' + rgb[0] + rgb[1] + rgb[2];
}