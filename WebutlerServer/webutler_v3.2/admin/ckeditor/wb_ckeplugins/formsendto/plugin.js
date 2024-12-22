/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.scriptLoader.load(CKEDITOR.plugins.getPath('formsendto')+'dialogs/sendtos.php');CKEDITOR.plugins.add('formsendto',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'formsendto,formsendto-rtl',hidpi:true,init:function(editor)
{CKEDITOR.addCss('img.cke_sendtofield {'+'  background-image: url('+CKEDITOR.getUrl(this.path+'icons/formsendto.png')+');'+'    background-position: center center;'+'    background-repeat: no-repeat;'+'    width: 18px;'+'    height: 18px;'+'}');editor.addCommand('formsendto',new CKEDITOR.dialogCommand('formsendto'));editor.on('doubleclick',function(evt)
{var element=evt.data.element;if(element&&element.data('cke-real-element-type')=='sendtofield'&&element.hasClass('cke_sendtofield'))
evt.data.dialog='formsendto';else
return null;});if(editor.addMenuItems)
{editor.addMenuItems({sendtofield:{label:editor.lang.formsendto.properties,command:'formsendto',icon:this.path+'icons/formsendto.png',group:'sendtofield'}});}
if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection)
{if(element&&element.data('cke-real-element-type')=='sendtofield'&&element.hasClass('cke_sendtofield'))
return{sendtofield:CKEDITOR.TRISTATE_OFF};else
return null;});}
CKEDITOR.dialog.add('formsendto',this.path+'dialogs/formsendto.js');var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;if(dataFilter)
{dataFilter.addRules({elements:{input:function(element)
{if(element.attributes.type=='hidden'&&element.attributes.name=='sendto')
return editor.createFakeParserElement(element,'cke_sendtofield','sendtofield',false);}}},{applyToAll:true});}},requires:['dialog','fakeobjects']});})();