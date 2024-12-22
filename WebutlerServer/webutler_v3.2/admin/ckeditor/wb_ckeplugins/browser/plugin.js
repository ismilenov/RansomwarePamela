/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.plugins.add('browser',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],icons:'media',hidpi:true,init:function(editor)
{editor.ui.addButton('Media',{label:editor.lang.browser.mediabrowser,command:'browser'});editor.addCommand('browser',{exec:function(editor)
{var iWidth=editor.config.filebrowserWindowWidth;if(typeof iWidth=='string'&&iWidth.length>1&&iWidth.substr(iWidth.length-1,1)=='%')
iWidth=parseInt(window.screen.width*parseInt(iWidth,10)/100,10);var iHeight=editor.config.filebrowserWindowHeight;if(typeof iHeight=='string'&&iHeight.length>1&&iHeight.substr(iHeight.length-1,1)=='%')
iHeight=parseInt(window.screen.height*parseInt(iHeight,10)/100,10);if(iWidth<640)iWidth=640;if(iHeight<420)iHeight=420;var iTop=parseInt((window.screen.height-iHeight)/2,10);var iLeft=parseInt((window.screen.width-iWidth)/2,10);var WindowFeatures=editor.config.filebrowserWindowFeatures+',width='+iWidth+',height='+iHeight+',left='+iLeft+',top='+iTop;var popupWindow=window.open('','CKBrowseNoneDialog',WindowFeatures,true);if(!popupWindow)
return false;try
{popupWindow.moveTo(iLeft,iTop);popupWindow.resizeTo(iWidth,iHeight);popupWindow.focus();popupWindow.location.href=editor.config.filebrowserBrowseUrl;}
catch(e)
{popupWindow=window.open(editor.config.filebrowserBrowseUrl,'CKBrowseNoneDialog',WindowFeatures,true);}},canUndo:false});}});