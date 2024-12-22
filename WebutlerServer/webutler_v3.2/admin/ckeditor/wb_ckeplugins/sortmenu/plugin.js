/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
var treemenubuilder;(function()
{CKEDITOR.plugins.add('sortmenu',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'sortmenu',hidpi:true,init:function(editor)
{var confirmRemove;CKEDITOR.scriptLoader.load(CKEDITOR.plugins.getPath('pagelinks')+'pages.php');CKEDITOR.scriptLoader.load(this.path+'dialogs/treebuilder.js');CKEDITOR.scriptLoader.load(this.path+'dialogs/treeplugins.js');CKEDITOR.scriptLoader.load(this.path+'dialogs/html.js');CKEDITOR.document.appendStyleSheet(this.path+'dialogs/tree.css');var command=editor.addCommand('sortmenu',new CKEDITOR.dialogCommand('sortmenu'));editor.ui.addButton('SortMenu',{label:editor.lang.sortmenu.title,command:'sortmenu'});editor.on('selectionChange',function(evt)
{var path=evt.data.path;var list=path.contains('ul');command.setState(list?CKEDITOR.TRISTATE_OFF:CKEDITOR.TRISTATE_DISABLED);});CKEDITOR.dialog.add('sortmenu',this.path+'dialogs/sortmenu.js');}});})();