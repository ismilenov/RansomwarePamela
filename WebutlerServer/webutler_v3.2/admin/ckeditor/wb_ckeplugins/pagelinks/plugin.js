/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.plugins.add('pagelinks',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],requires:'uioptselect'});CKEDITOR.scriptLoader.load(CKEDITOR.plugins.getPath('pagelinks')+'pages.php');CKEDITOR.on('dialogDefinition',function(ev)
{var dialogName=ev.data.name;var dialogDefinition=ev.data.definition;var editor=ev.editor;if(dialogName=='link')
{var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'optselect',id:'intern',label:editor.lang.pagelinks.internpage,'default':'',style:'width:350px',items:InternPagesSelectBox,onChange:function()
{var d=CKEDITOR.dialog.getCurrent();d.setValueOf('info','url',this.getValue());if(this.getValue()!='')
d.setValueOf('info','protocol','');},setup:function(data)
{this.allowOnChange=false;this.setValue(data.url?data.url.url:'');this.allowOnChange=true;}},'browse');dialogDefinition.onLoad=function()
{var internField=this.getContentElement('info','intern');internField.reset();};}});})();