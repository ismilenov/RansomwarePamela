/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.plugins.formfields={lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],init:function(editor)
{if(editor.addMenuItems)
{editor.addMenuItems({formfelder:{label:editor.lang.formfields.title,group:'formfelder',order:1,getItems:function()
{return{_textfield:CKEDITOR.TRISTATE_OFF,_textarea:CKEDITOR.TRISTATE_OFF,_checkbox:CKEDITOR.TRISTATE_OFF,_radio:CKEDITOR.TRISTATE_OFF,_select:CKEDITOR.TRISTATE_OFF,_hiddenfield:CKEDITOR.TRISTATE_OFF,_button:CKEDITOR.TRISTATE_OFF,_sendtofield:CKEDITOR.TRISTATE_OFF};}},_textfield:{label:editor.lang.common.textField,group:'textfield',icon:'textfield',command:'textfield',order:20},_textarea:{label:editor.lang.common.textarea,group:'textarea',icon:'textarea',command:'textarea',order:21},_checkbox:{label:editor.lang.common.checkbox,group:'checkbox',icon:'checkbox',command:'checkbox',order:22},_radio:{label:editor.lang.common.radio,group:'radio',icon:'radio',command:'radio',order:23},_select:{label:editor.lang.common.select,group:'select',icon:'select',command:'select',order:24},_hiddenfield:{label:editor.lang.common.hiddenField,group:'hiddenfield',icon:'hiddenfield',command:'hiddenfield',order:25},_button:{label:editor.lang.common.button,group:'button',icon:'button',command:'button',order:26},_sendtofield:{label:editor.lang.formsendto.recipient,group:'sendtofield',icon:'formsendto',command:'formsendto',order:27}});}
if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection,path)
{var isForm=path.contains('cke:form',1);if(isForm&&element.getName()!='input'&&element.getName()!='textarea'&&element.getName()!='button'&&element.getName()!='select'&&!element.data('cke-realelement'))
{return{formfelder:CKEDITOR.TRISTATE_OFF};}
return null;});}}};CKEDITOR.plugins.add('formfields',CKEDITOR.plugins.formfields);})();