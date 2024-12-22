// +---------------------------------------------------------------+
// | DO NOT REMOVE THIS                                            |
// +---------------------------------------------------------------+
// | DynamicTree 1.5.0                                             |
// | Author: Cezary Tomczak [www.gosu.pl]                          |
// | Free for any use as long as all copyright messages are intact |
// +---------------------------------------------------------------+
/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
function DynamicTreePlugins(){this.importFromHtml=function(html){html=html.replace(/href=["']([^"']*)["']/g,'href="dynamictree://dynamictree/$1"');document.getElementById(this.id).innerHTML=html;this.reset();};this.exportToHtml=function(node){var ret='';if(node){var retags=node.savedtags;if(node.isCode){ret+='<!--{cke_protected}'+encodeURIComponent(unescape(retags))+'-->';}
if(node.isDoc){ret+=unescape(retags);}
if(node.isFolder){var nextDiv=document.createElement('div');var divCont=document.getElementById('treemenu_subDiv').appendChild(nextDiv);divCont.innerHTML=unescape(retags);var inhalt='';for(var i=0;i<node.childNodes.length;++i){inhalt+=this.exportToHtml(node.childNodes[i]);}
divCont.getElementsByTagName('ul')[0].innerHTML=inhalt;ret+=divCont.innerHTML;}}else{var nodes=this.treemenubuilder.childNodes;for(var i=0;i<nodes.length;++i){ret+=this.exportToHtml(nodes[i]);}}
return ret;};this.treePluginLink=function(node){var node=this.allNodes[this.active];var ret='';if(node&&(node.isCode||node.isDoc||node.isFolder)){ret+='??'.format(''.repeat(1*(node.getLevel()-1)),node.text)}else{var active=this.treemenubuilder.childNodes;ret+=this.treePluginEdit(active);}
return ret;};if(!String.prototype.repeat){String.prototype.repeat=function(n){var ret='';for(var i=0;i<n;++i){ret+=this;}
return ret;}}}
function treeMoveUp(){if(document.getElementById('tree-insert').style.display!='block'&&treemenubuilder.mayMoveUp()){treemenubuilder.moveUp();}}
function treeMoveDown(){if(document.getElementById('tree-insert').style.display!='block'&&treemenubuilder.mayMoveDown()){treemenubuilder.moveDown();}}
function treeMoveLeft(){if(document.getElementById('tree-insert').style.display!='block'&&treemenubuilder.mayMoveLeft()){treemenubuilder.moveLeft();}}
function treeMoveRight(){if(document.getElementById('tree-insert').style.display!='block'&&treemenubuilder.mayMoveRight()){treemenubuilder.moveRight();}}
function treeInsert(){document.getElementById('tree-insert').style.display='block';}
function setPageToURL(){if(document.getElementById('tree-select-page').value!='')
document.getElementById('tree-insert-href').value=document.getElementById('tree-select-page').value;}
function treeInsertExecute(alerttxt){var name=document.getElementById('tree-insert-name');var href=document.getElementById('tree-insert-href');name.value=name.value.trim();href.value=href.value.trim();if(!name.value||!href.value){alert(decodeURIComponent(alerttxt));return false;}
var savedtags=escape('<li><a href="'+href.value+'">'+name.value+'</a></li>');treemenubuilder.insert('treemenubuilder-'+(++treemenubuilder.count),savedtags,name.value,'doc');name.value='';href.value='';this.blur();treeCancelNew();}
function treeCancelNew(){document.getElementById('tree-insert').style.display='none';document.getElementById('tree-insert-name').value='';document.getElementById('tree-insert-href').value='';document.getElementById('tree-select-page').options[0].selected='selected';}
function treeRemove(){if(document.getElementById('tree-insert').style.display!='block'&&treemenubuilder.mayRemove()){if(confirm(confirmRemove)){treemenubuilder.remove();}}}