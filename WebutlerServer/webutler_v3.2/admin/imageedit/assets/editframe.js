/**
 * Javascript used by the editframe.php, it basically initializes the frame.
 * @author $Author: Wei Zhuo $
 * @author $Author: Paul Moers <mail@saulmade.nl> $ - watermarking and replace code + 
 * several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: editframe.js 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 */

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

var topDoc = window.top.document;

var m_sx = topDoc.getElementById('sx');
var m_sy = topDoc.getElementById('sy');
var m_w = topDoc.getElementById('mw');
var m_h = topDoc.getElementById('mh');
var m_a = topDoc.getElementById('ma');
var m_d = topDoc.getElementById('md');

var t_cx = topDoc.getElementById('cx');
var t_cy = topDoc.getElementById('cy');
var t_cw = topDoc.getElementById('cw');
var t_ch = topDoc.getElementById('ch');

var s_sw = topDoc.getElementById('sw');
var s_sh = topDoc.getElementById('sh');

var flip = topDoc.getElementById('flip');

var r_ra = topDoc.getElementById('ra');
var r_angle = topDoc.getElementById('angle');

var pattern = "img/blank.gif";

function doSubmit(action)
{
	// hiding action buttons
	var buttons = parent.document.getElementById('buttons');
	buttons.style.display = 'none';
	// hiding indicator
    var indicator = parent.document.getElementById('indicatorimg');
	indicator.src = 'img/blank.gif';
	// hiding current action's controls
	var tools = parent.document.getElementById('tools_' + action);
	tools.style.display = 'none';

    if (action == 'crop')
    {
        var img_width = parseInt(document.getElementById('theImage').style.width);
        var img_height = parseInt(document.getElementById('theImage').style.height);
		if(t_cx.value <= 0 || t_cx.value >= img_width) {
			t_cx.value = 0;
        }
		if(t_cy.value <= 0 || t_cy.value >= img_height) {
			t_cy.value = 0;
        }
		if(t_cw.value == "" || t_cw.value <= 0) {
			t_cw.value = img_width;
        }
		if(t_ch.value == "" || t_ch.value <= 0) {
			t_ch.value = img_height;
        }

		var url = "editframe.php?img="+currentImageFile+"&action=crop&params="+parseInt(t_cx.value)+','+parseInt(t_cy.value)+','+ parseInt(t_cw.value)+','+parseInt(t_ch.value)+CKEparams;

		parent.showMessage(messages[0]);
	    location.href = url;
    } 
    else if (action == 'text')
    {
		var font_text = topDoc.getElementById('text').value;
		var font_angle = topDoc.getElementById('angle').value;
		var font_size = parseInt(topDoc.getElementById('font_size').value);
		var posX = dd.elements.insertText.x - dd.elements.TextBG.x;
		var posY = dd.elements.insertText.y - dd.elements.TextBG.y + font_size;
		var font_color = '#' + topDoc.getElementById('font_color').value;
		var font_family = topDoc.getElementById('font_family').options[topDoc.getElementById('font_family').selectedIndex].value;

		var url = "editframe.php?img="+currentImageFile+"&action=text&params="+escape(font_text)+','+font_angle+','+parseInt(posX)+','+ parseInt(posY)+','+parseFloat(font_size)+','+escape(font_color)+','+escape(font_family)+CKEparams;

		parent.showMessage(messages[1]);
        location.href = url;
    }  
    else if (action == 'scale')
    {
        var url = "editframe.php?img="+currentImageFile+"&action=scale&params="+parseInt(s_sw.value)+','+parseInt(s_sh.value)+CKEparams;

		parent.showMessage(messages[2]);
        location.href = url;
    }
    else if (action == 'flip')
    {
        if (flip.value == 'hor' || flip.value == 'ver')
        {
            var url = "editframe.php?img="+currentImageFile+"&action=flip&params="+flip.value+CKEparams;
            
    		parent.showMessage(messages[3]);
            location.href = url;
        }
        else {
        	buttons.style.display = 'inline';
	        parent.toggle('flip');
	        return false;
        }
    }
    else if (action == 'rotate')
    {
        if (isNaN(parseFloat(r_ra.value))==false)
		{
			var url = "editframe.php?img="+currentImageFile+"&action=rotate&params="+parseFloat(r_ra.value)+CKEparams;
	
			parent.showMessage(messages[4]);
			location.href = url;
		}
    }
    else if(action == 'save')
	{
        var org_file = topDoc.getElementById('org_file');
        var s_path = topDoc.getElementById('save_filepath');
        var s_file = topDoc.getElementById('save_filename');
        var s_format = topDoc.getElementById('save_format');
        var s_quality = topDoc.getElementById('sliderfieldsave');
		
		if(s_format.value.length <= 0) 
		{
            var format = "jpg";
        }
		else
		{
			var f_format = s_format.value.split(",");
			var format = f_format[0];
		}
		
        if(s_file.value.length <= 0) 
		{
            alert(messages[7]);
        }
        else
        {
            var filepath = s_path.options ? s_path.options[s_path.selectedIndex].value : s_path.value;
            var filename = s_file.value;
			var file = encodeURIComponent('/' + filepath + '/' + filename);
            var quality = parseInt(s_quality.value);
			var o_file = '';
			if(s_path.options && org_file.value != '/' + filepath + '/' + filename + '.' + format)
				o_file = ',' + encodeURIComponent(topDoc.getElementById('org_boxfile').value);
            var url = 'editframe.php?img=' + currentImageFile + '&action=save&params=' + format + ',' + quality + o_file + '&file=' + file + CKEparams;
            
            parent.showMessage(messages[5]);
            location.href = url;
        }
    }
    else if (action == 'watermark')
    {
		var watermarkX = dd.elements.floater.x - dd.elements.background.x;
		var watermarkY = dd.elements.floater.y - dd.elements.background.y;
		var opacity = topDoc.getElementById('sliderfieldwatermark').value;
		var watermarkFullPath = topDoc.getElementById('watermark_file').options[topDoc.getElementById('watermark_file').selectedIndex].getAttribute("fullPath");
		
        var url = "editframe.php?img="+currentImageFile+"&action=watermark&params="+parseInt(watermarkX)+','+parseInt(watermarkY)+','+parseInt(opacity)+','+watermarkFullPath+CKEparams;

		parent.showMessage(messages[6]);
        location.href = url;
    }
	parent.toggle('');
}

function addEvent(obj, evType, fn)
{ 
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, true);
		return true;
	} 
	else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} 
	else {
		return false;
	} 
}

var jg_doc;

init = function()
{
	jg_doc = new jsGraphics('imgCanvas'); // draw directly into document
	jg_doc.setColor('#000000'); // black
	initEditor();
}

addEvent(window, 'load', init);
