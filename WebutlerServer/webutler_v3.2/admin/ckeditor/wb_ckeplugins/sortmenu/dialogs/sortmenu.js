/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('sortmenu',function(editor)
{if(!String.prototype.trim){String.prototype.trim=function(){return this.replace(/^\s*|\s*$/g,"");};}
function sourceview(source){var textlength=30;source=decodeURIComponent(source);source=source.replace(/<\?/,'&lt;?').replace(/\?>/,'?&gt;');source=source.trim();var dots=source.length>textlength?'...':'';source=source.substring(0,textlength)+dots;return source;}
function loadTreeMenu(list,ulid,container){for(var i=0;i<list.childNodes.length;i++){var text='';var newDiv=document.createElement('div');var divContent=document.getElementById(container).appendChild(newDiv);var childNode=list.childNodes[i];if(childNode.nodeType==8){divContent.setAttribute('class','code');var value=childNode.nodeValue;if(value.match(/cke_protected/))value=value.substring(15);divContent.setAttribute('savedtags',value.trim());value=sourceview(value);divContent.appendChild(document.createTextNode(value));}
if(childNode.nodeType==1&&childNode.nodeName=='LI'){var docorfolder;var subLists=new Array();var subListID='';var uDiv=document.createElement('div');var uCont=document.getElementById('treemenu_subDiv').appendChild(uDiv);var elms=childNode.cloneNode(true);var count=0;if(elms.getElementsByTagName('ul').length>0){docorfolder='folder';for(var k=0;k<elms.getElementsByTagName('ul').length;k++){var ulNode=elms.getElementsByTagName('ul')[k];var parentOK=true;while(ulNode){if(ulNode&&ulNode.parentNode){if(ulNode.parentNode.nodeName=='UL'){parentOK=false;break;}}
else
break;ulNode=ulNode.parentNode;}
if(parentOK){ulid=parseInt(ulid+1);subListID='savedUL_'+ulid+'_D';divContent.id=subListID;subLists[count]={'ulid':ulid,'listid':subListID,'source':elms.getElementsByTagName('ul')[k].cloneNode(true)};elms.getElementsByTagName('ul')[k].innerHTML='';count++;}}}
else{docorfolder='doc';}
var html=elms.outerHTML.replace(/\>\s+\</g,'><');divContent.setAttribute('class',docorfolder);divContent.setAttribute('savedtags',escape(html));document.getElementById('treemenu_subDiv').innerHTML='';if(childNode.hasChildNodes()){text=getTextNode(childNode);}
if(text==''||text=='&nbsp;'){text='- - '+editor.lang.sortmenu.empty+' - -';}
divContent.innerHTML=text;if(docorfolder=='folder'&&subLists.length>0){for(var m=0;m<subLists.length;m++){var _source='';var _ulid='';var _listid='';for(var key in subLists[m]){if(key=='source')_source=subLists[m][key];if(key=='ulid')_ulid=subLists[m][key];if(key=='listid')_listid=subLists[m][key];}
loadTreeMenu(_source,_ulid,_listid);}}}}};function getTextNode(node){var res='';if(node.nodeType==3){res+=node.nodeValue.trim();}
if(node.hasChildNodes()){for(var i=0;i<node.childNodes.length;i++){newnode=node.childNodes[i];if(newnode.nodeName!='LI'){res+=getTextNode(newnode);}}}
return res;};function loadTreeElements(loadMenu)
{if(CKEDITOR.env.ie){ckeMenu=loadMenu.getOuterHtml().replace(/href=["']([^"']*)["']/g,'href="dynamictree://dynamictree/$1"');}
else{ckeMenu=loadMenu.getOuterHtml();}
document.getElementById('treemenu_savedMenu').innerHTML=ckeMenu;orgMenu=document.getElementById('treemenu_savedMenu');if(orgMenu.getElementsByTagName('ul')[0])
loadTreeMenu(orgMenu.getElementsByTagName('ul')[0],0,'treemenu_newMenu');else
return;var divarray=document.getElementById('treemenu_newMenu').getElementsByTagName('div');for(var d=0;d<divarray.length;d++){if(divarray[d].id!=''){divarray[d].removeAttribute('id');}}}
function reset_treemenu()
{document.getElementById('treemenu_savedMenu').innerHTML='';document.getElementById('treemenu_newMenu').innerHTML='';document.getElementById('treemenubuilder').innerHTML='';treemenubuilder=null;}
var loadMenu;var ckeMenu;var orgMenu;var imgpath=CKEDITOR.plugins.getPath('sortmenu')+'images/';confirmRemove=editor.lang.sortmenu.remove;function restoreSource(source){var elements=source.getElementsByTag('img');for(var i=elements.$.length-1;i>=0;i--)
{var element=elements.getItem(i);if(element.$.getAttribute('class')&&element.$.getAttribute('class')=='cke_protected')
{var commentdata=decodeURIComponent(element.$.getAttribute('data-cke-realelement'));var comment=document.createComment(commentdata.substring(4,commentdata.length-3));element.$.parentNode.replaceChild(comment,element.$);}}
return source;}
return{title:editor.lang.sortmenu.title,minWidth:300,minHeight:300,onShow:function()
{loadMenu='';orgMenu='';treemenubuilder=new DynamicTreeBuilder('treemenubuilder',imgpath);treemenubuilder.init();DynamicTreePlugins.call(treemenubuilder);var startelement=editor.getSelection().getStartElement();var rootlist=startelement.getParents();for(var i=0;i<rootlist.length;i++){if(rootlist[i].getName()=='ul'){loadMenu=rootlist[i].clone(true,true);loadMenu=restoreSource(loadMenu);editor.getSelection().fake(rootlist[i]);break;}}
if(loadMenu!=''){loadTreeElements(loadMenu);treemenubuilder.importFromHtml(document.getElementById('treemenu_newMenu').innerHTML);}},onCancel:function()
{reset_treemenu();},onOk:function()
{orgMenu.getElementsByTagName('ul')[0].innerHTML=treemenubuilder.exportToHtml();if(CKEDITOR.env.ie){var aTags=orgMenu.getElementsByTagName('a');for(var i=0;i<aTags.length;i++){var str=aTags[i].parentNode.innerHTML.match(/href=["'](dynamictree:\/\/dynamictree\/)?([^"']*)["']/i);if(str)
aTags[i].href=str[2];}}
var newMenu=orgMenu.innerHTML.replace(/<!--\?/gi,'<?').replace(/\?-->/gi,'?>');editor.insertHtml(newMenu);reset_treemenu();},contents:[{id:'tab1',label:'',title:'',elements:[{type:'html',html:loadTreeHTML(imgpath,editor)}]}]}});