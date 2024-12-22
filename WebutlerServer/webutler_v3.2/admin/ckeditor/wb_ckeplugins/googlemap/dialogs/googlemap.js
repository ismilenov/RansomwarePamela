/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/
CKEDITOR.dialog.add('googlemap',function(editor)
{function getStyle(Style,Name)
{var result='';if(Style){var Styles=Style.split(';');for(var i=0;i<Styles.length;i++)
{var styleAttr=Styles[i].split(':');if(CKEDITOR.tools.trim(styleAttr[0]).toLowerCase()==Name)
{result=CKEDITOR.tools.trim(styleAttr[1]);if(result.substr(result.length-1,1)!='%')
result=parseInt(result).toString();break;}}}
return result?result:'';}
function defaultMapVar(realelem,suche)
{var term='var '+suche+' = ';var start=realelem.indexOf(term);var sel=realelem.substr(start+term.length,realelem.length);var value=sel.substr(0,sel.indexOf(';'));value.replace(/\'/g,"");return CKEDITOR.tools.trim(value);}
return{title:editor.lang.googlemap.wintitle,minWidth:450,minHeight:350,onOk:function()
{var lat=iframeWindow.lat;var lng=iframeWindow.lng;var zoom=iframeWindow.map.getZoom();var marker=this.getValueOf('info','marker')==true?'yes':'no';var width=this.getValueOf('info','mapWidth');var height=this.getValueOf('info','mapHeight');var classes=this.getValueOf('info','cssClass');var mapid=this.getValueOf('info','mapId');var infowintext=document.getElementById('mapinfowin_'+editor.name).getElementsByTagName('textarea')[0].value;infowintext=infowintext.replace(/\r/g,'').replace(/\n/g,' ');var sizes=new Array();if(width.length>0){var styleWidth='width: '+(width.substr(width.length-1,1)=='%'?width:parseInt(width)+'px');sizes.push(styleWidth);}
if(height.length>0){var styleHeight='height: '+(height.substr(height.length-1,1)=='%'?height:parseInt(height)+'px');sizes.push(styleHeight);}
var id=mapid.length>0?' id="'+mapid+'"':'';var mapclass=classes.length>0?' class="'+classes+'"':'';var styles=width.length>0||height.length>0?' style="'+sizes.join('; ')+'"':'';var mapHtml='<div'+id+mapclass+styles+' data-source="googlemapsframe">\n'+'<scr'+'ipt>\n'+'  /*<![CDATA[*/\n'+'    document.writeln(\'<scr\' + \'ipt src="http://maps.google.com/maps/api/js?sensor=false"></scr\' + \'ipt>\' +\n'+'    \'    <scr\' + \'ipt>\' +\n'+'    \'    /*<![CDATA[*/\' +\n'+'    \'    (function() {\' +\n'+'    \'        if (window.addEventListener)\' +\n'+'    \'            window.addEventListener(\\\'load\\\', initgooglemap, false);\' +\n'+'    \'        else\' +\n'+'    \'            window.attachEvent(\\\'onload\\\', initgooglemap);\' +\n'+'    \'    })();\' +\n'+'    \'    function initgooglemap() {\' +\n'+'    \'        var mapZoom = '+zoom+';\' +\n'+'    \'        var mapLat = '+lat+';\' +\n'+'    \'        var mapLng = '+lng+';\' +\n'+'    \'        var mapMarker = \\\''+marker+'\\\';\' +\n'+'    \'        var mapInfo = \\\''+infowintext+'\\\';\' +\n'+'    \'        var latlng = new google.maps.LatLng(mapLat,mapLng);\' +\n'+'    \'        var myOptions = {\' +\n'+'    \'            zoom: mapZoom,\' +\n'+'    \'            center: latlng,\' +\n'+'    \'            mapTypeId: google.maps.MapTypeId.ROADMAP\' +\n'+'    \'        };\' +\n'+'    \'        var map = new google.maps.Map(document.getElementById(\\\'googlemapcanvas\\\'), myOptions);\' +\n';if(marker=='yes'){mapHtml+='    \'        var marker = new google.maps.Marker({\' +\n'+'    \'            position: latlng,\' +\n'+'    \'            map: map\' +\n'+'    \'        });\' +\n'+'    \'        marker.setMap(map);\' +\n'+'    \'        if(mapInfo != \\\'\\\') {\' +\n'+'    \'            var infowindow = new google.maps.InfoWindow({\' +\n'+'    \'                content: mapInfo\' +\n'+'    \'            });\' +\n'+'    \'            infowindow.open(map, marker);\' +\n'+'    \'            google.maps.event.addListener(marker, \\\'click\\\', function() {\' +\n'+'    \'                infowindow.open(map, marker);\' +\n'+'    \'            });\' +\n'+'    \'        }\' +\n';}
mapHtml+='    \'    };\' +\n'+'    \'    /*]]>*/\' +\n'+'    \'    </scr\' + \'ipt>\' +\n'+'    \'    <div id="googlemapcanvas" style="width: 100%; height: 100%"></div>\');\n'+'  /*]]>*/\n'+'</scr'+'ipt>\n'+'</div>';editor.insertHtml(mapHtml);},contents:[{id:'info',label:'','style':'overflow: hidden',elements:[{type:'vbox',padding:0,children:[{type:'hbox',widths:['10%','100%','10%'],children:[{type:'html','style':'display: block; margin-top: 4px;',html:editor.lang.googlemap.address+':'},{type:'text',id:'adresse',label:'','style':'width: 100%','default':editor.lang.googlemap.typeaddress,onClick:function()
{this.setValue('');}},{type:'button',id:'suche',label:editor.lang.googlemap.search,onClick:function()
{iframeWindow.showAddress(editor.lang.googlemap.addressnotfound);}}]},{id:'iframe',label:'googlemapframe',expand:true,type:'iframe',src:CKEDITOR.plugins.getPath('googlemap')+'dialogs/gmap.php',width:'450',height:'280','style':'padding: 10px 0px 10px 0px;',onContentLoad:function(){var iframe=document.getElementById(this._.frameId);iframeWindow=iframe.contentWindow;var dialog=CKEDITOR.dialog.getCurrent();var element=editor.getSelection().getSelectedElement();if(element)
{var realElement=editor.restoreRealElement(element);if(realElement.getAttribute('data-source')=='googlemapsframe')
{var mapScript=decodeURIComponent(realElement.getHtml()).toString();var gmap_marker=defaultMapVar(mapScript,'mapMarker')=='\\\'yes\\\''?true:false;var gmap_width=getStyle(realElement.getAttribute('style'),'width')||'';var gmap_height=getStyle(realElement.getAttribute('style'),'height')||'';var gmap_classes=realElement.getAttribute('class')||'';var gmap_id=realElement.getAttribute('id')||'';dialog.setValueOf('info','marker',gmap_marker);dialog.setValueOf('info','mapHeight',gmap_height);dialog.setValueOf('info','mapWidth',gmap_width);dialog.setValueOf('info','cssClass',gmap_classes);dialog.setValueOf('info','mapId',gmap_id);var map_infotext=defaultMapVar(mapScript,'mapInfo');map_infotext=map_infotext.substr(2,map_infotext.length-4);document.getElementById('mapinfowin_'+editor.name).getElementsByTagName('textarea')[0].value=map_infotext;var gmap_zoom=defaultMapVar(mapScript,'mapZoom');var gmap_lat=defaultMapVar(mapScript,'mapLat');var gmap_lng=defaultMapVar(mapScript,'mapLng');iframeWindow.zoomer=parseInt(gmap_zoom);iframeWindow.infowintext=map_infotext;iframeWindow.lat=parseFloat(gmap_lat);iframeWindow.lng=parseFloat(gmap_lng);iframeWindow.reloadMap();}}}},{type:'hbox',widths:['0px','17%','17%','33%','33%'],children:[{type:'html',html:'<div id="mapinfowin_'+editor.name+'" style="display: none" class="wbcke_maps_win">'+'<textarea class="wbcke_maps_editor"></textarea>'+'<img class="wbcke_maps_button" src="'+CKEDITOR.plugins.getPath('googlemap')+'images/delete.png" onclick="if(this.parentNode.getElementsByTagName(\'textarea\')[0].value != \'\') { if(confirm(\''+editor.lang.googlemap.confirm+'\')) { this.parentNode.getElementsByTagName(\'textarea\')[0].value = \'\'; iframeWindow.infowintext = \'\'; iframeWindow.setInfoWin(); } else { return false; } } this.parentNode.style.display=\'none\';" title="'+editor.lang.googlemap.del+'" />'+'<img class="wbcke_maps_button" src="'+CKEDITOR.plugins.getPath('googlemap')+'images/ok.png" onclick="iframeWindow.infowintext = this.parentNode.getElementsByTagName(\'textarea\')[0].value; iframeWindow.setInfoWin(); this.parentNode.style.display=\'none\';" title="'+editor.lang.googlemap.accept+'" style="margin-top: 30px" />'+'</div>'},{id:'marker',type:'checkbox',label:' '+editor.lang.googlemap.marker,'style':'margin-top: 17px;',onClick:function()
{iframeWindow.setMarker();}},{type:'button',id:'infotext',label:editor.lang.googlemap.text,'style':'margin-top: 15px;',onClick:function()
{this.getElement().getDocument().getById('mapinfowin_'+editor.name).setStyle('display','block');document.getElementById('mapinfowin_'+editor.name).getElementsByTagName('textarea')[0].focus();}},{type:'text',id:'mapWidth',label:editor.lang.googlemap.width,'default':'',required:false},{type:'text',id:'mapHeight',label:editor.lang.googlemap.height,'default':'',required:false}]},{type:'vbox',children:[{type:'text',id:'cssClass',label:editor.lang.common.cssClasses,'style':'margin-top: 10px;','default':'',required:false}]},{type:'vbox',children:[{type:'text',id:'mapId',label:editor.lang.googlemap.mapid,'default':'',required:false}]}]}]}]};});