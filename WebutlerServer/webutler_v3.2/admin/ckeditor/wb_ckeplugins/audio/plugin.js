/**************************************
	Original file Video plugin for CKEditor
	Copyright (C) 2011 Alfonso Martínez de Lizarrondo
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function(){function getWidth(style)
{var result='';if(typeof style!==undefined&&style!=null&&style!=''){var styles=style.split(';');for(var i=0;i<styles.length;i++)
{var styleAttr=styles[i].split(':');if(CKEDITOR.tools.trim(styleAttr[0]).toLowerCase()=='width')
{result=CKEDITOR.tools.trim(styleAttr[1]);if(result.substr(result.length-1,1)!='%')
result=parseInt(result).toString()+'px';break;}}}
return result!=''?result:'';}
CKEDITOR.plugins.add('audio',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'audio',hidpi:true,onLoad:function()
{CKEDITOR.addCss('img.cke_audio {'+'background-image: url('+CKEDITOR.getUrl(this.path+'images/audiofakeimg.gif')+');'+'background-position: center center;'+'background-repeat: no-repeat;'+'background-color:#fff;'+'outline: 1px solid #A9A9A9;'+'height: 30px;'+'}');},init:function(editor)
{var lang=editor.lang.audio;CKEDITOR.dialog.add('audio',this.path+'dialogs/audio.js');editor.addCommand('audio',new CKEDITOR.dialogCommand('audio'));editor.ui.addButton('Audio',{label:lang.toolbar,command:'audio'});if(editor.addMenuItems)
{editor.addMenuItems({audio:{label:lang.properties,command:'audio',group:'flash'}});}
editor.on('doubleclick',function(evt)
{var element=evt.data.element;if(element.is('img')&&element.data('cke-real-element-type')=='audio')
evt.data.dialog='audio';});if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection)
{if(element&&element.is('img')&&!element.isReadOnly()&&element.data('cke-real-element-type')=='audio')
return{audio:CKEDITOR.TRISTATE_OFF};});}
CKEDITOR.dtd.$empty['cke:source']=1;CKEDITOR.dtd.$empty['source']=1;},afterInit:function(editor)
{var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;dataFilter.addRules({elements:{$:function(realElement)
{if(realElement.name=='audio')
{realElement.name='cke:audio';for(var i=0;i<realElement.children.length;i++)
{if(realElement.children[i].name=='source')
realElement.children[i].name='cke:source'}
var style=realElement.attributes['style'];var width=getWidth(style);var attrclass=realElement.attributes['class'];var classes=typeof attrclass!==undefined&&attrclass!=null&&attrclass!=''?' '+attrclass:'';var fakeElement=editor.createFakeParserElement(realElement,'cke_audio'+classes,'audio',false),fakeStyle=fakeElement.attributes.style||'';var attrWidth=realElement.attributes.width;var attrHeight=realElement.attributes.height;if(typeof attrWidth!==undefined&&attrWidth!=null&&attrWidth!='')
delete realElement.attributes.width;if(typeof attrHeight!==undefined&&attrHeight!=null&&attrHeight!='')
delete realElement.attributes.height;if(typeof width!==undefined&&width!=null&&width!='')
fakeElement.attributes.width=width;else
fakeElement.attributes.width='300';return fakeElement;}}}});}});})();