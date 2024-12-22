/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.plugins.add('disabletoolbar');var cancelEvent=function(evt)
{evt.cancel();};var savedCommand=new Array();var savedButtonState=new Array();var savedComboState=new Array();CKEDITOR.editor.prototype.readOnlyToolbar=function(editorReadOnly,measure)
{this[editorReadOnly?'on':'removeListener']('key',cancelEvent,null,null,0);this[editorReadOnly?'on':'removeListener']('selectionChange',cancelEvent,null,null,0);var command;var commands=this.commands;for(var name in commands)
{if((measure&&name!='measuretool')||(!measure&&name!='savetemppage'&&name!='save'&&name!='closeeditor'&&name!='docProps'&&name!='insertscript'&&name!='measuretool'&&name!='sourcedialog'))
{command=commands[name];if(editorReadOnly)
{savedCommand[name]=command.state;command.disable();}
else
{command.setState(savedCommand[name]);}
this[editorReadOnly?'on':'removeListener']('state',cancelEvent,null,null,0);}}
var toolbars=this.toolbox.toolbars;for(var i=0;i<toolbars.length;i++)
{var toolbarItems=toolbars[i].items;for(var j=0;j<toolbarItems.length;j++)
{var button=toolbarItems[j].button;if(button&&((measure&&button.name!='measure')||(!measure&&button.name!='savetemp'&&button.name!='save'&&button.name!='closer'&&button.name!='docprops'&&button.name!='insertscript'&&button.name!='measure'&&button.name!='sourcedialog')))
{if(editorReadOnly&&(button.name=='textcolor'||button.name=='bgcolor')){savedButtonState[button.name]=button._.state;}
if(!editorReadOnly&&button.name!='textcolor'&&button.name!='bgcolor'){savedButtonState[button.name]=button._.state;}
button.setState(editorReadOnly?CKEDITOR.TRISTATE_DISABLED:savedButtonState[button.name]);}
var combo=toolbarItems[j].combo;if(combo)
{if(editorReadOnly){savedComboState[combo.name]=combo._.state;}
combo.setState(editorReadOnly?CKEDITOR.TRISTATE_DISABLED:savedComboState[combo.name]);}}}};})();