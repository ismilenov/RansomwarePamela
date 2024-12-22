/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.plugins.add('uioptselect',{init:function()
{CKEDITOR.document.appendStyleSheet(this.path+'optgroup.css');var initPrivateObject=function(elementDefinition){this._||(this._={});this._['default']=this._.initValue=elementDefinition['default']||'';this._.required=elementDefinition.required||false;var args=[this._];for(var i=1;i<arguments.length;i++)
args.push(arguments[i]);args.push(true);CKEDITOR.tools.extend.apply(CKEDITOR.tools,args);return this._;},commonBuilder={build:function(dialog,elementDefinition,output){return new CKEDITOR.ui.dialog[elementDefinition.type](dialog,elementDefinition,output);}},commonPrototype={isChanged:function(){return this.getValue()!=this.getInitValue();},reset:function(noChangeEvent){this.setValue(this.getInitValue(),noChangeEvent);},setInitValue:function(){this._.initValue=this.getValue();},resetInitValue:function(){this._.initValue=this._['default'];},getInitValue:function(){return this._.initValue;}},eventRegex=/^on([A-Z]\w+)/,cleanInnerDefinition=function(def){for(var i in def){if(eventRegex.test(i)||i=='title'||i=='type')
delete def[i];}
return def;};CKEDITOR.tools.extend(CKEDITOR.ui.dialog,{optselect:function(dialog,elementDefinition,htmlList){if(arguments.length<3)
return;var _=initPrivateObject.call(this,elementDefinition);if(elementDefinition.validate)
this.validate=elementDefinition.validate;_.inputId=CKEDITOR.tools.getNextId()+'_select';var innerHTML=function(){var myDefinition=CKEDITOR.tools.extend({},elementDefinition,{id:(elementDefinition.id?elementDefinition.id+'_select':CKEDITOR.tools.getNextId()+'_select')},true),html=[],innerHTML=[],attributes={'id':_.inputId,'class':'cke_dialog_ui_input_select','aria-labelledby':this._.labelId};html.push('<div class="cke_dialog_ui_input_select" role="presentation"');if(elementDefinition.width)
html.push('style="width:'+elementDefinition.width+'" ');html.push('>');if(elementDefinition.size!==undefined)
attributes.size=elementDefinition.size;if(elementDefinition.multiple!==undefined)
attributes.multiple=elementDefinition.multiple;cleanInnerDefinition(myDefinition);for(var i=0;i<elementDefinition.items.length;i++){var item=elementDefinition.items[i];if(typeof item[1]==='object'){innerHTML.push('<optgroup label="'+CKEDITOR.tools.htmlEncode(item[0])+'">');for(var k=0;k<item[1].length;k++){var opt=item[1][k];innerHTML.push('<option'+
CKEDITOR.tools.htmlEncode(opt[2]!==undefined?' '+opt[2]:'')+' value="'+CKEDITOR.tools.htmlEncode(opt[1]!==undefined?opt[1]:'').replace(/"/g,'&quot;')+'">'+
CKEDITOR.tools.htmlEncode(opt[0])+'</option>');}
innerHTML.push('</optgroup>');}
else{innerHTML.push('<option'+
CKEDITOR.tools.htmlEncode(item[2]!==undefined?' '+item[2]:'')+' value="'+CKEDITOR.tools.htmlEncode(item[1]!==undefined?item[1]:'').replace(/"/g,'&quot;')+'">'+
CKEDITOR.tools.htmlEncode(item[0])+'</option>');}}
if(typeof myDefinition.inputStyle!='undefined')
myDefinition.style=myDefinition.inputStyle;_.optselect=new CKEDITOR.ui.dialog.uiElement(dialog,myDefinition,html,'select',null,attributes,innerHTML.join(''));html.push('</div>');return html.join('');};CKEDITOR.ui.dialog.labeledElement.call(this,dialog,elementDefinition,htmlList,innerHTML);}});CKEDITOR.ui.dialog.optselect.prototype=CKEDITOR.tools.extend(new CKEDITOR.ui.dialog.labeledElement(),{getInputElement:function(){return this._.optselect.getElement();},add:function(label,value,index){var option=new CKEDITOR.dom.element('option',this.getDialog().getParentEditor().document),selectElement=this.getInputElement().$;option.$.text=label;option.$.value=(value===undefined||value===null)?label:value;if(index===undefined||index===null){if(CKEDITOR.env.ie){selectElement.add(option.$);}else{selectElement.add(option.$,null);}}else{selectElement.add(option.$,index);}
return this;},remove:function(index){var selectElement=this.getInputElement().$;selectElement.remove(index);return this;},clear:function(){var selectElement=this.getInputElement().$;while(selectElement.length>0)
selectElement.remove(0);return this;},keyboardFocusable:true},commonPrototype,true);CKEDITOR.dialog.addUIElement('optselect',commonBuilder);}});})();