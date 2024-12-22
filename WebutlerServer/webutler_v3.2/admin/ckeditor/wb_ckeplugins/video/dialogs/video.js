/**************************************
	Original file Video plugin for CKEditor
	Copyright (C) 2011 Alfonso Martínez de Lizarrondo
	
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('video',function(editor)
{var lang=editor.lang.video;function commitValue(videoNode,extraStyles)
{var isCheckbox=(this instanceof CKEDITOR.ui.dialog.checkbox);var value=this.getValue();if(isCheckbox){if(!value)
videoNode.removeAttribute(this.id);else
videoNode.setAttribute(this.id,this.id);}
else{if(!value)
videoNode.removeAttribute(this.id);else
videoNode.setAttribute(this.id,value);}}
function commitSrc(videoNode,extraStyles,videos)
{var match=this.id.match(/(\w+)(\d)/),id=match[1],number=parseInt(match[2],10);var video=videos[number]||(videos[number]={});video[id]=this.getValue();}
function loadValue(videoNode)
{var isCheckbox=(this instanceof CKEDITOR.ui.dialog.checkbox);var value=videoNode.getAttribute(this.id);if(isCheckbox){this.setValue(value==this.id?true:false);}
else{if(value&&value!=''&&value!==undefined)
this.setValue(value);}}
function loadSrc(videoNode,videos)
{var match=this.id.match(/(\w+)(\d)/),id=match[1],number=parseInt(match[2],10);var video=videos[number];if(!video)
return;this.setValue(video[id]);}
function generateId()
{var now=parseInt((new Date().getTime())/1000);return'video'+now;}
return{title:lang.dialogTitle,minWidth:400,minHeight:200,onShow:function()
{this.fakeImage=this.videoNode=null;this.previewImage=editor.document.createElement('img');var fakeImage=this.getSelectedElement();if(fakeImage&&fakeImage.data('cke-real-element-type')&&fakeImage.data('cke-real-element-type')=='video')
{this.fakeImage=fakeImage;var videoNode=editor.restoreRealElement(fakeImage),videos=[],sourceList=videoNode.getElementsByTag('source','');if(sourceList.count()==0)
sourceList=videoNode.getElementsByTag('source','cke');for(var i=0,sourceLength=sourceList.count();i<sourceLength;i++)
{var item=sourceList.getItem(i);videos.push({src:item.getAttribute('src'),type:item.getAttribute('type')});}
this.videoNode=videoNode;this.setupContent(videoNode,videos);var selection=editor.getSelection();var parent=selection.getCommonAncestor();if(parent.getName()=='div'&&parent.hasAttribute('data-source')&&parent.getAttribute('data-source')=='video'){var classes=parent.getAttribute('class');if(classes!==undefined&&classes!==null&&classes!=''){this.setValueOf('info','videoclass',classes);this.setValueOf('info','class2div',true);}}
else{var classes=videoNode.getAttribute('class');if(classes!==undefined&&classes!==null&&classes!=''){this.setValueOf('info','videoclass',classes);}}}
else
this.setupContent(null,[]);},onOk:function()
{var videoNode=null;if(!this.fakeImage)
{videoNode=CKEDITOR.dom.element.createFromHtml('<cke:video></cke:video>',editor.document);}
else
{videoNode=this.videoNode;}
videoNode.setAttribute('preload','metadata');var extraStyles={},videos=[];this.commitContent(videoNode,extraStyles,videos);var innerHtml='';for(var i=0;i<videos.length;i++)
{var video=videos[i];if(!video||!video.src)
continue;innerHtml+='<cke:source src="'+video.src+'" type="'+video.type+'" />';}
videoNode.setHtml(innerHtml+lang.error);var classes=this.getValueOf('info','videoclass')!=''&&this.getValueOf('info','class2div')==''?' '+this.getValueOf('info','videoclass'):'';var newFakeImage=editor.createFakeElement(videoNode,'cke_video'+classes,'video',false);newFakeImage.setStyles(extraStyles);if(this.getValueOf('info','width')!='')
newFakeImage.setAttribute('width',this.getValueOf('info','width'));if(this.getValueOf('info','height')!='')
newFakeImage.setAttribute('height',this.getValueOf('info','height'));var divTag='';if(this.getValueOf('info','videoclass')!=''&&this.getValueOf('info','class2div')===true)
divTag=CKEDITOR.dom.element.createFromHtml('<div class="'+this.getValueOf('info','videoclass')+'" data-source="video"></div>');if(this.fakeImage)
{var selection=editor.getSelection();var parent=selection.getCommonAncestor();if(parent.getName()=='div'&&parent.hasAttribute('data-source')&&parent.getAttribute('data-source')=='video'){if(divTag!=''){parent.removeAttribute('class');parent.setAttribute('class',this.getValueOf('info','videoclass'));newFakeImage.replace(this.fakeImage);}
else{newFakeImage.replace(parent);}}
else{if(divTag!=''){divTag.replace(this.fakeImage);divTag.append(newFakeImage,true);}
else{newFakeImage.replace(this.fakeImage);}}
selection.selectElement(newFakeImage);}
else
{if(divTag!=''){editor.insertElement(divTag);divTag.append(newFakeImage,true);}
else{editor.insertElement(newFakeImage);}}},contents:[{id:'info',elements:[{type:'hbox',widths:['','100px'],children:[{type:'text',id:'poster',label:lang.poster,commit:commitValue,setup:loadValue},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:poster',url:editor.config.filebrowserImageBrowseUrl},label:editor.lang.common.browseServer}]},{type:'hbox',widths:['50%','50%'],children:[{type:'text',id:'width',label:editor.lang.common.width,'default':'',commit:commitValue,setup:loadValue},{type:'text',id:'height',label:editor.lang.common.height,'default':'',commit:commitValue,setup:loadValue}]},{type:'hbox',widths:['33%','33%','33%'],children:[{type:'checkbox',id:'controls',label:lang.controls,'default':true,commit:commitValue,setup:loadValue},{type:'checkbox',id:'autoplay',label:lang.autoplay,'default':false,commit:commitValue,setup:loadValue},{type:'checkbox',id:'loop',label:lang.loop,'default':false,commit:commitValue,setup:loadValue}]},{type:'hbox',widths:['','100px','75px'],children:[{type:'text',id:'src0',label:lang.sourceVideo,commit:commitSrc,setup:loadSrc},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:src0',url:editor.config.filebrowserAVBrowseUrl},label:editor.lang.common.browseServer},{id:'type0',label:lang.sourceType,type:'select','default':'video/mp4',items:[['MP4','video/mp4'],['OGG','video/ogg'],['WebM','video/webm']],commit:commitSrc,setup:loadSrc}]},{type:'hbox',widths:['','100px','75px'],children:[{type:'text',id:'src1',label:lang.sourceVideo,commit:commitSrc,setup:loadSrc},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:src1',url:editor.config.filebrowserAVBrowseUrl},label:editor.lang.common.browseServer},{id:'type1',label:lang.sourceType,type:'select','default':'video/ogg',items:[['MP4','video/mp4'],['OGG','video/ogg'],['WebM','video/webm']],commit:commitSrc,setup:loadSrc}]},{type:'hbox',widths:['','100px','75px'],children:[{type:'text',id:'src2',label:lang.sourceVideo,commit:commitSrc,setup:loadSrc},{type:'button',id:'browse',hidden:'true',style:'display:inline-block;margin-top:15px;',filebrowser:{action:'Browse',target:'info:src2',url:editor.config.filebrowserAVBrowseUrl},label:editor.lang.common.browseServer},{id:'type2',label:lang.sourceType,type:'select','default':'video/webm',items:[['MP4','video/mp4'],['OGG','video/ogg'],['WebM','video/webm']],commit:commitSrc,setup:loadSrc}]},{type:'hbox',widths:['100%'],children:[{type:'text',id:'videoclass',label:lang.videoclass,'default':''}]},{type:'hbox',widths:['100%'],children:[{type:'checkbox',id:'class2div',label:' '+lang.class2div,'default':'',value:'checked'}]},{type:'hbox',widths:['100%'],children:[{type:'text',id:'id',label:lang.videoid,'default':'',commit:commitValue,setup:loadValue}]}]}]};});