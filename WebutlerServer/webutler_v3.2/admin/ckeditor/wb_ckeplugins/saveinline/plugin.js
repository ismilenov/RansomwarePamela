/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('saveinline',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'save',hidpi:true,init:function(editor)
{editor.addCommand('saveinline',{exec:function(editor)
{var editorsaveform=document.getElementById('wb_editorsaveform');editorsaveform.submit();}});editor.ui.addButton('Save',{label:editor.lang.saveinline.save,command:'saveinline'});}});