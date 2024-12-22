/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('formsendto',function(editor)
{return{title:editor.lang.formsendto.properties,hiddenField:null,minWidth:350,minHeight:70,onShow:function()
{delete this.hiddenField;var editor=this.getParentEditor(),selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='sendtofield')
{this.hiddenField=element;element=editor.restoreRealElement(this.hiddenField);this.setupContent(element);selection.selectElement(this.hiddenField);}},onOk:function()
{if(this.getValueOf('info','value')=='')
return false;var name='sendto',value=this.getValueOf('info','value'),editor=this.getParentEditor(),element=CKEDITOR.env.ie&&!(CKEDITOR.document.$.documentMode>=8)?editor.document.createElement('<input name="'+CKEDITOR.tools.htmlEncode(name)+'">'):editor.document.createElement('input');element.setAttribute('type','hidden');element.setAttribute('name','sendto');this.commitContent(element);var fakeElement=editor.createFakeElement(element,'cke_sendtofield','sendtofield');if(!this.hiddenField)
editor.insertElement(fakeElement);else
{fakeElement.replace(this.hiddenField);editor.getSelection().selectElement(fakeElement);}
return true;},contents:[{id:'info',label:'',title:'',elements:[{type:'html',html:editor.lang.formsendto.choose,},{id:'value',type:'select',label:editor.lang.formsendto.recipient,'default':'',style:'width:398px',items:SendToAddresses,accessKey:'V',setup:function(element)
{this.setValue(element.getAttribute('value')||'');},commit:function(element)
{if(this.getValue())
element.setAttribute('value',this.getValue());else
element.removeAttribute('value');}}]}]}});