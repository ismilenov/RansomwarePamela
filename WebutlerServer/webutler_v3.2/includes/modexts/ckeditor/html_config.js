/**
 * Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	//config.removePlugins = 'bbcode,justify,indent,indentlist,list,blockquote';
	config.removePlugins = 'bbcode,justify,blockquote';
	config.removeButtons = 'Cut,Copy,Paste,PasteText,Underline,Strike,Subscript,Superscript,NumberedList,Blockquote,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Anchor,BGColor';
	config.toolbarGroups = [
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'forms' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'about' }
	];
	config.toolbar = [
		[ 'Bold', 'Italic' ],
		[ 'TextColor' ],
		[ 'BulletedList', 'Indent', 'Outdent' ],
		[ 'Undo', 'Redo' ],
		[ 'Link', 'Unlink' ],
		[ 'Source' ]
	];
	config.stylesSet = [];
    config.forcePasteAsPlainText = true;
	config.entities = false;
    config.height = '150px';
	config.removeDialogTabs = 'link:advanced';
};

CKEDITOR.on('dialogDefinition', function( ev )
{
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;
    var editor = ev.editor;
    
	dialogDefinition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;
});

