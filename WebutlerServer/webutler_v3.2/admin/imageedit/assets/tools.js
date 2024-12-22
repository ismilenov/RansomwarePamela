/**
 * Functions for the ImageEditor interface, used by tools.php only	
 * @author $Author: Wei Zhuo $
 * @author $Author: Frédéric Klee <fklee@isuisse.com> $ - constraints toggle and check
 * @author $Author: Paul Moers <mail@saulmade.nl> $ - watermarking and replace code + 
 * several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: tools.js 2006-04-09 $
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

var current_action = null;
var actions = ['save', 'scale', 'flip', 'rotate', 'crop', 'text', 'watermark', 'measure'];
var orginal_width = null;
var orginal_height = null;

function toggle(action) 
{
	editor.resetImg();
	if(current_action != null && action != '' && current_action == action)
	{
        action = '';
	}
	if(current_action != action)
	{
		// hiding watermark
		if (editor.window.document.getElementById('imgCanvas'))
		{
			editor.window.document.getElementById('imgCanvas').style.display = "block";
			editor.window.dd.elements.background.hide(true);
			editor.window.dd.elements.TextBG.hide(true);
			if (editor.window.watermarkingEnabled == true)
			{
				editor.window.dd.elements.floater.hide(true);
			}
		}

		for (var i in actions)
		{
			if(actions[i] != action)
			{
				var tools = document.getElementById('tools_'+actions[i]);
				tools.style.display = 'none';
				var icon = document.getElementById('icon_'+actions[i]);
				icon.className = '';
				icon.blur();
			}
		}

		current_action = action;

		var indicator = document.getElementById('indicatorimg');
		if(action == '')
		{
			indicator.src = 'img/blank.gif';
		}
		else
		{
			indicator.src = 'img/'+action+'.gif';
			var tools = document.getElementById('tools_'+action);
			tools.style.display = 'block';
			var icon = document.getElementById('icon_'+action);
			icon.className = 'iconActive';
		}

		editor.setMode(current_action);

		if(action == 'save')
		{
            var save_format_field = document.getElementById('save_format');
            updateFormat(save_format_field, action);
        }

		if(action == 'crop')
		{
			document.getElementById('cw').value = 0;
			document.getElementById('ch').value = 0;
			document.getElementById('cx').value = 0;
			document.getElementById('cy').value = 0;
		}

		//constraints on the scale,
		//code by Frédéric Klee <fklee@isuisse.com>
		else if(action == 'scale') 
		{
			var theImage = editor.window.document.getElementById('theImage');
			orginal_width = theImage.width;
			orginal_height = theImage.height;

			var w = document.getElementById('sw');
			w.value = orginal_width ;
			var h = document.getElementById('sh') ;
			h.value = orginal_height ;
		}
		
		else if(action == 'text') 
		{
			editor.window.document.getElementById('imgCanvas').style.display = "none";
			editor.window.dd.elements.background.hide(true);
			editor.window.dd.elements.TextBG.show(true);
			editor.window.dd.elements.insertText.show(true);
			editor.window.dd.elements.insertText.moveTo(0, 0);
			editor.window.verify_insertText();
			updateFont();
		}
		
		else if(action == 'watermark')
		{
			if (editor.window.document.getElementById('imgCanvas'))
			{
				editor.window.document.getElementById('imgCanvas').style.display = "none";
				editor.window.dd.elements.background.show(true);
				editor.window.dd.elements.TextBG.hide(true);
				if (editor.window.watermarkingEnabled == true)
				{
					editor.window.dd.elements.floater.show(true);
					editor.window.dd.elements.floater.moveTo(0, 0);
					editor.window.verifyBounds();
				}
			}
		}
	}
}

function fontAngle() {
	if(!isNaN(document.getElementById("angle").value) && (document.getElementById("angle").value >= -180 || document.getElementById("angle").value <= 360))
    {
        if(document.getElementById("angle").value >= 181)
            document.getElementById("angle").value = document.getElementById("angle").value-360;
        
        var el = editor.window.document.getElementById("insertText");
        var angle = document.getElementById('angle').value*-1;

        var radian = angle * (Math.PI * 2 / 360);
        var mathcos = Math.cos(radian);
        var mathsin = Math.sin(radian);
        
        if (el.filters)
        {
            a = mathcos;
            b = -mathsin;
            c = mathsin;
            d = mathcos;
            
        	el.style.filter = 'progid:DXImageTransform.Microsoft.Matrix(M11='+ a +', M12='+ b +', M21='+ c +', M22='+ d +', sizingMethod=\'auto expand\')';
        }
        else
        {
        	//el.style.oTransform = el.style.webkitTransform = el.style.MozTransform = 
            el.style.transform = 'rotate('+ angle +'deg)';
        }
    }
}

function updateFont() {
	editor.window.dd.elements.insertText.write(document.getElementById('text').value);
	
	var insertTextElem = editor.window.document.getElementById("insertText");
	insertTextElem.style.fontFamily = document.getElementById("font_family").value;
	insertTextElem.style.color = '#' + document.getElementById("font_color").value;
	if(!isNaN(document.getElementById("font_size").value))
        insertTextElem.style.fontSize = parseInt(document.getElementById("font_size").value) + 'px';
	
	fontAngle();
	updatePosition();
}

function updatePosition() {
	var insertTextElem = editor.window.document.getElementById("insertText");
	
    insertTextElem.style.width = 'auto';
	insertTextElem.style.height = 'auto';
	
    document.getElementById("text_top").value = editor.window.dd.elements.insertText.x;
	document.getElementById("text_left").value = editor.window.dd.elements.insertText.y;
}

function toggleMarker() 
{
	var marker = document.getElementById("markerImg");
	
	if(marker != null && marker.src != null) {
		if(marker.src.indexOf("t_black.gif") > 0) {
			marker.src = "img/t_white.gif";
		}
		else {
			marker.src = "img/t_black.gif";
		}
		editor.toggleMarker();
	}
}

//Toggle constraints
function toggleConstraints() 
{
	var lock = document.getElementById("scaleConstImg");
	var checkbox = document.getElementById("constProp");
	
	if(lock != null && lock.src != null) {
		if(lock.src.indexOf("unlocked.gif") >= 0)
		{
			lock.src = "img/locked.gif";
			checkbox.checked = true;
			checkConstrains('width');
		}
		else
		{
			lock.src = "img/unlocked.gif";
			checkbox.checked = false;
		}
	}
}

//check the constraints
function checkConstrains(changed) 
{
	var constrained = document.getElementById('constProp');
	if(constrained.checked) 
	{
		var w = document.getElementById('sw') ;
		var width = w.value ;
		var h = document.getElementById('sh') ;
		var height = h.value ;
		
		if(orginal_width > 0 && orginal_height > 0) 
		{
			if(changed == 'width' && width > 0) 
				h.value = parseInt((width/orginal_width)*orginal_height);
			else if(changed == 'height' && height > 0) 
				w.value = parseInt((height/orginal_height)*orginal_width);
		}
	}
	updateMarker('scale') ;
}


function updateMarker(mode) 
{
	if (mode == 'crop')
	{
		var t_cx = document.getElementById('cx');
		var t_cy = document.getElementById('cy');
		var t_cw = document.getElementById('cw');
		var t_ch = document.getElementById('ch');

		editor.setMarker(parseInt(t_cx.value), parseInt(t_cy.value), parseInt(t_cw.value), parseInt(t_ch.value));
	}
	else if(mode == 'scale')
	{
		var s_sw = document.getElementById('sw');
		var s_sh = document.getElementById('sh');
		
		editor.setMarker(0, 0, parseInt(s_sw.value), parseInt(s_sh.value));
	}
	editor.dragStopped();
}


function rotatePreset(selection) 
{
	var value = selection.options[selection.selectedIndex].value;
	
	if(value.length > 0 && parseInt(value) != 0) {
		var ra = document.getElementById('ra');
		ra.value = parseInt(value);
	}
}

function updateFormat(selection, action) 
{
	if(selection.options) {
		var selected = selection.options[selection.selectedIndex].value;

		var values = selected.split(",");
		if(values.length >1) {
			updateSlider(parseInt(values[1]), action);
			if(parseInt(values[1]) == 0)
			{
				document.getElementById('thesaveslider').style.display = 'none';
			}
			else
			{
				document.getElementById('thesaveslider').style.display = 'block';
			}
		}
	}
	else {
		updateSlider(100, action);
		document.getElementById('thesaveslider').style.display = 'block';
	}
}

// show processing message
function showMessage(newMessage) 
{
	var message = document.getElementById('message');
	if(message.firstChild) {
		message.removeChild(message.firstChild);
	}
	message.appendChild(document.createTextNode(unescape(newMessage)));
	
	document.getElementById('webutler_loadingscreen').style.display = "block";
    if(navigator.appName.indexOf("Internet Explorer") != -1) {
        window.setTimeout(function() {
			document.getElementById('loadingimg').outerHTML = document.getElementById('loadingimg').outerHTML;
		}, 500);
    }
}

// hide message
function hideMessage() 
{
	document.getElementById('webutler_loadingscreen').style.display = "none";
}

// change watermark
function changeWatermark(source)
{
	if (editor.window.watermarkingEnabled)
	{
		floater = editor.window.dd.elements.floater;
		floater.swapImage(eval("editor.window." + source.options[source.selectedIndex].value + "Preload.src"));
		floater.resizeTo(source.options[source.selectedIndex].getAttribute("x"), source.options[source.selectedIndex].getAttribute("y"));
		editor.window.verifyBounds();
	}
}

// change watermark opacity
function changeWatermarkOpacity(opacity)
{
	if (editor.window.watermarkingEnabled)
	{
		floater = editor.window.dd.elements.floater;

		// IE/Win
		floater.css.filter = "alpha(opacity:" + opacity + ")";
		// Safari < 1.2, Konqueror
		floater.css.KHTMLopacity = opacity / 100;
		// Older Mozilla and Firefox
		floater.css.Mozopacity = opacity / 100;
		// Safari 1.2, newer Firefox and Mozilla, CSS3
		floater.css.opacity = opacity / 100;
	}
}

// change watermark position
function moveWatermark(x, y)
{
	if (editor.window.watermarkingEnabled)
	{
		floater = editor.window.dd.elements.floater;
		background = editor.window.dd.elements.background;

		x = background.x + (background.w - floater.w) * x;
		y = background.y + (background.h - floater.h) * y;
		
		floater.moveTo(x, y);
	}
}

function ckeckFilename( input, message )
{
	zugelassen = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
	
	for (var Pos = 0; Pos < input.value.length; Pos++)
	{
		if (zugelassen.indexOf(input.value.charAt(Pos)) == -1)
		{
			alert( message.replace(/_STRING_/g, input.value.charAt(Pos)) );
			input.focus();
			return false;
		}
	}
	editor.doSubmit('save');
}
