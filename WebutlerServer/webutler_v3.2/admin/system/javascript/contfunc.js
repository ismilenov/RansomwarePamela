
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


var wbjq = jQuery.noConflict(true);

function WBeditor_loadcontfuncs() {
    WBcontvar_IframeIsLoaded = false;
    WBcontvar_IframeIntervalId = false;
    WBeditor_checkloaded();
}

function WBeditor_checkloaded() {
    if(!WBcontvar_IframeIsLoaded) {
    	if(!WBcontvar_IframeIntervalId) {
    		WBcontvar_IframeIntervalId = window.setInterval(WBeditor_checkiframeload, 100);
    	}
    	return;
    }
}

WBeditor_checkiframeload = function() {
    WBeditor_checkinstances();
	if(WBcontvar_InstancesLoaded) {
		window.clearInterval(WBcontvar_IframeIntervalId) ;
		WBcontvar_IframeIsLoaded = true;
		
		document.execCommand('enableObjectResizing', false, false);
		document.execCommand('enableInlineTableEditing', false, false);
		
		/*
		document.body.addEventListener('mscontrolselect', function(evt) {
			evt.preventDefault();
		});
		img.addEventListener('resizestart', function(e) { e.returnValue = false; }, false);
		*/
		
		WBcontvar_InstancesLoaded = false;
		WBeditor_settoolbarheight();
		WBeditor_gotometas();
		wbjq(window).scrollTop(0);
		wbjq('#webutler_loadingscreen').css('display', 'none');
	}
}

function WBeditor_checkinstances() {
    var ScriptCount = parseInt(WBcontvar_ScriptInstances.length);
    if(ScriptCount == WBcontvar_PageCount) {
        for(var i = 0; i < WBcontvar_ScriptInstances.length; i++) {
            if(!WBeditor_inarray(WBcontvar_ScriptInstances[i], WBcontvar_PageInstances)) {
                return false;
            }
        }
        WBcontvar_InstancesLoaded = true;
    }
    return;
}

function WBeditor_inarray(find, arr) {
    for(var i in arr) {
        if(find == arr[i]) {
            return true;
        }
    }
    return false;
}

function WBeditor_gotometas() {
	CKEDITOR.instances.metas.focus();
	CKEDITOR.instances.metas.readOnlyToolbar( true, false );
}

function WBeditor_settoolbarheight() {
	var paddingtop = wbjq('#WBeditor_toolbar').height();
	wbjq('body').css({
		'padding-top': paddingtop + 'px',
		'height': wbjq(window).height()-paddingtop + 'px'
	});
}


wbjq(document).ready(function()
{
	wbjq('body').click(function(e) {
		if(wbjq(e.target).closest('#WBeditor_toolbar').length === 0 && wbjq(e.target).closest('#WBeditor_linealdiv').length === 0 && wbjq(e.target).closest('.cke_textarea_inline').length === 0) {
			var dofocus = true;
			for(var i = 0; i < WBcontvar_PageInstances.length; i++) {
				if(wbjq(e.target).closest('.cke_editor_' + WBcontvar_PageInstances[i] + '_dialog').length !== 0 ||
				  wbjq(e.target).closest('.cke_dialog_background_cover').length !== 0 ||
				  wbjq(e.target).closest('.cke_upload_options').length !== 0) {
					dofocus = false;
					break;
				}
			}
			if(dofocus) {
				WBeditor_gotometas();
			}
		}
	});
	
	WBcontvar_PageCount = parseInt(WBcontvar_PageInstances.length);
	WBeditor_loadcontfuncs();
	
	wbjq(window).resize(function() {
		WBeditor_settoolbarheight();
	});
});







