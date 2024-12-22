
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


function setuploadpath(elem, field, dataid)
{
	if(typeof field != 'undefined' && typeof XMLHttpRequest != 'undefined') {
		if(uploadfiletypes[field] != '') {
			var types = uploadfiletypes[field].split('|');
			var allowed = false;
			
			for(var i = 0; i < types.length; i++) {
				if(types[i] == elem.files[0]['type']) {
					allowed = true;
					break;
				}
			}
			
			if(!allowed) {
				alert(uploadwrongmime);
				elem.value = '';
				return false;
			}
		}
		
		if(elem.files[0]['size'] > uploadmaxsize) {
			if(!document.getElementById('uploadpopbox')) {
				var newBG = document.createElement('div');
				var addBG = document.body.appendChild(newBG);
				addBG.id = 'uploadpopbox';
				addBG.style.display = 'block';
			}
			else {
				document.getElementById('uploadpopbox').style.display = 'block';
			}
			
			var filename = elem.value;
			elem.nextSibling.value = filename;
			
			document.getElementById('uploadelarge_button').onclick = new Function('uploadelarge(\'' + field + '\', ' + dataid + ')');
			document.getElementById('uploadelarge_cancel').onclick = new Function('closeuploadpopup()');
			document.getElementById('uploadlarge_progressbar').style.display = 'none';
			document.getElementById('uploadlargepopup').style.display = 'block';
		}
		else {
			var filename = elem.value;
			elem.nextSibling.value = filename;
		}
	}
	else {
		var filename = elem.value;
		elem.nextSibling.value = filename;
	}
}

function closeuploadpopup()
{
    document.getElementById('uploadpopbox').style.display = 'none';
    document.getElementById('uploadlargepopup').style.display = 'none';
	document.getElementById('uploadlarge_progressbar').style.width = '0px';
	document.getElementById('uploadelarge_button').disabled = false;
}

function uploadelarge(field, dataid) {
	document.getElementById('uploadelarge_button').disabled = true;
	
    var prozent = document.getElementById('uploadlarge_prozentbar');
    var progress = document.getElementById('uploadlarge_progressbar');
	progress.style.display = '';
	
    var upload = document.getElementsByName(field)[0];
	var file = upload.files[0];
	var filename = file.name;
	var filetype = file['type'];
	
	var url = 'admin.php?upload=file&field=' + field + '&filename=' + encodeURIComponent(filename) + '&filetype=' + encodeURIComponent(filetype);
	if(typeof dataid != 'undefined' && dataid != '')
		url += '&dataid=' + dataid;
	
	var request = new XMLHttpRequest();
	request.open('POST', url, true);
	request.setRequestHeader("X_FILENAME", filename);
	
	request.upload.addEventListener('progress', function(evt) {
		var uploadstate = Number((100 / evt.total) * evt.loaded).toFixed(2);
        prozent.innerHTML = uploadstate + ' %';
		progress.style.width = (uploadstate * 3) + 'px';
	}, false);
	
	request.addEventListener('load', function(evt) {
		var infos = request.responseText;
		
		file.name = '';
		
		if(infos != 'updated') {
			upload.style.display = 'none';
				
			var newType = document.createElement('input');
			var addType = upload.parentNode.insertBefore(newType, upload.nextSibling);
			addType.type = 'hidden';
			addType.name = field + '[type]';
			addType.value = filetype;
			
			var newInput = document.createElement('input');
			var addInput = upload.parentNode.insertBefore(newInput, upload.nextSibling);
			addInput.type = 'hidden';
			addInput.name = field + '[infos]';
			addInput.value = infos;
			
			upload.parentNode.removeChild(upload);
			
			alert(uploadcomplete);
		}
		
		closeuploadpopup();
	}, false);
	
	var canceling = function() {
		document.getElementById('uploadelarge_cancel').removeEventListener('click', canceling, false);
        request.abort();
    }
	
	document.getElementById('uploadelarge_cancel').addEventListener('click', canceling, false);
	
	request.send(file);
}

function prevpopbox(imgpath, imgwidth, imgheight)
{
	var maxwidth = 900;
	
    if(!document.getElementById('prevpopbox')) {
        var newBG = document.createElement('div');
        var addBG = document.body.appendChild(newBG);
        addBG.id = 'prevpopbox';
        addBG.style.display = 'block';
        addBG.onclick = new Function('closepopup()');
    }
    else {
        document.getElementById('prevpopbox').style.display = 'block';
    }
    
    if(!document.getElementById('prevpopimg')) {
        var newIMG = document.createElement('img');
        var addIMG = document.body.appendChild(newIMG);
        addIMG.id = 'prevpopimg';
        addIMG.src = imgpath;
        addIMG.style.display = 'block';
        addIMG.onclick = new Function('closepopup()');
    }
    else {
        document.getElementById('prevpopimg').src = imgpath;
        document.getElementById('prevpopimg').style.display = 'block';
    }
    
    
    var winheight = window.innerHeight-40;
	
	var _width = '';
	if(imgwidth > maxwidth) {
		_width = maxwidth;
		imgheight = imgheight*(maxwidth/imgwidth);
		imgwidth = maxwidth;
	}
	if(imgheight > winheight) {
		imgwidth = imgwidth*(winheight/imgheight);
		imgheight = winheight;
		_width = imgwidth;
	}
	
    var _top = parseInt(imgheight/2);
    var _left = parseInt(imgwidth/2);
    document.getElementById('prevpopimg').style.margin = '-' + _top + 'px 0px 0px -' + _left + 'px';
	document.getElementById('prevpopimg').style.width = _width != '' ? _width + 'px' : '';
}

function closepopup()
{
    document.getElementById('prevpopbox').style.display = 'none';
    document.getElementById('prevpopimg').style.display = 'none';
    document.getElementById('prevpopimg').src = '';
}

function checkonlyfull()
{
    if(document.getElementById('alldatas').checked == true) {
		document.getElementById('onlyfull').checked = false;
		document.getElementById('onlyfull').disabled = true;
	}
	else {
		document.getElementById('onlyfull').disabled = false;
	}
    if(document.getElementById('onlyfull').checked == true) {
		document.getElementById('alldatas').disabled = true;
	}
	else {
		document.getElementById('alldatas').disabled = false;
	}
}

function opencatitemlist()
{
    if(!document.getElementById('catpopbox')) {
        var newBG = document.createElement('div');
        var addBG = document.body.appendChild(newBG);
        addBG.id = 'catpopbox';
        addBG.style.display = 'block';
        addBG.onclick = new Function('closecatitemlist()');
    }
    else {
        document.getElementById('catpopbox').style.display = 'block';
    }
    
    document.getElementById('catitemlist').style.display = 'block';
    var divheight = document.getElementById('catitemlist').offsetHeight;
    var divwidth = document.getElementById('catitemlist').offsetWidth;
    
    var _top = parseInt(divheight/2);
    var _left = parseInt(divwidth/2);
    document.getElementById('catitemlist').style.margin = '-' + _top + 'px 0px 0px -' + _left + 'px';
    /*
    var winheight = window.innerHeight;
    var winwidth = window.innerWidth;
    var margtop = parseInt((winheight-divheight)/2);
    var margleft = parseInt((winwidth-divwidth)/2);
    document.getElementById('catitemlist').style.margin = margtop + 'px 0px 0px ' + margleft + 'px';
    */
}

function closecatitemlist()
{
    document.getElementById('catpopbox').style.display = 'none';
    document.getElementById('catitemlist').style.display = 'none';
}

function moveitemlistheight(scrollpos)
{
    var mDiv = document.getElementById('moveitemlist');
    var winheight = window.innerHeight;
    var oTop = mDiv.offsetTop;
    var mHeight = parseInt(winheight-oTop-100);
    mDiv.style.height = mHeight + 'px';
    mDiv.scrollTop = scrollpos;
}

function setscrollpos()
{
    document.getElementById('scrollpos').value = document.getElementById('moveitemlist').scrollTop;
    return true;
}

function addClass(elem) {
    if(!elem.className.match(new RegExp('itemmover')))
        elem.className = 'itemmover';
}

function removeClass() {
    var elems = document.getElementById('moveitemlist').getElementsByTagName('label');
    for(var i = 0; i < elems.length; i++) {
        if(elems[i].className.match(new RegExp('itemmover'))) {
            elems[i].className = 'itemunsel';
            var elemid = elems[i].getAttribute('for');
            document.getElementById(elemid).checked = false;
        }
    }
}

function setdefitem(elem)
{
    removeClass();
    addClass(elem);
}

function cleardatefields()
{
    document.getElementById('DPC_fromtime').value = '';
    document.getElementById('DPC_totime').value = '';
}

function cleardatefield(fieldid)
{
    document.getElementById('DPC_' + fieldid).value = '';
}

function openoptionimgs()
{
    if(!document.getElementById('optionpopbox')) {
        var newBG = document.createElement('div');
        var addBG = document.body.appendChild(newBG);
        addBG.id = 'optionpopbox';
        addBG.style.display = 'block';
        addBG.onclick = new Function('closeoptionimgs()');
    }
    else {
        document.getElementById('optionpopbox').style.display = 'block';
    }
    
    document.getElementById('uploadoptionimgs').style.display = 'inline-block';
    var divheight = document.getElementById('uploadoptionimgs').offsetHeight;
    var divwidth = document.getElementById('uploadoptionimgs').offsetWidth;
    
    var _top = parseInt(divheight/2);
    var _left = parseInt(divwidth/2);
    document.getElementById('uploadoptionimgs').style.margin = '-' + _top + 'px 0px 0px -' + _left + 'px';
    /*
    var winheight = window.innerHeight;
    var winwidth = window.innerWidth;
    var margtop = parseInt((winheight-divheight)/2);
    var margleft = parseInt((winwidth-divwidth)/2);
    document.getElementById('uploadoptionimgs').style.margin = margtop + 'px 0px 0px ' + margleft + 'px';
    */
    
    return false;
}

function closeoptionimgs()
{
    document.getElementById('optionpopbox').style.display = 'none';
    document.getElementById('uploadoptionimgs').style.display = 'none';
}

function listoptionimgs(open, close)
{
    if(document.getElementById('showoptionimgs').style.display == 'none') {
	    document.getElementById('showoptionimgs').style.display = 'inline-block';
	    document.getElementById('listoptimglink').innerHTML = close;
	}
	else {
	    document.getElementById('showoptionimgs').style.display = 'none';
	    document.getElementById('listoptimglink').innerHTML = open;
	}
	
    var divheight = document.getElementById('showoptionimgs').offsetHeight;
    
    var _top = parseInt(divheight/2);
    document.getElementById('showoptionimgs').style.margin = '-' + _top + 'px 0px 0px 305px';
    /*
    var divwidth = document.getElementById('showoptionimgs').offsetWidth;
    var winheight = window.innerHeight;
    var winwidth = window.innerWidth;
    var margtop = parseInt((winheight-divheight)/2);
    var margleft = parseInt((winwidth-divwidth)/2+305);
    document.getElementById('showoptionimgs').style.margin = margtop + 'px 0px 0px ' + margleft + 'px';
    */
    
    return false;
}

function showoptimgdiv(num)
{
	var div = document.getElementById('showoptimg_' + num);
    if(div.style.display == 'block')
	    div.style.display = 'none';
	else
	    div.style.display = 'block';
}

function showtext(field, langs, lang)
{
	var all = langs.split('|');
	
	for(var i = 0; i < all.length; i++) {
		disliteflag(document.getElementById(field + all[i]), 'currentlangflag');
	    document.getElementById('bylang_' + field + '_' + all[i]).style.display = 'none';
	}
	
	highliteflag(document.getElementById(field + lang), 'currentlangflag');
	document.getElementById('bylang_' + field + '_' + lang).style.display = '';
}

function checkclassname(elem, curclass) {
    if (elem.className == '') {
        return false;
    }
	else {
        return new RegExp('\\b' + curclass + '\\b').test(elem.className);
    }
}

function highliteflag(elem, curclass) {
    if(!checkclassname(elem, curclass)) {
        elem.className += (elem.className ? ' ' : '') + curclass;
        return true;
    }
	else {
        return false;
    }
}

function disliteflag(elem, curclass) {
    if(checkclassname(elem, curclass)) {
        elem.className = elem.className.replace((elem.className.indexOf(' ' + curclass) >= 0 ? ' ' + curclass : curclass), '');
        return true;
    }
	else {
        return false;
    }
}












