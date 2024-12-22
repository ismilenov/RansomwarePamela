/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('insertscript',function(editor)
{return{onOk:function()
{var eScripts=document.getElementById('wbcke_jsinsert_list').getElementsByTagName('span');var oScripts=new Array();var oEditor;var eHead;for(var i=0;i<eScripts.length;i++){var isActive=eScripts[i].getAttribute('active');if(isActive=='yes'){var path=eScripts[i].getAttribute('path');oScripts.push(path);}}
if(oScripts.length>0){if(editor.name=='metas'){oEditor=CKEDITOR.instances.metas;}
else if(editor.name=='fulleditor'){oEditor=CKEDITOR.instances.fulleditor;}
eHead=oEditor.document.getHead();for(var j=0;j<oScripts.length;j++){if(oScripts[j]&&oScripts[j]!=''){var ins_script=CKEDITOR.document.createElement('script',{attributes:{'data-cke-saved-src':oScripts[j]}});eHead.append(ins_script);}}}},title:editor.lang.insertscript.insertjs,minWidth:295,minHeight:300,contents:[{id:'tabscript',label:'',title:'',elements:[{type:'html',html:editor.lang.insertscript.choose},{type:'html',html:'<div id="wbcke_jsinsert_list"></div>',onShow:function()
{var listdiv=document.getElementById('wbcke_jsinsert_list');var list=document.createElement('div');list.id='wbcke_jsinsert_scripts';list.innerHTML=loadjs_folders()+loadjs_scripts();listdiv.appendChild(list);},onHide:function()
{var list=document.getElementById('wbcke_jsinsert_scripts');document.getElementById('wbcke_jsinsert_list').removeChild(list);}}]}]}});