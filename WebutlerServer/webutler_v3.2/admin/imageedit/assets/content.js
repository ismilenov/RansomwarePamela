/***********************************************************************
** Title.........:  Online Image Editor Interface
** Version.......:  1.0
** Author........:  Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:  content.js
** Last changed..:  31 Mar 2004 
** Notes.........:  Handles most of the interface routines for the ImageEditor.
*
* Added:  29 Mar 2004  - Constrainted resizing/scaling
**/ 

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


function findObj(n, d) {
	var p,i,x;
	if(!d) {
		d = document;
	}
	if((p = n.indexOf("?")) > 0 && parent.frames.length) {
		d = parent.frames[n.substring(p+1)].document;
		n = n.substring(0, p);
	}
	for (i = 0; !x && i < d.forms.length; i++) {
		x = d.forms[i][n];
	}
	if(!x) {
		x = d.getElementById(n);
	}
	return x;
}

var pic_x, pic_y;

function P7_Snap() { //v2.62 by PVII
	var x, y, ox, bx, oy, p, tx, a, b, k, d, da, e, el, args=P7_Snap.arguments;
	a = parseInt(a);
	for (k = 0; k < (args.length-3); k += 4) {
		if ((g = findObj(args[k])) != null) {
			el = eval(findObj(args[k+1]));
			a = parseInt(args[k+2]);
			b = parseInt(args[k+3]);
			x = 0;
			y = 0;
			ox = 0;
			oy = 0;
			p = "";
			tx = 1;
			d = "document.getElementsByName('"+args[k]+"')[0]";
			if(!eval(d)) {
				d = "document.getElementById('"+args[k]+"')";
			}
			while (tx==1) {
				p+= ".offsetParent";
				if(eval(d+p)) {
					x+= parseInt(eval(d+p+".offsetLeft"));
					y+= parseInt(eval(d+p+".offsetTop"));
				} else {
					tx = 0;
				}
			}
			ox = parseInt(g.offsetLeft);
			oy = parseInt(g.offsetTop);
			
			var tw = x+ox+y+oy;
			if(tw == 0 || (navigator.appVersion.indexOf("MSIE 4")>-1 && navigator.appVersion.indexOf("Mac")>-1)) {
				ox = 0;
				oy = 0;
				if(g.style.left) {
					x = parseInt(g.style.left);
					y = parseInt(g.style.top);
				}
				else {
					var w1 = parseInt(el.style.width);
					bx = (a<0)?-5-w1:-10;
					a = (Math.abs(a)<1000)?0:a;
					b = (Math.abs(b)<1000)?0:b;
					if(window.event == null) {
						x = document.body.scrollLeft + bx;
						y = document.body.scrollTop;
					}
					else {
						x = document.body.scrollLeft + event.pageX + bx;
						y = document.body.scrollTop + event.pageY;
					}
				}
			}
			if(el) {
				e = el.style;
				var xx = parseInt(x+ox+a), yy = parseInt(y+oy+b);
				//alert(xx+":"+yy);
				if(navigator.appVersion.indexOf("MSIE 5")>-1 && navigator.appVersion.indexOf("Mac")>-1) {
					xx+= parseInt(document.body.leftMargin);
					yy+= parseInt(document.body.topMargin);
					/*
					xx+= parseInt(document.body.scrollLeft);
					yy+= parseInt(document.body.scrollTop);
					*/
					xx+= 'px';
					yy+= 'px';
				}
				e.left = xx;
				e.top = yy;
			}
			pic_x = parseInt(xx);
			pic_y = parseInt(yy);
			//alert(xx+":"+yy);
		}
	}
}

var dragapproved = false;
var z, x, y, s_top, ant, mode, canvas, content, pic_width, pic_height, image, resizeHandle, oa_w, oa_h, oa_x, oa_y, mx2, my2;

function init_resize() {
    if(mode == "scale") {
        P7_Snap('theImage','ant',0,0);

        if (canvas == null) {
            canvas = findObj("imgCanvas");
		}
        if (pic_width == null || pic_height == null) {
            image = findObj("theImage");
            pic_width = parseInt(image.style.width);
            pic_height = parseInt(image.style.height);
        }
        if (ant == null) {
            ant = findObj("ant");
		}

		ant.className = setSelectionMode();
        ant.style.left = pic_x-1 + 'px';
		ant.style.top = pic_y-1 + 'px';
        ant.style.width = pic_width + 'px';
		ant.style.height = pic_height + 'px';
		
        drawBoundHandle();
        jg_doc.paint();
		dragStopped();
    }
}

function init_crop() {
    P7_Snap('theImage','ant',0,0);
}

function reset_crop() {
    if (ant == null) {
        ant = findObj("ant");
	}

    if (pic_width == null || pic_height == null) {
        image = findObj("theImage");
        pic_width = parseInt(image.style.width);
        pic_height = parseInt(image.style.height);
    }
	ant.className = 'noselection';
    ant.style.left = pic_x + 'px';
	ant.style.top = pic_y + 'px';
    ant.style.width = pic_width + 'px';
	ant.style.height = pic_height + 'px';
}

function resetImg() {
    image = findObj("theImage");
    image.style.width = image.getAttribute('orgwidth') + 'px';
    image.style.height = image.getAttribute('orgheight') + 'px';
}

function _reset() {
    if (ant == null) {
        ant = findObj("ant");
	}

	ant.className = 'noselection';
   	ant.style.width = 0;
   	ant.style.height = 0;
    ant.style.left = '-10px';
    ant.style.top = '-10px';

    mx2 = null;
    my2 = null;

    jg_doc.clear();
	
	if(mode != 'measure') {
		showStatus();   
	}
	if(mode == 'scale') {
		init_resize();
	}
	if(mode == 'crop') {
		init_crop();
		reset_crop();
	}
}

function setMode(newMode) {
    mode = newMode;
    _reset();
}

function setSelectionMode() {
	var selectionmode;
	if(window.top.document.getElementById("markerImg").src.indexOf("t_white.gif") > 0) {
		selectionmode = 'selectionWhite';
	}
	else {
		selectionmode = 'selection';
	}
	return selectionmode;
}

function toggleMarker() {
    //alert("Toggle");
    if (ant == null) {
        ant = findObj("ant");
	}

	if(ant.className != "noselection") {
	    ant.className = setSelectionMode();
	}
	
   	if(jg_doc.getColor() == '#000000') {
   	    jg_doc.setColor('#ffffff');
	}
   	else {
   	    jg_doc.setColor('#000000');
   	}
	
   	drawBoundHandle();
	jg_doc.paint();
	dragStopped();
}


function move(e) {
    if (dragapproved) {
        var w = temp1 + e.pageX - x;
        var h = temp2 + e.pageY - y;
        if (ant != null) {
            if (w >= 0) {
                ant.style.left = parseInt(x) + 'px';
                ant.style.width = parseInt(w) + 'px';
            }
            else {
                ant.style.left = parseInt(x+w) + 'px';
                ant.style.width = parseInt(-1*w) + 'px';
            }
            if (h >= 0) {
                ant.style.top = parseInt(y) + 'px';
                ant.style.height = parseInt(h) + 'px';
            }
            else {
                ant.style.top = parseInt(y+h) + 'px';
                ant.style.height = parseInt(-1*h) + 'px';
            }
        }
        showStatus();
        return false;
    }
}

function moveContent(e) {
    if (dragapproved) {
        var dx = oa_x + e.pageX-x;
        var dy = oa_y + e.pageY-y;
        ant.style.left = parseInt(dx) + 'px';
        ant.style.top = parseInt(dy) + 'px';
        showStatus();
        return false;
    }
}

//Code add for constraints by Frédéric Klee <fklee@isuisse.com>
function moveHandle(e) {
    if (dragapproved) {
        var w = e.pageX - x;
        var h = e.pageY - y;
		if(mode == 'scale') {
			var constrained = findObj('constProp', window.top.document);
		}
        var orginal_height = parseInt(document.theImage.style.height);
        var orginal_width = parseInt(document.theImage.style.width);
		
        rapp = orginal_width / orginal_height;
        rapp_inv = orginal_height / orginal_width;
        switch(resizeHandle) {
			case "s-resize":
			if (oa_h + h >= 0) {
				ant.style.height = parseInt(oa_h + h) + 'px';
				if(constrained && constrained.checked) {
					ant.style.width = parseInt(rapp * (oa_h + h)) + 'px';
					ant.style.left = parseInt(oa_x - rapp * h/2) + 'px';
				}
			}
			break;
			case "e-resize":
			if(oa_w + w >= 0) {
				ant.style.width = parseInt(oa_w + w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w + w)) + 'px';
					ant.style.top = parseInt(oa_y - rapp_inv * w/2) + 'px';
				}
			}
			break;
			case "n-resize":
			if (oa_h - h >= 0) {
				ant.style.top = parseInt(oa_y + h) + 'px';
				ant.style.height = parseInt(oa_h - h) + 'px';
				if(constrained && constrained.checked) {
					ant.style.width = parseInt(rapp * (oa_h - h)) + 'px';
					ant.style.left = parseInt(oa_x + rapp * h/2) + 'px';
				}
			}
			break;
			case "w-resize":
			if(oa_w - w >= 0) {
				ant.style.left = parseInt(oa_x + w) + 'px';
				ant.style.width = parseInt(oa_w - w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w - w)) + 'px';
					ant.style.top = parseInt(oa_y + rapp_inv * w/2) + 'px';
				}
			}
			break;
			case "nw-resize":
			if(oa_h - h >= 0 && oa_w - w >= 0) {
				ant.style.width = parseInt(oa_w - w) + 'px';
				ant.style.left = parseInt(oa_x + w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w - w)) + 'px';
					ant.style.top = (s_top - parseInt(rapp_inv * (oa_w - w))) + 'px';
				}
				else {
					ant.style.height = parseInt(oa_h - h) + 'px';
					ant.style.top = parseInt(oa_y + h) + 'px';
				}
			}
			break;
			case "ne-resize":
			if (oa_h - h >= 0 && oa_w + w >= 0) {
				ant.style.width = parseInt(oa_w + w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w + w)) + 'px';
					ant.style.top = (s_top - parseInt(rapp_inv * (oa_w + w))) + 'px';
				}
				else {
					ant.style.height = parseInt(oa_h - h) + 'px';
					ant.style.top = parseInt(oa_y + h) + 'px';
				}
			}
			break;
			case "se-resize":
			if (oa_h + h >= 0 && oa_w + w >= 0) {
				ant.style.width = parseInt(oa_w + w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w + w)) + 'px';
				}
				else {
					ant.style.height = parseInt(oa_h + h) + 'px';
				}
			}
			break;
			case "sw-resize":
			if (oa_h + h >= 0 && oa_w - w >= 0) {
				ant.style.left = parseInt(oa_x + w) + 'px';
				ant.style.width = parseInt(oa_w - w) + 'px';
				if(constrained && constrained.checked) {
					ant.style.height = parseInt(rapp_inv * (oa_w - w)) + 'px';
				}
				else {
				   ant.style.height = parseInt(oa_h + h) + 'px';
				}
			}
        }
        showStatus();
        return false;
    }
}

function drags(e) {
	var firedobj = e.target;
	var topelement = 'HTML';

	while (firedobj.tagName != topelement &&
		 !(firedobj.className == "crop"
		|| firedobj.className == "handleBox"
		|| firedobj.className == "selection"
		|| firedobj.className == "selectionWhite")) {
			firedobj = firedobj.parentNode;
	}
	if(firedobj.className == "handleBox") {
		if(content != null && content.style != null) {
			if(content.style.width != null && content.style.height != null) {
				content.style.width = 0;
				content.style.height = 0;
			}
		}
		resizeHandle = firedobj.id;
		
		x = e.pageX;
		y = e.pageY;

		oa_w = parseInt(ant.style.width);
		oa_h = parseInt(ant.style.height);
		oa_x = parseInt(ant.style.left);
		oa_y = parseInt(ant.style.top);

		dragapproved = true;
		document.onmousemove = moveHandle;
		return false;
	}
	else if((firedobj.className == "selection" || firedobj.className == "selectionWhite") && mode == "crop") {
		x = e.pageX;
		y = e.pageY;
		
		oa_x = parseInt(ant.style.left);
		oa_y = parseInt(ant.style.top);

		dragapproved = true;
		document.onmousemove = moveContent;
		return false;
	}
	else if(firedobj.className == "crop" && mode == "crop") {
		if(content != null && content.style != null) {
			if(content.style.width != null && content.style.height != null) {
				content.style.width = 0;
				content.style.height = 0;
			}
		}
		if(ant == null) {
			ant = findObj("ant");
		}
		if(canvas == null) {
			canvas = findObj("imgCanvas");
		}
		if(content == null) {
			content = findObj("cropContent");
		}
		if(pic_width == null || pic_height == null) {
			image = findObj("theImage");
			pic_width = parseInt(image.style.width);
			pic_height = parseInt(image.style.height);
		}
		ant.className = setSelectionMode();
		obj = firedobj;
		dragapproved = true;
		z = firedobj;
		temp1 = parseInt(z.style.left+0);
		temp2 = parseInt(z.style.top+0);
		x = e.pageX;
		y = e.pageY;
		document.onmousemove = move;
		return false;
	}
	else if(firedobj.className == "crop" && mode == "measure") {
		if (ant == null) {
			ant = findObj("ant");
		}
		if (canvas == null) {
			canvas = findObj("imgCanvas");
		}
		x = e.pageX;
		y = e.pageY;

		dragapproved = true;
		document.onmousemove = measure;
		return false;
	}
}

function measure(e) {
    if (dragapproved) {
        mx2 = e.pageX;
        my2 = e.pageY;
        
        jg_doc.clear();
        jg_doc.setStroke(Stroke.DOTTED); 
        jg_doc.drawLine(x,y,mx2,my2);
        jg_doc.paint();
        showStatus();
        return false;
    }
}

function setMarker(nx,ny,nw,nh) {
	if (isNaN(nx)) nx = 0;
    if (isNaN(ny)) ny = 0;
    if (isNaN(nw)) nw = 0;
    if (isNaN(nh)) nh = 0;

    if (ant == null) {
        ant = findObj("ant");
	}
    if (canvas == null) {
        canvas = findObj("imgCanvas");
	}
    if (content == null) {
        content = findObj("cropContent");
    }
    if (pic_width == null || pic_height == null) {
        image = findObj("theImage");
        pic_width = parseInt(image.style.width);
        pic_height = parseInt(image.style.height);
    }
	ant.className = setSelectionMode();

    nx = pic_x + nx;
    ny = pic_y + ny;

    if (nw >= 0) {
        ant.style.left = parseInt(nx) + 'px';
        ant.style.width = parseInt(nw) + 'px';
    }
    else {
        ant.style.left = parseInt(nx+nw) + 'px';
        ant.style.width = parseInt(-1*nw) + 'px';
    }
    if (nh >= 0) {
        ant.style.top = parseInt(ny) + 'px';
        ant.style.height = parseInt(nh) + 'px';
    }
    else {
        ant.style.top = parseInt(ny+nh) + 'px';
        ant.style.height = parseInt(-1*nh) + 'px';
    } 
}

function max(x,y) {
    if(y > x) {
        return x;
	}
    else {
        return y;
	}
}

function drawBoundHandle() {
    if(ant == null || ant.style == null) {
        return false;
	}
	var ah = parseInt(ant.style.height);
	var aw = parseInt(ant.style.width);
	var ax = parseInt(ant.style.left);
	var ay = parseInt(ant.style.top);

	jg_doc.drawHandle(ax-15,ay-15,30,30,"nw-resize"); //upper left
	jg_doc.drawHandle(ax-15,ay+ah-15,30,30,"sw-resize"); //lower left
	jg_doc.drawHandle(ax+aw-15,ay-15,30,30,"ne-resize"); //upper right
	jg_doc.drawHandle(ax+aw-15,ay+ah-15,30,30,"se-resize"); //lower right

	jg_doc.drawHandle(ax+max(15,aw/10),ay-8,aw-2*max(15,aw/10),8,"n-resize"); //top middle
	jg_doc.drawHandle(ax+max(15,aw/10),ay+ah,aw-2*max(15,aw/10),8,"s-resize"); //bottom middle
	jg_doc.drawHandle(ax-8, ay+max(15,ah/10),8,ah-2*max(15,ah/10),"w-resize"); //left middle
	jg_doc.drawHandle(ax+aw, ay+max(15,ah/10),8,ah-2*max(15,ah/10),"e-resize"); //right middle

	jg_doc.drawHandleBox(ax-4,ay-4,8,8,"nw-resize"); //upper left
	jg_doc.drawHandleBox(ax-4,ay+ah-4,8,8,"sw-resize"); //lower left
	jg_doc.drawHandleBox(ax+aw-4,ay-4,8,8,"ne-resize"); //upper right
	jg_doc.drawHandleBox(ax+aw-4,ay+ah-4,8,8,"se-resize"); //lower right

	jg_doc.drawHandleBox(ax+aw/2-4,ay-4,8,8,"n-resize"); //top middle
	jg_doc.drawHandleBox(ax+aw/2-4,ay+ah-4,8,8,"s-resize"); //bottom middle
	jg_doc.drawHandleBox(ax-4, ay+ah/2-4,8,8,"w-resize"); //left middle
	jg_doc.drawHandleBox(ax+aw-4, ay+ah/2-4,8,8,"e-resize"); //right middle
}

function showStatus() {
    if(ant == null || ant.style == null) {
        return false;
    }
    if(mode == "measure") {
        //alert(pic_x);
        mx1 = x - pic_x;
        my1 = y - pic_y;

        mw = mx2 - x;
        mh = my2 - y;

        md = parseInt(Math.sqrt(mw*mw + mh*mh)*100)/100;

        ma = (Math.atan(-1*mh/mw)/Math.PI)*180;
        if(mw < 0 && mh < 0) {
            ma = ma+180;
		}
        if (mw < 0 && mh > 0) {
            ma = ma-180;
		}
        ma = parseInt(ma*100)/100;

        if (m_sx != null && !isNaN(mx1)) {
            m_sx.value = mx1 + "px";
		}
        if (m_sy != null && !isNaN(my1)) {
            m_sy.value = my1 + "px";
		}
        if(m_w != null && !isNaN(mw)) {
            m_w.value = mw + "px";
		}
        if(m_h != null && !isNaN(mh)) {
            m_h.value = mh + "px";
		}
        if(m_d != null && !isNaN(md)) {
            m_d.value = md + "px";
		}
        if(m_a != null && !isNaN(ma)) {
            m_a.value = ma + "";
		}
        if(r_ra != null && !isNaN(ma)) {
            r_ra.value = ma;            
		}
        if(r_angle != null && !isNaN(ma)) {
            r_angle.value = ma;            
		}
        //alert("mx1:"+mx1+" my1"+my1);
        return false;
    }

    var ah = parseInt(ant.style.height);
    var aw = parseInt(ant.style.width);
    var ax = parseInt(ant.style.left);
    var ay = parseInt(ant.style.top);

    var cx = ax-pic_x<0 ? 0 : ax-pic_x;
    var cy = ay-pic_y<0 ? 0 : ay-pic_y;
    cx = cx>pic_width ? pic_width : cx;
    cy = cy>pic_height ? pic_height : cy;
    
    var cw = ax-pic_x>0 ? aw : aw-(pic_x-ax);
    var ch = ay-pic_y>0 ? ah : ah-(pic_y-ay);

    ch = ay+ah<pic_y+pic_height ? ch : ch-(ay+ah-pic_y-pic_height);
    cw = ax+aw<pic_x+pic_width ? cw : cw-(ax+aw-pic_x-pic_width);

    ch = ch<0 ? 0 : ch;
	cw = cw<0 ? 0 : cw;

    if (ant.style.visibility == "hidden") {
        cx = '';
		cy = '';
		cw = '';
		ch = '';
    }

    if(mode == 'crop') {
        if(t_cx != null) {
            t_cx.value = cx;
		}
        if (t_cy != null) { 
            t_cy.value = cy;
		}
        if(t_cw != null) {
            t_cw.value = cw;
		}
        if (t_ch != null) {
            t_ch.value = ch;
		}
	}
	
	if(mode == 'scale') {
        var sw = aw, sh = ah;

        if (s_sw.value.indexOf('%')>0 && s_sh.value.indexOf('%')>0) {   
            sw = cw/pic_width;
            sh = ch/pic_height;
        }
        if (s_sw != null) {
            s_sw.value = sw;
		}
        if (s_sh != null) {
            s_sh.value = sh;
		}
    }
}

function dragStopped() {
    dragapproved = false;

    if(ant == null || ant.style == null) {
        return false;
    }

    if(mode == "measure") {
        jg_doc.drawLine(x-4,y,x+4,y);
        jg_doc.drawLine(x,y-4,x,y+4);
        jg_doc.drawLine(mx2-4,my2,mx2+4,my2);
        jg_doc.drawLine(mx2,my2-4,mx2,my2+4);

        jg_doc.paint();
        showStatus();
        return false;
    }
	
	var ah = parseInt(ant.style.height);
	var aw = parseInt(ant.style.width);
	var ax = parseInt(ant.style.left);
	var ay = parseInt(ant.style.top);
	jg_doc.clear();
	
	if(content != null && content.style != null) {
		if(content.style.width != null && content.style.height != null) {
			content.style.width = parseInt(aw-1) + 'px';
			content.style.height = parseInt(ah-1) + 'px';
		}
		//alert(content.width+":"+content.height);
	}
	
	if(mode == "crop") {
		//alert(pic_y);
		jg_doc.fillRectPattern(pic_x,pic_y,pic_width,ay-pic_y,pattern);
		
		var h1 = ah;
		var y1 = ay;
		if (ah+ay >= pic_height+pic_y) {
			h1 = pic_height+pic_y-ay;
		}
		else if (ay <= pic_y) {
			h1 = ay+ah-pic_y;
			y1 = pic_y;
		}
		jg_doc.fillRectPattern(pic_x,y1,ax-pic_x,h1,pattern);
		jg_doc.fillRectPattern(ax+aw,y1,pic_x+pic_width-ax-aw,h1,pattern);
		jg_doc.fillRectPattern(pic_x,ay+ah,pic_width,pic_height+pic_y-ay-ah,pattern);
	}
	else if(mode == "scale") {
		//alert("Resizing: iw:"+image.width+" nw:"+aw);
		document.theImage.style.width = parseInt(aw) + 'px';
		document.theImage.style.height = parseInt(ah) + 'px';

		P7_Snap('theImage','ant',0,0);
		//alert("After Resizing: iw:"+image.width+" nw:"+aw);
		
		var winheight = document.body.scrollHeight;
		var top = (winheight-ah)/2;
		
		ant.style.left = (document.body.scrollWidth-aw)/2 + 'px';
		ant.style.top = top + 'px';
		
		s_top = (winheight-top);
	}

	drawBoundHandle();
	jg_doc.paint();
	showStatus();
	return false;
}

// set function for the wz_dragdrop script to use when dragging
function my_DragFunc()
{
	if(dd.elements.floater) {
		verifyBounds();
	}
	if(dd.elements.insertText) {
		verify_insertText();
	}
}

// keep the watermark within the background
function verifyBounds()
{
	var floater = dd.elements.floater;		
	var floater_org = dd.elements.background;
	var floater_newX = floater.x;
	var floater_newY = floater.y;

	if (floater.x < floater_org.x)
	{
		floater_newX = floater_org.x;
	}
	else if (floater.x + floater.w > floater_org.x + floater_org.w)
	{
		floater_newX = floater_org.x + (floater_org.w - floater.w);
	}
	
	if (floater.y < floater_org.y)
	{
		floater_newY = floater_org.y;
	}
	else if (floater.y + floater.h > floater_org.y + floater_org.h)
	{
		floater_newY = floater_org.y + (floater_org.h - floater.h);
	}
	
	if (floater_newX != floater.x || floater_newY != floater.y)
	{
		floater.moveTo(floater_newX, floater_newY);
	}
}

function verify_insertText()
{
	var insertText_txt = dd.elements.insertText;		
	var insertText_org = dd.elements.TextBG;
	var insertText_newX = insertText_txt.x;
	var insertText_newY = insertText_txt.y;
	
	if (insertText_txt.x < insertText_org.x)
	{
		insertText_newX = insertText_org.x;
	}
	else if (insertText_txt.x + insertText_txt.w > insertText_org.x + insertText_org.w)
	{
		insertText_newX = insertText_org.x + (insertText_org.w - insertText_txt.w);
	}
	
	if (insertText_txt.y < insertText_org.y)
	{
		insertText_newY = insertText_org.y;
	}
	else if (insertText_txt.y + insertText_txt.h > insertText_org.y + insertText_org.h)
	{
		insertText_newY = insertText_org.y + (insertText_org.h - insertText_txt.h);
	}
	
	if (insertText_newX != insertText_txt.x || insertText_newY != insertText_txt.y)
	{
		insertText_txt.moveTo(insertText_newX, insertText_newY);
	}
}

initEditor = function()
{
    init_crop();
    init_resize();
    var markerImg = findObj('markerImg', window.top.document);

    if (markerImg.src.indexOf("img/t_white.gif") > 0)
        toggleMarker() ;
}

document.onmousedown = drags;
document.onmouseup = dragStopped;
