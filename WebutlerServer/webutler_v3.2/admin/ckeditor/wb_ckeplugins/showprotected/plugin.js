/**************************************
	Original file "showprotected" CKEditor plugin
	Created by Matthew Lieder (https://github.com/IGx89)
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('showprotected',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'editprotected,editprotected-rtl',hidpi:true,requires:'dialog,fakeobjects',onLoad:function(){var iconPath=CKEDITOR.getUrl(this.path+'images/code.png'),baseStyle='background:url('+iconPath+') no-repeat %1 center;background-size:16px;';var template='.%2 img.cke_protected'+'{'+
baseStyle+'width:16px;'+'min-height:15px;'+'margin-top:-10px;'+'position:relative;'+'height:1.15em;'+'vertical-align:'+(CKEDITOR.env.opera?'middle':'text-bottom')+';'+'}';function cssWithDir(dir){return template.replace(/%1/g,dir=='rtl'?'right':'left').replace(/%2/g,'cke_contents_'+dir);}
CKEDITOR.addCss(cssWithDir('ltr')+cssWithDir('rtl'));},init:function(editor){CKEDITOR.dialog.add('showProtectedDialog',this.path+'dialogs/protected.js');editor.addCommand('editprotected',new CKEDITOR.dialogCommand('showProtectedDialog'));if(editor.addMenuItems){editor.addMenuItems({editprotected:{label:editor.lang.showprotected.editprotected,command:'editprotected',icon:this.path+'icons/editprotected.png',group:'editprotected'}});}
if(editor.contextMenu){editor.contextMenu.addListener(function(element,selection){if(element.is('img')&&element.hasClass('cke_protected')){return{editprotected:CKEDITOR.TRISTATE_OFF};}});}
editor.on('doubleclick',function(evt){var element=evt.data.element;if(element.is('img')&&element.hasClass('cke_protected')){evt.data.dialog='showProtectedDialog';editor.execCommand('enableObjectResizing',false,false);}});},afterInit:function(editor){var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;if(dataFilter){dataFilter.addRules({comment:function(commentText,commentElement){if(commentElement.getAscendant('head')==null&&commentText.indexOf(CKEDITOR.plugins.showprotected.protectedSourceMarker)==0)
{var decodedSource=decodeURIComponent(commentText);var alt='';if(decodedSource.substring(0,22)==CKEDITOR.plugins.showprotected.protectedSourceMarker+'<script')
alt='protected_script';if(decodedSource.substring(0,17)==CKEDITOR.plugins.showprotected.protectedSourceMarker+'<?')
alt='protected_php';commentElement.attributes=[];var fakeElement=editor.createFakeParserElement(commentElement,'cke_protected',alt,false);return fakeElement;}
return null;}});}}});CKEDITOR.plugins.showprotected={protectedSourceMarker:'{cke_protected}',decodeProtectedSource:function(protectedSource){return decodeURIComponent(protectedSource).replace(/<!--\{cke_protected\}([\s\S]+?)-->/gi,function(match,data){return decodeURIComponent(data);});},encodeProtectedSource:function(protectedSource){return'<!--'+CKEDITOR.plugins.showprotected.protectedSourceMarker+
encodeURIComponent(protectedSource).replace(/--/g,'%2D%2D')+'-->';}};