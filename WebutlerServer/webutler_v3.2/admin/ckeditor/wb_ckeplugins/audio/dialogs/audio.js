/**************************************
	Original file Video plugin for CKEditor
	Copyright (C) 2011 Alfonso Martínez de Lizarrondo
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('audio',function(editor)
{var lang=editor.lang.audio;function setWidth(style)
{var result='';if(typeof style!==undefined&&style!=null&&style!=''){var styles=style.split(';');for(var i=0;i<styles.length;i++)
{var styleAttr=styles[i].split(':');if(CKEDITOR.tools.trim(styleAttr[0]).toLowerCase()=='width')
{result=CKEDITOR.tools.trim(styleAttr[1]);if(result.substr(result.length-1,1)!='%')
result=parseInt(result).toString();break;}}}
return result!=''?result:'';}
function commitValue(audioNode,extraStyles)
{var isCheckbox=(this instanceof CKEDITOR.ui.dialog.checkbox);var value=this.getValue();if(isCheckbox){if(!value)
audioNode.removeAttribute(this.id);else
audioNode.setAttribute(this.id,this.id);}
else{if(!value)
audioNode.removeAttribute(this.id);else
audioNode.setAttribute(this.id,value);}}
function commitSrc(audioNode,extraStyles,audios)
{var match=this.id.match(/(\w+)(\d)/),id=match[1],number=parseInt(match[2],10);var audio=audios[number]||(audios[number]={});audio[id]=this.getValue();}
function loadValue(audioNode)
{var isCheckbox=(this instanceof CKEDITOR.ui.dialog.checkbox);var value=audioNode.getAttribute(this.id);if(isCheckbox){this.setValue(value==this.id?true:false);}
else{if(value||value!='')
this.setValue(value);}}
function loadSrc(audioNode,audios)
{var match=this.id.match(/(\w+)(\d)/),id=match[1],number=parseInt(match[2],10);var audio=audios[number];if(!audio)
return;this.setValue(audio[id]);}
return{title:lang.dialogTitle,minWidth:400,minHeight:150,onShow:function()
{this.fakeImage=this.audioNode=null;var fakeImage=this.getSelectedElement();if(fakeImage&&fakeImage.data('cke-real-element-type')&&fakeImage.data('cke-real-element-type')=='audio')
{this.fakeImage=fakeImage;var audioNode=editor.restoreRealElement(fakeImage),audios=[],sourceList=audioNode.getElementsByTag('source','');if(sourceList.count()==0)
sourceList=audioNode.getElementsByTag('source','cke');for(var i=0,length=sourceList.count();i<length;i++)
{var item=sourceList.getItem(i);audios.push({src:item.getAttribute('src'),type:item.getAttribute('type')});}
this.audioNode=audioNode;this.setupContent(audioNode,audios);var classes=audioNode.getAttribute('class');if(classes!=undefined&&classes!=null&&classes!=''){this.setValueOf('info','audioclass',classes);}
var attrStyle=audioNode.getAttribute('style');var attrWidth=setWidth(attrStyle);if(attrWidth!=undefined&&attrWidth!=null&&attrWidth!=''){this.setValueOf('info','audiowidth',attrWidth);}}
else
this.setupContent(null,[]);},onOk:function()
{var audioNode=null;if(!this.fakeImage)
{audioNode=CKEDITOR.dom.element.createFromHtml('<cke:audio></cke:audio>',editor.document);}
else
{audioNode=this.audioNode;}
audioNode.setAttribute('preload','metadata');var extraStyles={},audios=[];this.commitContent(audioNode,extraStyles,audios);var widthVal=this.getValueOf('info','audiowidth');if(widthVal!='')
audioNode.setAttribute('style','width:'+(widthVal.substr(widthVal.length-1,1)=='%'?widthVal:parseInt(widthVal)+'px'));else
audioNode.removeAttribute('style');var innerHtml='';for(var i=0;i<audios.length;i++)
{var audio=audios[i];if(!audio||!audio.src)
continue;innerHtml+='<cke:source src="'+audio.src+'" type="'+audio.type+'" />';}
audioNode.setHtml(innerHtml+lang.error);var classes=this.getValueOf('info','audioclass')!=''?' '+this.getValueOf('info','audioclass'):'';var newFakeImage=editor.createFakeElement(audioNode,'cke_audio'+classes,'audio',false);newFakeImage.setAttribute('width',widthVal!=''?widthVal:'300');newFakeImage.setStyles(extraStyles);if(this.fakeImage)
{var selection=editor.getSelection();newFakeImage.replace(this.fakeImage);selection.selectElement(newFakeImage);}
else
{editor.insertElement(newFakeImage);}},contents:[{id:'info',elements:[{type:'hbox',widths:['40%','20%','20%','20%'],children:[{type:'text',id:'audiowidth',label:editor.lang.common.width,'default':''},{type:'checkbox',id:'controls',label:lang.controls,'default':true,style:'margin:20px 0 0 10px;',commit:commitValue,setup:loadValue},{type:'checkbox',id:'autoplay',label:lang.autoplay,'default':false,style:'margin:20px 0 0 0;',commit:commitValue,setup:loadValue},{type:'checkbox',id:'loop',label:lang.loop,'default':false,style:'margin:20px 0 0 0;',commit:commitValue,setup:loadValue}]},{type:'hbox',widths:['100%'],children:[{type:'html',html:lang.volume}]},{type:'hbox',widths:['100%'],children:[{id:'volume',type:'radio',label:'',items:[[' '+lang.mute,'0.0'],[' 25%','0.25'],[' 50%','0.5'],[' 75%','0.75'],[' 100%','1.0']],'default':'1.0',commit:commitValue,setup:loadValue}]},{type:'hbox',widths:['','100px','75px'],children:[{type:'text',id:'src0',label:lang.sourceaudio,commit:commitSrc,setup:loadSrc},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:src0',url:editor.config.filebrowserAVBrowseUrl},label:editor.lang.common.browseServer},{id:'type0',label:lang.sourceType,type:'select','default':'audio/mp3',items:[['MP3','audio/mp3'],['WAV','audio/wav']],commit:commitSrc,setup:loadSrc}]},{type:'hbox',widths:['','100px','75px'],children:[{type:'text',id:'src1',label:lang.sourceaudio,commit:commitSrc,setup:loadSrc},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:src1',url:editor.config.filebrowserAVBrowseUrl},label:editor.lang.common.browseServer},{id:'type1',label:lang.sourceType,type:'select','default':'audio/wav',items:[['MP3','audio/mp3'],['WAV','audio/wav']],commit:commitSrc,setup:loadSrc}]},{type:'hbox',widths:['100%'],children:[{type:'text',id:'audioclass',label:lang.audioclass,'default':''}]},{type:'hbox',widths:['100%'],children:[{type:'text',id:'id',label:lang.audioid,'default':'',commit:commitValue,setup:loadValue}]}]}]};});