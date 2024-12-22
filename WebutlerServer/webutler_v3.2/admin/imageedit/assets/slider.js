/***********************************************************************
** Title.........:  Simple Lite Slider for Image Editor
** Version.......:  1.1
** Author........:  Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:  slider.js
** Last changed..:  31 Mar 2004 
** Notes.........:  Works in IE and Mozilla
**/ 

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

document.onmouseup = captureStop;

var currentSlider = null;
var sliderField = null;

var rangeMin = null;
var rangeMax = null;
var sx = -1;
var sy = -1;
var initX = 0;

function getMouseXY(e) {

    x = e.clientX;
    y = e.clientY;
    
    if (sx < 0) {
		sx = x;
	}
	if (sy < 0) {
		sy = y;
	}

    var dx = initX +(x-sx);
    
    if (dx <= rangeMin) {
        dx = rangeMin;
	}
    else if (dx >= rangeMax) {
        dx = rangeMax;
	}

    var range = (dx-rangeMin)/(rangeMax - rangeMin)*100;

    if (currentSlider !=  null) {
        currentSlider.style.left = parseInt(dx) + 'px';
	}
        
    if (sliderField != null) {
        sliderField.value = parseInt(range);
    }

	if (currentSlider.id == "sliderbarwatermark") {
		changeWatermarkOpacity(dx);
	}

	return false;
}

function initSlider(action) {

	currentSlider = document.getElementById('sliderbar' + action);
    sliderField = document.getElementById('sliderfield' + action);

    if (rangeMin == null) {
        rangeMin = 3;
	}
    if (rangeMax == null) {
        var track = document.getElementById('slidertrack' + action);
        rangeMax = parseInt(track.style.width);
    }
}

function updateSlider(value, action) {

	initSlider(action);
	
	if(value == "")
		value = 100;

    var newValue = (parseInt(value)/100)*(rangeMax-rangeMin);

    if (newValue <= rangeMin) {
        var newValue = rangeMin;
	}
    else if (newValue >= rangeMax) {
        var newValue = rangeMax;
	}

    if (currentSlider !=  null) {
        currentSlider.style.left = parseInt(newValue) + 'px';
	}
    
    var range = (newValue/(rangeMax-rangeMin))*100;

    if (sliderField != null) {
        sliderField.value = parseInt(range);
    }

	if (action == "watermark") {
		changeWatermarkOpacity(newValue);
	}
}

function captureStart(action) {

    initSlider(action);

    initX = parseInt(currentSlider.style.left);
	
    if (initX > rangeMax) {
        initX = rangeMax;
	}
    else if (initX < rangeMin) {
        initX = rangeMin;
	}

    document.onmousemove = getMouseXY;

    return false;
}

function UpdateSliderField(action) {

	if(document.getElementById('save_format').options && action == 'save') {
		if(document.getElementById('sliderfieldsave').value == '85') {
			document.getElementById('save_format').options.value = 'jpg,85';
		}
		else if(document.getElementById('sliderfieldsave').value == '60') {
			document.getElementById('save_format').options.value = 'jpg,60';
		}
		else if(document.getElementById('sliderfieldsave').value == '35') {
			document.getElementById('save_format').options.value = 'jpg,35';
		}
		else {
			document.getElementById('save_format').options.value = 'jpg,100';
		}
	}
}

function captureStop() {

    sx = -1;
	sy = -1;
    document.onmousemove = null;
    return false;
}
