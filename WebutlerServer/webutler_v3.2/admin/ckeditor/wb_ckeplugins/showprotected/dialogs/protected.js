/**************************************
	Original file "showprotected" CKEditor plugin
	Created by Matthew Lieder (https://github.com/IGx89)
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('showProtectedDialog',function(editor){return{title:editor.lang.showprotected.title,minWidth:500,minHeight:200,onOk:function(){var newSourceValue=this.getContentElement('info','txtProtectedSource').getValue();var encodedSourceValue=CKEDITOR.plugins.showprotected.encodeProtectedSource(newSourceValue);this._.selectedElement.setAttribute('data-cke-realelement',encodedSourceValue);},onHide:function(){delete this._.selectedElement;},onShow:function(){this._.selectedElement=editor.getSelection().getSelectedElement();var decodedSourceValue=CKEDITOR.plugins.showprotected.decodeProtectedSource(this._.selectedElement.getAttribute('data-cke-realelement'));this.setValueOf('info','txtProtectedSource',decodedSourceValue);},contents:[{id:'info',label:editor.lang.showprotected.title,accessKey:'I',elements:[{id:'txtProtectedSource',type:'textarea',label:editor.lang.showprotected.label,inputStyle:'height:200px;margin-bottom:7px;',required:true,validate:function(){if(!this.getValue()){alert(editor.lang.showprotected.alert);return false;}
return true;}}]}]};});