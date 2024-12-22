/*
Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.md or http://ckeditor.com/license
*/
CKEDITOR.plugins.add('docprops',{requires:'wysiwygarea,dialog,colordialog',lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'docprops,docprops-rtl',hidpi:true,init:function(editor){var cmd=new CKEDITOR.dialogCommand('docProps');cmd.modes={wysiwyg:editor.config.fullPage};cmd.allowedContent={body:{styles:'*',attributes:'dir'},html:{attributes:'lang,xml:lang'}};cmd.requiredContent='body';editor.addCommand('docProps',cmd);CKEDITOR.dialog.add('docProps',this.path+'dialogs/docprops.js');editor.ui.addButton&&editor.ui.addButton('DocProps',{label:editor.lang.docprops.label,command:'docProps',toolbar:'document,30'});}});