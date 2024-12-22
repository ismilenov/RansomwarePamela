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
CKEDITOR.plugins.add('sourcedialog',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'sourcedialog,sourcedialog-rtl',hidpi:true,init:function(editor){editor.addCommand('sourcedialog',new CKEDITOR.dialogCommand('sourcedialog'));CKEDITOR.dialog.add('sourcedialog',this.path+'dialogs/sourcedialog.js');if(editor.ui.addButton){editor.ui.addButton('Sourcedialog',{label:editor.lang.sourcedialog.toolbar,command:'sourcedialog',toolbar:'mode,10'});}
CKEDITOR.scriptLoader.load(codemirror_rootpath+'codemirror/editor/js/codemirror.js');CKEDITOR.scriptLoader.load(codemirror_rootpath+'codemirror/lang/'+CKEDITOR.lang.detect(CKEDITOR.config.language)+'.js');CKEDITOR.document.appendStyleSheet(codemirror_rootpath+'codemirror/config/editor.css');CKEDITOR.scriptLoader.load(codemirror_rootpath+'codemirror/codemirror_config.js');}});