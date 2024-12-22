/*
Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.md or http://ckeditor.com/license
*/
(function(){function parseClasses(aRules,skipSelectors,validSelectors){var s=aRules.join(' ');s=s.replace(/(,|>|\+|~)/g,' ');s=s.replace(/\[[^\]]*/g,'');s=s.replace(/#[^\s]*/g,'');s=s.replace(/\:{1,2}[^\s]*/g,'');s=s.replace(/\s+/g,' ');var aSelectors=s.split(' '),aClasses=[];for(var i=0;i<aSelectors.length;i++){var selector=aSelectors[i];if(validSelectors.test(selector)&&!skipSelectors.test(selector)){if(CKEDITOR.tools.indexOf(aClasses,selector)==-1)
aClasses.push(selector);}}
return aClasses;}
function contains(sheet,files){for(var i=0;i<files.length;i++){if(files[i]===sheet){return true;}}
return false;}
function LoadStylesCSS(theDoc,cssFiles,skipSelectors,validSelectors){var styles=[],aRules=[],i;for(i=0;i<theDoc.styleSheets.length;i++){var sheet=theDoc.styleSheets[i],node=sheet.ownerNode||sheet.owningElement;if(node.getAttribute('data-cke-temp'))
continue;if(sheet.href&&sheet.href.substr(0,9)=='chrome://')
continue;if(contains(sheet.href,cssFiles)){try{var sheetRules=sheet.cssRules||sheet.rules;for(var j=0;j<sheetRules.length;j++)
aRules.push(sheetRules[j].selectorText);}catch(e){}}}
var aClasses=parseClasses(aRules,skipSelectors,validSelectors);for(i=0;i<aClasses.length;i++){var oElement=aClasses[i].split('.'),element=oElement[0].toLowerCase(),sClassName=oElement[1];var styleElement=element.toUpperCase();var Class2Names=sClassName.replace('-',' ').replace('_',' ');var NameSplits=Class2Names.split(' ');var NameParts=new Array();for(k=0;k<NameSplits.length;k++){NameParts.push(NameSplits[k].charAt(0).toUpperCase()+NameSplits[k].slice(1).toLowerCase());}
var styleName=NameParts.join(' ');styles.push({name:styleElement+' '+styleName,element:element,attributes:{'class':sClassName}});}
return styles;}
CKEDITOR.plugins.add('stylesheetparser',{init:function(editor){editor.filter.disable();var cachedDefinitions;editor.once('stylesSet',function(evt){evt.cancel();editor.once('contentDom',function(){editor.getStylesSet(function(definitions){var skipSelectors=editor.config.stylesheetParser_skipSelectors||(/(^body\.|^\.)/i),validSelectors=editor.config.stylesheetParser_validSelectors||(/\w+\.\w+/);var cssFiles=editor.config.contentsCss;cachedDefinitions=definitions.concat(LoadStylesCSS(editor.document.$,cssFiles,skipSelectors,validSelectors));editor.getStylesSet=function(callback){if(cachedDefinitions)
return callback(cachedDefinitions);};editor.fire('stylesSet',{styles:cachedDefinitions});});});},null,null,1);}});})();