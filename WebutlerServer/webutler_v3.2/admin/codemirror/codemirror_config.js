
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


var codemirror_parserfile;
var codemirror_stylesheet;
var codemirror_highlight = true;
var codemirror_editor;

if(typeof codemirror_syntaxmode == 'undefined') {
    var codemirror_syntaxmode = '';
}

if(codemirror_syntaxmode == '' || (codemirror_syntaxmode != 'css' && codemirror_syntaxmode != 'js')) {
    codemirror_parserfile = new Array(
		"parsexml.js",
		"parsecss.js",
		"tokenizejavascript.js",
		"parsejavascript.js",
		"php/tokenizephp.js",
		"php/parsephp.js",
		"php/parsephphtmlmixed.js"
	);
    codemirror_stylesheet = new Array(
		"admin/codemirror/editor/css/xmlcolors.css",
		"admin/codemirror/editor/css/csscolors.css",
		"admin/codemirror/editor/css/jscolors.css",
		"admin/codemirror/editor/css/phpcolors.css"
	);
}

if(document.getElementById('codemirror_editormenu') && (document.getElementById('codemirror_editorsource') || document.getElementById('codemirror_patternsource')))
{
    var codemirror_editorheight;
    
    if(document.all) {
    	codemirror_editorheight = document.body.clientHeight;
    }
    else if(document.getElementById && !document.all) {
    	codemirror_editorheight = window.innerHeight;
    }
    
    if(codemirror_syntaxmode == 'css') {
        codemirror_parserfile = "parsecss.js";
        codemirror_stylesheet = "admin/codemirror/editor/css/csscolors.css";
    }
    else if(codemirror_syntaxmode == 'js') {
        codemirror_parserfile = new Array(
                "tokenizejavascript.js",
                "parsejavascript.js"
            );
        codemirror_stylesheet = "admin/codemirror/editor/css/jscolors.css";
    }
	
	if(document.getElementById('codemirror_editorsource')) {
		codemirror_editor = CodeMirror.fromTextArea('codemirror_editorsource', {
			parserfile: codemirror_parserfile,
			stylesheet: codemirror_stylesheet,
			path: "admin/codemirror/editor/js/",
			continuousScanning: 500,
			lineNumbers: true
		});
		
		codemirror_makeButton(codemirror_lang.button.save, "codemirror_save()", "save", "codemirror_editormenu");
		codemirror_makeButton(codemirror_lang.button.cancel, "window.location=codemirror_lastpage", "cancel", "codemirror_editormenu");
	}
	
	if(document.getElementById('codemirror_patternsource')) {
		codemirror_editor = CodeMirror.fromTextArea('codemirror_patternsource', {
			parserfile: codemirror_parserfile,
			stylesheet: codemirror_stylesheet,
			path: "admin/codemirror/editor/js/",
			continuousScanning: 500,
			lineNumbers: true
		});
	}
    
    codemirror_makeButton(codemirror_lang.button.highlight, "codemirror_syntax(codemirror_editor, codemirror_stylesheet)", "highlight", "codemirror_editormenu");
    codemirror_makeButton(codemirror_lang.button.undo, "codemirror_editor.undo()", "undo", "codemirror_editormenu");
    codemirror_makeButton(codemirror_lang.button.redo, "codemirror_editor.redo()", "redo", "codemirror_editormenu");
    codemirror_makeButton(codemirror_lang.button.reindent, "codemirror_editor.reindent()", "reindent", "codemirror_editormenu");
    codemirror_makeButton(codemirror_lang.button.search, "codemirror_searchpart(codemirror_editor)", "search", "codemirror_editormenu");
}

function codemirror_setHeight() {
	if(document.getElementById('codemirror_editormenu')) {
		var editorheight;
		var infoheight = 0;
		var saveheight = 0;
		
		if(document.all) {
			editorheight = document.body.clientHeight;
		}
		else if(document.getElementById && !document.all) {
			editorheight = window.innerHeight;
		}
		
		if(document.getElementById('codemirror_patternsource')) {
			infoheight = 149;
			saveheight = 44;
		}
		
		document.getElementById('codemirror_wrapper').style.height = parseInt(editorheight-infoheight-saveheight-46) + "px";
	}
}

function codemirror_syntax(editor, stylesheet) {
    if(codemirror_highlight == true) {
        editor.setStylesheet("admin/codemirror/config/nocolors.css");
        codemirror_highlight = false;
    }
    else {
        editor.setStylesheet(stylesheet);
        codemirror_highlight = true;
    }
}

function codemirror_save() {
    document.forms.codemirror_form.submit();
}

function codemirror_searchpart(editor) {
    var text = prompt(codemirror_lang.searchwins.searchfor, "");
    if (!text) return;
    
    var first = true;
    do {
        var cursor = editor.getSearchCursor(text, first);
        first = false;
        while (cursor.findNext()) {
            cursor.select();
            if (!confirm(codemirror_lang.searchwins.tryagain))
                return;
        }
    }
    while(confirm(codemirror_lang.searchwins.endofdoc));
}

function codemirror_makeButton(title, func, img, menu) {
    var menuDiv = document.getElementById(menu);
    var menuId = menuDiv.childNodes.item('buttons');
    var button = document.createElement('div');
    button.className = 'button button_out';
    button.onmousedown = new Function("this.className = 'button button_down'");
    button.onmouseup = new Function("this.className = 'button button_up'");
    button.onmouseover = new Function("this.className = 'button button_over'");
    button.onmouseout = new Function("this.className = 'button button_out'");
    menuId.appendChild(button);
    var icon = document.createElement('div');
    icon.className = 'icon';
    icon.title = title;
    icon.onclick = new Function(func);
	var ishidpi = window.devicePixelRatio >= 2 ? 'hidpi/' : '';
    icon.style.backgroundImage = 'url(admin/codemirror/config/images/' + ishidpi + img + '.png)';
    button.appendChild(icon);
}

(function()
{	
    if (window.addEventListener) {
        window.addEventListener('load', codemirror_setHeight, false); 
        window.addEventListener('resize', codemirror_setHeight, false);    
    }
    else {
        window.attachEvent('onload', codemirror_setHeight);
        window.attachEvent('onresize', codemirror_setHeight);
    }
})();

