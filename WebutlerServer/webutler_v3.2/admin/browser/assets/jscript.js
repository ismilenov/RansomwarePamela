
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

(function() {
	var d = document.domain ;

	while ( true ) {
		try {
			var test = window.opener.document.domain ;
			break ;
		}
		catch( e ) {}

		d = d.replace( /.*?(?:\.|$)/, '' ) ;

		if ( d.length == 0 )
			break ;
		try {
			document.domain = d ;
		}
		catch (e) {
			break ;
		}
	}
})();

function setuploadpath(elem) {
    var filename = elem.value;
    elem.nextSibling.value = filename;
}

function setheights() {
    var fullheight = parseInt(document.body.offsetHeight);
    var frameheight = parseInt(fullheight-135) + 'px';
    document.getElementById('folderswin').style.height = frameheight;
    document.getElementById('fileswin').style.height = frameheight;
    document.getElementById('renamebg').style.height = fullheight + 'px';
}

function getUrlParam(paramName)
{
    var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
    var match = window.top.location.search.match(reParam) ;
    
    return (match && match.length > 1) ? match[1] : '' ;
}

function renamepopupopen(oldname)
{
	scalboxclose();
	
	document.getElementById('renamebg').style.display = 'block' ;
	document.getElementById('renamepopup').style.display = 'block' ;
	document.getElementById('renamepopup_oldname').value = oldname;
    var newname = oldname.substring(0, oldname.lastIndexOf('.'));
    var ext = ''
    if(newname == '') {
        newname = oldname;
    }
    else {
        ext = oldname.substring(oldname.lastIndexOf('.'), oldname.length);
    }
	document.getElementById('renamepopup_newname').value = newname;
    document.getElementById('renamepopup_oldext').innerHTML = ext;
}

function renamepopupclose()
{
	document.getElementById('renamebg').style.display = 'none' ;
	document.getElementById('renamepopup').style.display = 'none' ;
}

function streamfileupload() {
    if(document.getElementById('uploadfile').nextSibling.value == '')
		return false;
	
	scalboxclose();
	
	if(document.getElementById('ResourceSelector')) {
		document.getElementById('ResourceSelector').style.display = 'none';
	}
	document.getElementById('webutler_loadingscreen').style.display = 'block';
	
	document.getElementById('uploadscreen').style.display = 'block';
	
    var prozent = document.getElementById('prozentbar');
    var progress = document.getElementById('progressbar');
	progress.style.display = '';
	
    var upload = document.getElementsByName('uploadfile')[0];
	var file = upload.files[0];
	var filename = file.name;
	
	
	var url = document.getElementById('actionpath').value;
	url += '&filename=' + encodeURIComponent(filename);
	if(document.getElementById('imgsmallwidth')) {
		var imgsmallwidth = parseInt(document.getElementById('imgsmallwidth').value);
		var imgsmallheight = parseInt(document.getElementById('imgsmallheight').value);
		var imgboxwidth = parseInt(document.getElementById('imgboxwidth').value);
		var imgboxheight = parseInt(document.getElementById('imgboxheight').value);
		if(imgsmallwidth != '' && imgsmallwidth > 0) url += '&imgsmallwidth=' + imgsmallwidth;
		if(imgsmallheight != '' && imgsmallheight > 0) url += '&imgsmallheight=' + imgsmallheight;
		if(imgboxwidth != '' && imgboxwidth > 0) url += '&imgboxwidth=' + imgboxwidth;
		if(imgboxheight != '' && imgboxheight > 0) url += '&imgboxheight=' + imgboxheight;
		if(document.getElementById('lightbox').checked == true) url += '&lightbox=true';
	}
		
	if(document.getElementById('overwrite').checked == true) url += '&overwrite=true';
	
	var request = new XMLHttpRequest();
	request.open('POST', url, true);
	request.setRequestHeader("X_FILENAME", filename);
	
	request.upload.addEventListener('progress', function(evt) {
		var uploadstate = Number((100 / evt.total) * evt.loaded).toFixed(2);
        prozent.innerHTML = uploadstate + ' %';
		progress.style.width = (uploadstate * 2) + 'px';
	}, false);
	
	request.addEventListener('load', function(evt) {
		file.name = '';
		alert(request.responseText);
		window.top.uploadComplete();
	}, false);
	
	var canceling = function() {
		document.getElementById('uploadcancel').removeEventListener('click', canceling, false);
        request.abort();
		window.top.uploadComplete();
    }
	
	document.getElementById('uploadcancel').addEventListener('click', canceling, false);
	
	request.send(file);
}

function ScalboxView()
{
	if(document.getElementById('scalbox') && document.getElementById('scalbox').style.display == 'block') {
		document.getElementById('scalbox').style.display = 'none';
	}
	else {
		document.getElementById('scalbox').style.display = 'block';
	}
}

function scalboxclose()
{
	if(document.getElementById('scalbox') && document.getElementById('scalbox').style.display == 'block') {
		document.getElementById('scalbox').style.display = 'none';
	}
}

function OpenFile( fileUrl )
{
	if( window.top.name == 'CKBrowseNoneDialog' || window.top.name == 'CKBrowseTplIcons' ) {
		return false ;
	}
    var funcNum = getUrlParam('CKEditorFuncNum');
    window.top.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, '');
	window.top.close();
}

function setTplIcon( file )
{
	if( window.top.name != 'CKBrowseTplIcons' ) {
		return false ;
	}
    window.top.opener.document.getElementById('webutler_tplicon').value = file;
	window.top.close();
}

function OpenWindow( URL, preview, player )
{
	var width = 600;
	var height = 450;
	var top = parseInt( ( window.screen.height - height ) / 2, 10 );
	var left = parseInt( ( window.screen.width  - width ) / 2, 10 );
	
	var options = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=yes';
	
	var popupWindow = window.open( '', preview, options, true );
	
	if(!popupWindow)
		return false;
	
	try {
		popupWindow.resizeTo( width, height );
		popupWindow.moveTo( left, top );
		
    	if(player == 'true') {
        	var zDoc = popupWindow.document;
        	zDoc.open();
        	zDoc.write('<html><head></head><body><embed height="100%" width="100%" pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="' + URL + '" type="application/x-shockwave-flash"></embed></body></html>');
        	zDoc.close();
            popupWindow.focus();
    	}
		else {
    		popupWindow.focus();
    		popupWindow.location.href = URL;
		}
	}
	catch ( e ) {
		popupWindow = window.open( URL, preview, options, true );
	}
}

function OpenEditWin( WB_Host, ImageURL )
{
	var breite = imageeditorWindowWidth;
	var hoehe = imageeditorWindowHeight;
	
	var url = WB_Host + '/admin/imageedit/tools.php?imgfile=' + encodeURIComponent(ImageURL);
	
	if(typeof breite == 'string' && breite.length > 1 && breite.substr(breite.length - 1, 1) == '%')
		breite = parseInt(window.screen.width * parseInt(breite.substr(0, breite.length - 1)) / 100);

	if(typeof hoehe == 'string' && hoehe.length > 1 && hoehe.substr(hoehe.length - 1, 1) == '%')
		hoehe = parseInt(window.screen.height * parseInt(hoehe.substr(0, hoehe.length - 1)) / 100);

	if(breite < 640) breite = 640;
	if(hoehe < 420) hoehe = 420;
	
	var oben = parseInt((window.screen.height - hoehe) / 2, 10);
	var links = parseInt((window.screen.width - breite) / 2, 10);
	
	var options = 'width=' + breite + ',height=' + hoehe + ',left=' + links + ',top=' + oben + ',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=no';
	
	var popupWindow = window.open( '', 'ImgEdit', options, true );
	
	if(!popupWindow)
		return false;
	
	try {
		popupWindow.resizeTo( breite, hoehe );
		popupWindow.moveTo( links, oben );
		popupWindow.focus();
		popupWindow.location.href = url;
	}
	catch ( e ) {
		popupWindow = window.open( url, 'ImgEdit', options, true );
	}
}


