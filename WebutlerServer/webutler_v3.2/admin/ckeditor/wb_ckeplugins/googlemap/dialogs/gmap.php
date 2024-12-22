<!DOCTYPE html>
<html lang="<?PHP echo $_SESSION['loggedin']['userlang']; ?>">
	<head scroll="no" style="overflow: hidden">
	<!--
    /**************************************
		Webutler V3.2 - www.webutler.de
		Copyright (c) 2008 - 2016
		Autor: Sven Zinke
		Free for any use
		Lizenz: GPL
    **************************************/
    -->
		<title>Google Map</title>
		<meta charset="UTF-8" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script>
        /*<![CDATA[*/
            var CKEDITOR = window.top.CKEDITOR || {};
            var dialog = CKEDITOR.dialog.getCurrent();
            var map, locator, lat, lng, marker, zoomer, infowin, infowintext = '';
            window.onload = function() {
                lat = 50.36406130437471;
                lng = 7.605553868103016;
                locator = new google.maps.LatLng(lat,lng);
                var myOptions = {
                    zoom: 15,
                    center: locator,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.TOP_RIGHT
                    },
                    zoomControl: true,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.LARGE,
                        position: google.maps.ControlPosition.LEFT_CENTER
                    },
                    panControl: false,
                    scaleControl: false,
                    streetViewControl: false
                };
                map = new google.maps.Map(document.getElementById('googlemapcanvas'), myOptions);
                marker = new google.maps.Marker();
                setMarker();
                google.maps.event.addListener(map, 'dblclick', function(event) {
                    locator = event.latLng;
                    lat = event.latLng.lat();
                    lng = event.latLng.lng();
                    if(dialog.getValueOf( 'info', 'marker' ) == true)
                        marker.setPosition(locator);
                });
            }
            
            function reloadMap() {
                if(map) {
                    locator = new google.maps.LatLng(lat,lng);
                    map.setCenter(locator);
                    map.setZoom(zoomer);
                    setMarker();
                    if(infowintext != '')
                        setInfoWin();
                }
            }
          
            function showAddress(notfound) {
                var geocoder = new google.maps.Geocoder();
                var address = dialog.getValueOf( 'info', 'adresse' );
                if (geocoder) {
                    geocoder.geocode( { 'address' : address }, function( results, status ) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            locator = results[0].geometry.location;
                            lat = results[0].geometry.location.lat();
                            lng = results[0].geometry.location.lng();
                            map.setCenter(locator);
                            setMarker();
                        }
                        else
                            alert(notfound);
                    });
                }
            }
            
            function setMarker() {
                if(dialog.getValueOf( 'info', 'marker' ) == true) {
                    marker.setPosition(locator);
                    marker.setMap(map);
                    marker.setDraggable(true);
                    google.maps.event.addListener(marker, 'dragend', function() {
                        updateMarker();
                    });
                    google.maps.event.addListener(marker, 'drag', function() {
                        updateMarker();
                    });
                }
                else {
                    marker.setMap(null);
                }
            }
            
            function updateMarker() {
                locator = marker.getPosition();
                lat = marker.getPosition().lat();
                lng = marker.getPosition().lng();
            }
            
            function setInfoWin() {
                infowin = new google.maps.InfoWindow({
                    content: infowintext
                });
                google.maps.event.addListener(marker, 'click', function() {  
                    infowin.open(map, marker);  
                });
            }
        /*]]>*/
        </script>
    </head>
    <body scroll="no" style="overflow: hidden; margin: 0px; padding: 0px">
      <div id="googlemapcanvas" style="width: 450px; height: 280px"></div>
    </body>
</html>
