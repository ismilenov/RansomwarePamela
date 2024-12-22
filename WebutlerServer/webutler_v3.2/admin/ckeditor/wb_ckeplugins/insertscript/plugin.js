/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('insertscript',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'insertscript',hidpi:true,init:function(editor)
{CKEDITOR.scriptLoader.load(this.path+'dialogs/loadjs.php');CKEDITOR.document.appendStyleSheet(this.path+'dialogs/style.css');var command=editor.addCommand('insertscript',new CKEDITOR.dialogCommand('insertscript'));command.canUndo=true;editor.ui.addButton('InsertScript',{label:editor.lang.insertscript.insertjs,command:'insertscript'});CKEDITOR.dialog.add('insertscript',this.path+'dialogs/insertscript.js');}});