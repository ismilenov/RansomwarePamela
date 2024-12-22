/**************************************
	Webutler V3.2 - www.webutler.de
	Copyright (c) 2008 - 2016
	Autor: Sven Zinke
	Free for any use
	Lizenz: GPL
**************************************/
(function(){CKEDITOR.plugins.add('columns',{requires:'dialog',lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'columns,columns-rtl',hidpi:true,init:function(editor){CKEDITOR.scriptLoader.load(CKEDITOR.getUrl(this.path+'dialogs/config.php'));CKEDITOR.dialog.add('columns',CKEDITOR.getUrl(this.path+'dialogs/columns.js'));editor.addCommand('columns',new CKEDITOR.dialogCommand('columns'));editor.ui.addButton&&editor.ui.addButton('Columns',{label:editor.lang.columns.title,command:'columns'});}});})();