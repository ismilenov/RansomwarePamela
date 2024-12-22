/**
 * Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('sourcedialog',function(editor){var size=CKEDITOR.document.getWindow().getViewPaneSize();var width=Math.min(size.width-70,800);var height=size.height/1.5;var oldData;return{title:editor.lang.sourcedialog.title,minWidth:width,minHeight:height,onShow:function(){oldData=editor.getData();this.layout();},onOk:(function(){function setData(dialog,newData){editor.focus();editor.setData(newData,function(){dialog.hide();var range=editor.createRange();range.moveToElementEditStart(editor.editable());range.select();});}
return function(event){var newData=eval("codemirror_cke_"+editor.name+".getCode()");var dialog=this;if(newData===oldData)
return true;setTimeout(function(){setData(dialog,newData);});return false;};})(),contents:[{id:'main',label:editor.lang.sourcedialog.title,elements:[{type:'html',html:'<div class="codemirror_ckemenu" id="codemirror_ckemenu_'+editor.name+'"><div id="buttons"></div></div>',onLoad:function()
{eval('codemirror_makeButton(codemirror_lang.button.highlight, "codemirror_syntax(codemirror_cke_'+editor.name+', codemirror_stylesheet)", "highlight", "codemirror_ckemenu_'+editor.name+'");');eval('codemirror_makeButton(codemirror_lang.button.undo, "codemirror_cke_'+editor.name+'.undo()", "undo", "codemirror_ckemenu_'+editor.name+'");');eval('codemirror_makeButton(codemirror_lang.button.redo, "codemirror_cke_'+editor.name+'.redo()", "redo", "codemirror_ckemenu_'+editor.name+'");');eval('codemirror_makeButton(codemirror_lang.button.reindent, "codemirror_cke_'+editor.name+'.reindent()", "reindent", "codemirror_ckemenu_'+editor.name+'");');eval('codemirror_makeButton(codemirror_lang.button.search, "codemirror_searchpart(codemirror_cke_'+editor.name+')", "search", "codemirror_ckemenu_'+editor.name+'");');}},{type:'html',id:'htmlsourcearea',html:'<div class="codemirror_ckebg" id="codemirror_ckebg_'+editor.name+'" style="margin-bottom: 10px;"><textarea id="codemirror_ckesource_'+editor.name+'" style="display: none;"></textarea></div>',onShow:function()
{document.getElementById('codemirror_ckesource_'+editor.name).value=oldData;eval('codemirror_cke_'+editor.name+' = CodeMirror.fromTextArea( "codemirror_ckesource_'+editor.name+'", {\n'+'height : "'+(height-50)+'px",\n'+'parserfile : codemirror_parserfile,\n'+'stylesheet : codemirror_stylesheet,\n'+'path : codemirror_rootpath + "codemirror/editor/js/",\n'+'continuousScanning : 500,\n'+'lineNumbers : true\n'+'});\n');},onHide:function()
{document.getElementById('codemirror_ckesource_'+editor.name).value='';document.getElementById('codemirror_ckebg_'+editor.name).focus();var codemirrordiv=document.getElementById('codemirror_ckebg_'+editor.name).getElementsByTagName('div')[0];document.getElementById('codemirror_ckebg_'+editor.name).removeChild(codemirrordiv);}}]}]};});