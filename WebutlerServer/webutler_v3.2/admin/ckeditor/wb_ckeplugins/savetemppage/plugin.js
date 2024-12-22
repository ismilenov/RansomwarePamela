/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('savetemppage',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'savetemp',hidpi:true,init:function(editor)
{editor.addCommand('savetemppage',{exec:function(editor)
{var editorsaveform=document.getElementById('wb_editorsaveform');var input=document.createElement('input');input.type='hidden';input.name='saveastemp';input.value='1';editorsaveform.appendChild(input);editorsaveform.submit();}});editor.ui.addButton('Savetemp',{label:editor.lang.savetemppage.save,command:'savetemppage'});}});