/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('imageedit',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'brush,brush-rtl',hidpi:true,init:function(editor)
{var EditImageFileURL;if(editor.addMenuItems)
{editor.addMenuItems({imgedit:{label:editor.lang.imageedit.editimage,command:'editimage',icon:this.path+'icons/brush.png',group:'imgedit'}});}
if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection)
{if(!element||!element.is('img')||element.data('cke-realelement')||element.isReadOnly())
return null;EditImageFileURL=element.data('cke-saved-src')||element.getAttribute('src');editor._.imageeditFn=CKEDITOR.tools.addFunction(function(newImg)
{var timestamp=Number(new Date());element.setAttribute('data-cke-saved-src',newImg.newName);element.setAttribute('src',newImg.newName+'?t='+timestamp);if(editor.config.image_prefillDimensions){element.setSize('width',newImg.newWidth);element.setSize('height',newImg.newHeight);}},editor);return{imgedit:CKEDITOR.TRISTATE_OFF};});}
editor.addCommand('editimage',{exec:function(editor)
{var breite=imageeditorWindowWidth;var hoehe=imageeditorWindowHeight;var FileRoot='content/media/image';var FileName=EditImageFileURL.substring(FileRoot.length,EditImageFileURL.length);var ImageURL=encodeURIComponent(FileName)+'&CKEditor='+editor.name+'&CKEditorFuncNum='+editor._.imageeditFn;var url='admin/imageedit/tools.php?imgfile='+ImageURL;if(typeof breite=='string'&&breite.length>1&&breite.substr(breite.length-1,1)=='%')
breite=parseInt(window.screen.width*parseInt(breite.substr(0,breite.length-1))/100);if(typeof hoehe=='string'&&hoehe.length>1&&hoehe.substr(hoehe.length-1,1)=='%')
hoehe=parseInt(window.screen.height*parseInt(hoehe.substr(0,hoehe.length-1))/100);if(breite<640)breite=640;if(hoehe<420)hoehe=420;var oben=parseInt((window.screen.height-hoehe)/2);var links=parseInt((window.screen.width-breite)/2);var options='location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes,width='+breite+',height='+hoehe+',left='+links+',top='+oben;var popupWindow=window.open('','ImgEdit',options,true);if(!popupWindow)
return false;try{popupWindow.resizeTo(breite,hoehe);popupWindow.moveTo(links,oben);popupWindow.focus();popupWindow.location.href=url;}
catch(e){popupWindow=window.open(url,'ImgEdit',options,true);}},canUndo:false});}});