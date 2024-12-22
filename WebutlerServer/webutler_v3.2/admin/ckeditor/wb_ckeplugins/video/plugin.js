/**************************************
	Original file Video plugin for CKEditor
	Copyright (C) 2011 Alfonso Martínez de Lizarrondo
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function(){CKEDITOR.plugins.add('video',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'video',hidpi:true,onLoad:function()
{CKEDITOR.addCss('img.cke_video {'+' background-image: url('+CKEDITOR.getUrl(this.path+'images/videofakeimg.gif')+');'+' background-position: center center;'+' background-repeat: no-repeat;'+' background-color:#fff;'+' outline: 1px solid #A9A9A9;'+'}');},init:function(editor)
{var lang=editor.lang.video;CKEDITOR.dialog.add('video',this.path+'dialogs/video.js');editor.addCommand('video',new CKEDITOR.dialogCommand('video'));editor.ui.addButton('Video',{label:lang.toolbar,command:'video'});if(editor.addMenuItems)
{editor.addMenuItems({video:{label:lang.properties,command:'video',group:'flash'}});}
editor.on('doubleclick',function(evt)
{var element=evt.data.element;if(element.is('img')&&element.data('cke-real-element-type')=='video')
evt.data.dialog='video';});if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection)
{if(element&&element.is('img')&&!element.isReadOnly()&&element.data('cke-real-element-type')=='video')
return{video:CKEDITOR.TRISTATE_OFF};});}
CKEDITOR.dtd.$empty['cke:source']=1;CKEDITOR.dtd.$empty['source']=1;},afterInit:function(editor)
{var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;dataFilter.addRules({elements:{$:function(realElement)
{if(realElement.name=='video')
{realElement.name='cke:video';for(var i=0;i<realElement.children.length;i++)
{if(realElement.children[i].name=='source')
realElement.children[i].name='cke:source';}
var width=realElement.attributes.width,height=realElement.attributes.height,attrclass=realElement.attributes['class'];var classes=typeof attrclass!==undefined&&attrclass!=null&&attrclass!=''?' '+attrclass:'';var fakeElement=editor.createFakeParserElement(realElement,'cke_video'+classes,'video',false),fakeStyle=fakeElement.attributes.style||'';if(typeof width!==undefined&&width!=null&&width!='')
fakeElement.attributes.width=width;else
fakeElement.attributes.width='160';if(typeof height!==undefined&&height!=null&&height!='')
fakeElement.attributes.height=height;else
fakeElement.attributes.height='120';return fakeElement;}}}});}});})();