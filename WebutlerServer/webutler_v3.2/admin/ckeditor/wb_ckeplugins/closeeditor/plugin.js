/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('closeeditor',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'closer',hidpi:true,init:function(editor)
{editor.ui.addButton('Closer',{label:editor.lang.closeeditor.close,command:'closeeditor'});editor.addCommand('closeeditor',{exec:function(editor)
{window.location.href=window.location;},canUndo:false});}});