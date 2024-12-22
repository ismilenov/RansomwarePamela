/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{var iframeWindow=null;function getStyle(Style,Name)
{var result='';if(Style){var Styles=Style.split(';');for(var i=0;i<Styles.length;i++)
{var styleAttr=Styles[i].split(':');if(CKEDITOR.tools.trim(styleAttr[0]).toLowerCase()==Name)
{result=CKEDITOR.tools.trim(styleAttr[1]);if(result.substr(result.length-1,1)!='%')
result=parseInt(result).toString();break;}}}
return result?result:'';}
function createFakeElement(editor,realElement,width,height,classes)
{var fakeElement=editor.createFakeParserElement(realElement,'cke_googlemap'+(classes!==undefined&&classes!=null&&classes!=''?' '+classes:''),'googlemap',false);var fakeStyle=fakeElement.attributes.style||'';delete fakeElement.attributes.width;delete fakeElement.attributes.height;var styles=new Array();if(width.length>0){var styleWidth='width: '+(width.substr(width.length-1,1)=='%'?width:parseInt(width)+'px');styles.push(styleWidth);}
if(height.length>0){var styleHeight='height: '+(height.substr(height.length-1,1)=='%'?height:parseInt(height)+'px');styles.push(styleHeight);}
fakeStyle=fakeElement.attributes.style=fakeStyle+styles.join('; ');return fakeElement;}
CKEDITOR.plugins.add('googlemap',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'gmap',hidpi:true,init:function(editor)
{CKEDITOR.document.appendStyleSheet(this.path+'dialogs/mapinfowin.css');CKEDITOR.addCss('img.cke_googlemap {'+'background-image: url('+CKEDITOR.getUrl(this.path+'images/mapfakeimg.gif')+');'+'background-position: center center;'+'background-repeat: no-repeat;'+'outline: 1px solid #A9A9A9;'+'width: 160px;'+'height: 120px;'+'}');editor.addCommand('googlemap',new CKEDITOR.dialogCommand('googlemap'));editor.ui.addButton('gMap',{label:editor.lang.googlemap.title,command:'googlemap'});editor.on('doubleclick',function(evt)
{var element=evt.data.element;if(element.is('img')&&element.data('cke-real-element-type')=='googlemap')
{evt.data.dialog='googlemap';}
else
return null;});if(editor.addMenuItems)
{editor.addMenuItems({googlemaps:{label:editor.lang.googlemap.title,command:'googlemap',icon:this.path+'icons/gmap.png',group:'googlemaps'}});}
if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection)
{if(element&&element.is('img')&&!element.isReadOnly()&&element.data('cke-real-element-type')=='googlemap')
return{googlemaps:CKEDITOR.TRISTATE_OFF};else
return null;});}
CKEDITOR.dialog.add('googlemap',this.path+'dialogs/googlemap.js');},afterInit:function(editor)
{var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;if(dataFilter)
{dataFilter.addRules({elements:{div:function(element)
{var attributes=element.attributes;if(attributes['data-source']=='googlemapsframe')
{var width=getStyle(attributes['style'],'width')||'';var height=getStyle(attributes['style'],'height')||'';var classes=attributes['class']||'';return createFakeElement(editor,element,width,height,classes);}}}});}},requires:['dialog','iframedialog','fakeobjects']});})();