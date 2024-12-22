/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
(function()
{CKEDITOR.dtd['cke:form']=CKEDITOR.dtd.form;CKEDITOR.dtd.body['cke:form']=1;CKEDITOR.dtd.$block['cke:form']=1;CKEDITOR.dtd.$blockLimit['cke:form']=1;CKEDITOR.dtd.$editable['cke:form']=1;CKEDITOR.dtd.$inline['label']=1;CKEDITOR.dtd.$removeEmpty['label']=1;CKEDITOR.plugins.add('formchanges',{lang:[CKEDITOR.lang.detect(CKEDITOR.config.language)],beforeInit:function(editor){var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;dataFilter.addRules({elements:{form:function(element){if(!element.attributes['data-cke-real-element-type']){element.attributes['data-cke-real-element-type']='form';}
element.name='cke:form';return element;}}});},init:function(editor)
{CKEDITOR.addCss('.cke_editable [data-cke-real-element-type="form"] {'+'outline: 1px dotted #FF0000;'+'padding: 1px;'+'margin: 2px;'+'display: block;'+'}'+'.cke_fake_radio, .cke_fake_radio_checked, .cke_fake_checkbox, .cke_fake_checkbox_checked {'+'    background-position: center center;'+'    background-repeat: no-repeat;'+'    width: 16px;'+'    height: 16px;'+'}'+'.cke_fake_radio {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/radio_off.png')+');'+'}'+'.cke_fake_radio_checked {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/radio_on.png')+');'+'}'+'.cke_fake_checkbox {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/checkbox_off.png')+');'+'}'+'.cke_fake_checkbox_checked {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/checkbox_on.png')+');'+'}'+'.cke_fake_textfield, .cke_fake_select, .cke_fake_selects, .cke_fake_textarea {'+'    background-color: #fff;'+'    background-repeat: no-repeat;'+'    outline: 1px solid #787878;'+'}'+'.cke_fake_textfield {'+'    width: 200px;'+'    height: 18px;'+'}'+'.cke_fake_select {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/select.png')+');'+'    background-position: center right;'+'    width: 200px;'+'    height: 18px;'+'}'+'.cke_fake_selects {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/selects.png')+');'+'    background-position: center right;'+'    width: 200px;'+'    height: 54px;'+'}'+'.cke_fake_textarea {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/textarea.png')+');'+'    background-position: bottom right;'+'    width: 200px;'+'    height: 72px;'+'}'+'.cke_fake_button {'+'    background-image: url('+CKEDITOR.getUrl(this.path+'images/button.png')+');'+'    background-color: #ddd;'+'    background-position: center center;'+'    background-repeat: no-repeat;'+'    width: 96px;'+'    height: 20px;'+'    margin: 0px 2px;'+'    outline: 1px solid #787878;'+'}');editor.on('doubleclick',function(evt)
{var element=evt.data.element;if(element.hasClass('cke_fake_radio')||element.hasClass('cke_fake_radio_checked')){evt.data.dialog='radio';}
else if(element.hasClass('cke_fake_checkbox')||element.hasClass('cke_fake_checkbox_checked')){evt.data.dialog='checkbox';}
else if(element.hasClass('cke_fake_select')||element.hasClass('cke_fake_selects')){evt.data.dialog='select';}
else if(element.hasClass('cke_fake_button')){evt.data.dialog='button';}
else if(element.hasClass('cke_fake_textfield')){evt.data.dialog='textfield';}
else if(element.hasClass('cke_fake_textarea')){evt.data.dialog='textarea';}
else{return null;}});if(editor.contextMenu)
{editor.contextMenu.addListener(function(element,selection,path)
{var isForm=path.contains('cke:form',1);if(isForm&&element)
{if(element.getName()!='input'&&element.getName()!='textarea'&&element.getName()!='button'&&element.getName()!='select'&&!element.data('cke-realelement')){return{form:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='radio'&&(element.hasClass('cke_fake_radio')||element.hasClass('cke_fake_radio_checked'))){return{radio:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='checkbox'&&(element.hasClass('cke_fake_checkbox')||element.hasClass('cke_fake_checkbox_checked'))){return{checkbox:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='select'&&(element.hasClass('cke_fake_select')||element.hasClass('cke_fake_selects'))){return{select:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='button'&&element.hasClass('cke_fake_button')){return{button:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='textfield'&&element.hasClass('cke_fake_textfield')){return{textfield:CKEDITOR.TRISTATE_OFF};}
else if(element.data('cke-real-element-type')=='textarea'&&element.hasClass('cke_fake_textarea')){return{textarea:CKEDITOR.TRISTATE_OFF};}
else{return null;}}});}},afterInit:function(editor)
{var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter;if(dataFilter)
{dataFilter.addRules({elements:{input:function(element)
{var fakeElement;var attributes=element.attributes;switch(attributes.type)
{case'radio':if(attributes.checked)
fakeElement=editor.createFakeParserElement(element,'cke_fake_radio_checked','radio',false);else
fakeElement=editor.createFakeParserElement(element,'cke_fake_radio','radio',false);break;case'checkbox':if(attributes.checked)
fakeElement=editor.createFakeParserElement(element,'cke_fake_checkbox_checked','checkbox',false);else
fakeElement=editor.createFakeParserElement(element,'cke_fake_checkbox','checkbox',false);break;case'text':case'email':case'password':case'search':case'tel':case'url':fakeElement=editor.createFakeParserElement(element,'cke_fake_textfield','textfield',false);break;case'submit':case'reset':case'button':fakeElement=editor.createFakeParserElement(element,'cke_fake_button','button',false);break;}
return fakeElement;},select:function(element)
{var fakeElement;var attributes=element.attributes;if(attributes.size>=2)
fakeElement=editor.createFakeParserElement(element,'cke_fake_selects','select',false);else
fakeElement=editor.createFakeParserElement(element,'cke_fake_select','select',false);return fakeElement;},textarea:function(element)
{var fakeElement;fakeElement=editor.createFakeParserElement(element,'cke_fake_textarea','textarea',false);return fakeElement;}}});}},requires:['fakeobjects']});CKEDITOR.on('dialogDefinition',function(ev)
{var dialogName=ev.data.name;var dialogDefinition=ev.data.definition;var editor=ev.editor;function checkAttrs(oldelm,element){if(oldelm.hasAttribute('id')){var value=oldelm.getAttribute('id');element.setAttribute('id',value);}
if(oldelm.hasAttribute('class')){var value=oldelm.getAttribute('class');element.setAttribute('class',value);}
if(oldelm.hasAttribute('style')){var value=oldelm.getAttribute('style');element.setAttribute('style',value);}
return element;}
if(dialogName==='form')
{dialogDefinition.onLoad=function()
{var dialog=CKEDITOR.dialog.getCurrent();var autoAttributes={action:1,id:1,method:1,enctype:1,target:1};function autoSetup(element){this.setValue(element.getAttribute(this.id)||'');}
function autoCommit(element){if(this.getValue())
element.setAttribute(this.id,this.getValue());else
element.removeAttribute(this.id);}
this.foreach(function(contentObj){if(autoAttributes[contentObj.id]){contentObj.setup=autoSetup;contentObj.commit=autoCommit;}});dialog.on('show',function()
{delete this.form;var path=dialog.getParentEditor().elementPath(),form=path.contains('cke:form',1);if(form){this.form=form;this.setupContent(form);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var editor,element=this.form,isInsertMode=!element;if(isInsertMode){editor=dialog.getParentEditor();element=editor.document.createElement('cke:form');element.setAttributes({'data-cke-real-element-type':'form'});element.appendBogus();}
if(isInsertMode)
editor.insertElement(element);dialog.commitContent(element);return true;};}
if(dialogName==='radio')
{var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'vbox',children:[{type:'text',id:'label',label:editor.lang.formchanges.label,'default':''}]},'checked');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''},{type:'checkbox',id:'class2label',label:editor.lang.formchanges.class2label,'default':'checked',value:'checked'}]});dialogDefinition.onLoad=function()
{var dialog=CKEDITOR.dialog.getCurrent();dialog.on('show',function()
{delete this.radioField;delete this.orgRadioField;var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='radio')
{this.radioField=element;element=editor.restoreRealElement(this.radioField);this.orgRadioField=element;var classes=element.getAttribute('class');if(classes!==''&&classes!==null&&classes!==undefined)
this.setValueOf('info','class',classes);var parent=selection.getCommonAncestor();var labelClasses='';if(parent.getName()=='label'){var labelVal=parent.getText();if(labelVal!==undefined)
this.setValueOf('info','label',labelVal);labelClasses=parent.getAttribute('class');if(labelClasses!==''&&labelClasses!==null&&labelClasses!==undefined)
this.setValueOf('info','class',labelClasses);}
this.setValueOf('info','class2label',(labelClasses!=''&&labelClasses!=null&&labelClasses!==undefined?'checked':''));this.setupContent(element);selection.selectElement(this.radioField);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var element=this.radioField,isInsertMode=!element,isChecked=(dialog.getValueOf('info','checked')==true)?true:false;element=editor.document.createElement('input');element.setAttribute('type','radio');if(!isInsertMode)
element=checkAttrs(this.orgRadioField,element);var fakeType;if(!isChecked)
{fakeType='cke_fake_radio';element.removeAttribute('checked');}
else
{fakeType='cke_fake_radio_checked';element.setAttribute('checked','checked');}
var label=dialog.getValueOf('info','label');var classes=dialog.getValueOf('info','class');var labelClass='';if(classes!=''){if(label!=''&&dialog.getValueOf('info','class2label')==true){labelClass=' class="'+classes+'"';element.removeAttribute('class');}
else
element.setAttribute('class',classes);}
var labelTag=CKEDITOR.dom.element.createFromHtml('<label'+labelClass+'>'+label+'</label>');dialog.commitContent({element:element});var fakeElement=editor.createFakeElement(element,fakeType,'radio',false);if(isInsertMode)
{if(label!=''){editor.insertElement(labelTag);labelTag.append(fakeElement,true);}
else{editor.insertElement(fakeElement);}}
else
{var selection=editor.getSelection();var parent=selection.getCommonAncestor();if(label!=''){if(parent.getName()=='label')
labelTag.replace(parent);else
labelTag.replace(this.radioField);labelTag.append(fakeElement,true);}
else{if(parent.getName()=='label')
fakeElement.replace(parent);else
fakeElement.replace(this.radioField);}
selection.selectElement(fakeElement);}
return true;};}
if(dialogName==='checkbox')
{var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'vbox',children:[{type:'text',id:'label',label:editor.lang.formchanges.label,'default':''}]},'cmbSelected');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''},{type:'checkbox',id:'class2label',label:editor.lang.formchanges.class2label,'default':'checked',value:'checked'}]});dialogDefinition.onLoad=function()
{var dialog=CKEDITOR.dialog.getCurrent();dialog.on('show',function()
{delete this.checkboxField;delete this.orgCheckboxField;var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='checkbox')
{this.checkboxField=element;element=editor.restoreRealElement(this.checkboxField);this.orgCheckboxField=element;var classes=element.getAttribute('class');if(classes!=''&&classes!=null&&classes!==undefined)
this.setValueOf('info','class',classes);var parent=selection.getCommonAncestor();var labelClasses='';if(parent.getName()=='label'){var labelVal=parent.getText();if(labelVal!==undefined)
this.setValueOf('info','label',labelVal);labelClasses=parent.getAttribute('class');if(labelClasses!=''&&labelClasses!=null&&labelClasses!==undefined)
this.setValueOf('info','class',labelClasses);}
this.setValueOf('info','class2label',(labelClasses!=''&&labelClasses!=null&&labelClasses!==undefined?'checked':''));this.setupContent(element);selection.selectElement(this.checkboxField);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var element=this.checkboxField,isInsertMode=!element,isChecked=(dialog.getValueOf('info','cmbSelected')==true)?true:false;element=editor.document.createElement('input');element.setAttribute('type','checkbox');if(!isInsertMode)
element=checkAttrs(this.orgCheckboxField,element);var fakeType;if(!isChecked)
{fakeType='cke_fake_checkbox';element.removeAttribute('checked');}
else
{fakeType='cke_fake_checkbox_checked';element.setAttribute('checked','checked');}
var label=dialog.getValueOf('info','label');var classes=dialog.getValueOf('info','class');var labelClass='';if(classes!=''){if(label!=''&&dialog.getValueOf('info','class2label')==true){labelClass=' class="'+classes+'"';element.removeAttribute('class');}
else
element.setAttribute('class',classes);}
var labelTag=CKEDITOR.dom.element.createFromHtml('<label'+labelClass+'>'+label+'</label>');this.commitContent({element:element});var fakeElement=editor.createFakeElement(element,fakeType,'checkbox',false);if(isInsertMode)
{if(label!=''){editor.insertElement(labelTag);labelTag.append(fakeElement,true);}
else{editor.insertElement(fakeElement);}}
else
{var selection=editor.getSelection();var parent=selection.getCommonAncestor();if(label!=''){if(parent.getName()=='label')
labelTag.replace(parent);else
labelTag.replace(this.checkboxField);labelTag.append(fakeElement,true);}
else{if(parent.getName()=='label')
fakeElement.replace(parent);else
fakeElement.replace(this.checkboxField);}
selection.selectElement(fakeElement);}
return true;};}
if(dialogName==='select')
{function getOptions(combo)
{combo=getSelect(combo);return combo?combo.getChildren():false;}
function getSelect(obj)
{if(obj&&obj.domId&&obj.getInputElement().$)
return obj.getInputElement();else if(obj&&obj.$)
return obj;return false;}
var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''}]});dialogDefinition.onShow=function()
{delete this.selectBox;delete this.orgSelectBox;this.setupContent('clear');var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='select')
{this.selectBox=element;element=editor.restoreRealElement(this.selectBox);this.orgSelectBox=element;var classes=element.getAttribute('class');if(classes!=='')
this.setValueOf('info','class',classes);this.setupContent('select',element);var objOptions=getOptions(element);for(var i=0;i<objOptions.count();i++)
this.setupContent('option',objOptions.getItem(i));selection.selectElement(this.selectBox);}};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var element=this.selectBox,isInsertMode=!element;element=editor.document.createElement('select');if(!isInsertMode)
element=checkAttrs(this.orgSelectBox,element);var classes=dialog.getValueOf('info','class');if(classes!=='')
element.setAttribute('class',classes);this.commitContent(element);var fakeSelect;if(element.getAttribute('size')>=2)
{fakeSelect='cke_fake_selects';}
else
{fakeSelect='cke_fake_select';}
var fakeElement=editor.createFakeElement(element,fakeSelect,'select',false);if(isInsertMode)
{editor.insertElement(fakeElement);}
else
{fakeElement.replace(this.selectBox);editor.getSelection().selectElement(fakeElement);}};}
if(dialogName=='textfield')
{function autoCommit(data){var element=data.element;var value=this.getValue();value?element.setAttribute(this.id,value):element.removeAttribute(this.id);}
function autoSetup(element){var value=element.hasAttribute(this.id)&&element.getAttribute(this.id);this.setValue(value||'');}
var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'hbox',widths:['100%'],children:[{type:'text',id:'placeholder',label:editor.lang.formchanges.placeholder,'default':''}]},'required');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''}]});dialogDefinition.onLoad=function()
{this.foreach(function(contentObj){if(contentObj.getValue){if(!contentObj.setup)
contentObj.setup=autoSetup;if(!contentObj.commit)
contentObj.commit=autoCommit;}});var dialog=CKEDITOR.dialog.getCurrent();dialog.on('show',function()
{delete this.inputtextField;delete this.orgInputtextField;var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='textfield')
{this.inputtextField=element;element=editor.restoreRealElement(this.inputtextField);this.orgInputtextField=element;var classes=element.getAttribute('class');if(classes!==''&&classes!==null&&classes!==undefined)
this.setValueOf('info','class',classes);var placeholder=element.getAttribute('placeholder');if(placeholder!==''&&placeholder!==null&&placeholder!==undefined)
this.setValueOf('info','placeholder',placeholder);this.setupContent(element);selection.selectElement(this.inputtextField);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var editor=this.getParentEditor();var element=this.textField?CKEDITOR.dom.element.createFromHtml(decodeURIComponent(this.textField.data('cke-realelement')),editor.document):false;var isInsertMode=!element;if(isInsertMode){element=editor.document.createElement('input');element.setAttribute('type','text');}
var classes=dialog.getValueOf('info','class');if(classes!='')
element.setAttribute('class',classes);var placeholder=dialog.getValueOf('info','placeholder');if(placeholder!='')
element.setAttribute('placeholder',placeholder);var data={element:element};this.commitContent(data);var fakeElement=editor.createFakeElement(data.element,'cke_fake_textfield','textfield',false);if(!isInsertMode){fakeElement.replace(this.textField);}
else
editor.insertElement(fakeElement);return true;};}
if(dialogName=='textarea')
{var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'hbox',widths:['100%'],children:[{type:'text',id:'placeholder',label:editor.lang.formchanges.placeholder,'default':''}]},'required');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''}]});dialogDefinition.onLoad=function()
{var dialog=CKEDITOR.dialog.getCurrent();dialog.on('show',function()
{delete this.textareaField;delete this.orgTextareaField;var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='textarea')
{this.textareaField=element;element=editor.restoreRealElement(this.textareaField);this.orgTextareaField=element;var classes=element.getAttribute('class');if(classes!==''&&classes!==null&&classes!==undefined)
this.setValueOf('info','class',classes);var placeholder=element.getAttribute('placeholder');if(placeholder!==''&&placeholder!==null&&placeholder!==undefined)
this.setValueOf('info','placeholder',placeholder);this.setupContent(element);selection.selectElement(this.textareaField);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var element=this.textarea,isInsertMode=!element;element=editor.document.createElement('textarea');if(!isInsertMode)
element=checkAttrs(this.orgTextareaField,element);var classes=dialog.getValueOf('info','class');if(classes!='')
element.setAttribute('class',classes);var placeholder=dialog.getValueOf('info','placeholder');if(placeholder!='')
element.setAttribute('placeholder',placeholder);this.commitContent(element);var fakeElement=editor.createFakeElement(element,'cke_fake_textarea','textarea',false);if(isInsertMode)
{editor.insertElement(fakeElement);}
else
{fakeElement.replace(this.textareaField);editor.getSelection().selectElement(fakeElement);}};}
if(dialogName=='button')
{function commitAttrs(data){var element=data.element;var val=this.getValue();var id=this.id;if(val){if(id=='name')
element.data('cke-saved-name',val);else
element.setAttribute(id,val);}
else{if(id=='name')
element.data('cke-saved-name',false);element.removeAttribute(id);}}
var info=dialogDefinition.getContents('info');var nameField=info.get('name');var nameCommit=nameField.commit;var valueField=info.get('value');var valueCommit=valueField.commit;var typeField=info.get('type');var typeCommit=typeField.commit;nameField.commit=CKEDITOR.tools.override(nameField.commit,function(nameCommit){return commitAttrs;});valueField.commit=CKEDITOR.tools.override(valueField.commit,function(valueCommit){return commitAttrs;});typeField.commit=CKEDITOR.tools.override(typeField.commit,function(typeCommit){return commitAttrs;});var infoTab=dialogDefinition.getContents('info');infoTab.add({type:'vbox',children:[{type:'text',id:'class',label:editor.lang.formchanges.cssClass,'default':''}]});dialogDefinition.onLoad=function()
{var dialog=CKEDITOR.dialog.getCurrent();dialog.on('show',function()
{delete this.inputbuttonField;delete this.orgInputbuttonField;var selection=editor.getSelection(),element=selection.getSelectedElement();if(element&&element.data('cke-real-element-type')&&element.data('cke-real-element-type')=='button')
{this.inputbuttonField=element;element=editor.restoreRealElement(this.inputbuttonField);this.orgInputbuttonField=element;var classes=element.getAttribute('class');if(classes!=='')
this.setValueOf('info','class',classes);this.setupContent(element);selection.selectElement(this.inputbuttonField);}});};dialogDefinition.onOk=function()
{var dialog=CKEDITOR.dialog.getCurrent();var element=this.inputbuttonField,isInsertMode=!element;element=editor.document.createElement('input');element.setAttribute('type','submit');if(!isInsertMode)
element=checkAttrs(this.orgInputbuttonField,element);var classes=dialog.getValueOf('info','class');if(classes!=='')
element.setAttribute('class',classes);var data={element:element};this.commitContent(data);var fakeElement=editor.createFakeElement(data.element,'cke_fake_button','button',false);if(isInsertMode)
{editor.insertElement(fakeElement);}
else
{fakeElement.replace(this.inputbuttonField);editor.getSelection().selectElement(fakeElement);}
return true;};}});})();