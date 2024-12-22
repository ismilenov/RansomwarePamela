
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


var wbjq = jQuery.noConflict(true);
var webutler_columnsconfig;
var webutler_columneditors = '';
var webutler_columnscss = '';

function WBeditbox_requestdelete(txt, value) {
	if(confirm(txt.replace(/_STRING_/g, value))) {
        return true;
	}
    return false;
}

function WBeditbox_requestchange(txt, value) {
	if(confirm(txt.replace(/_STRING_/g, value))) {
        return true;
	}
    return false;
}

function WBeditbox_requestrename(txt, oldval, newval) {
	if(confirm(txt.replace(/_STRING_OLD_/g, oldval).replace(/_STRING_NEW_/g, newval))) {
        return true;
	}
    return false;
}

function WBeditbox_copyfrom() {
    if(wbjq('#webutler_newfrom').is(':checked')) {
    	wbjq('#webutler_copyrow').css('display', '');
    	wbjq('#webutler_layoutrow').css('display', 'none');
    }
    else {
    	wbjq('#webutler_layoutrow').css('display', '');
    	wbjq('#webutler_copyrow').css('display', 'none');
    }
}

function WBeditbox_changeblockedpage(from, to) {
	if(wbjq('#' + from).val() != '') {
		var item = wbjq('#' + from + ' option:selected');
		var value = item.val();
		wbjq('#' + to).append(wbjq('<option value="' + value + '">' + value + '</option>'));
		item.remove();
	}
}

function WBeditbox_savelistedpages(list, temp) {
    var pages = new Array();
	wbjq('#' + list + ' option').each(function(i) {
        pages.push(wbjq(this).val());
	});
	/*
    for(var i = 0; i < wbjq('#' + list + ' option').length; i++) {
        pages.push(wbjq('#' + list).options[i].val());
    }
	*/
    wbjq('#' + temp).val(pages.join(','));
	return true;
}

function WBeditbox_vorschau(url, txt) {
	if(url.substring(parseInt(url.lastIndexOf('=')+1), url.length) == "") {
        alert(txt);
	}
	else {
		var width = 800;
		var height = 500;
		var top = parseInt( ( window.screen.height - height ) / 2, 10 );
		var left = parseInt( ( window.screen.width  - width ) / 2, 10 );
		
		var show = window.open('index.php?' + url, 'vorschau', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=yes');	
		show.focus();
	}
}

function WBeditbox_dopublic(page) {
    wbjq('#webutler_loadingscreen').css('display', 'block');
    wbjq.ajax({
        type: 'post',
        url: 'admin/system/save.php',
        data: 'dopublicpage=' + page,
        success: function(datas) {
			if(datas != '') {
				var data = datas.split('###');
				alert(data[1]);
				if(data[0] == 'ok') {
					window.location = 'index.php?page=' + page;
				}
				else {
					wbjq('#webutler_loadingscreen').css('display', 'none');
				}
			}
        }
    });
    return false;
}

function WBeditbox_deltemp(page, txt) {
	if(confirm(txt)) {
		wbjq('#webutler_loadingscreen').css('display', 'block');
		wbjq.ajax({
			type: 'post',
			url: 'admin/system/save.php',
			data: 'deltemppage=' + page,
			success: function(datas) {
				if(datas != '') {
					var data = datas.split('###');
					if(data[0] == 'ok') {
						window.location = 'index.php?page=' + page;
					}
					else {
						alert(data[1]);
						wbjq('#webutler_loadingscreen').css('display', 'none');
					}
				}
			}
		});
	}
    return false;
}

function WBeditbox_checkbak(folder) {
    var file = wbjq('#webutler_' + folder + 'box').val();
    wbjq.ajax({
        type: 'post',
        url: 'admin/system/boxes.php',
        data: 'webutler_checkbak=1&folder=' + folder + '&file=' + file,
        success: function(datas) {
            var data = datas.split('###');
            wbjq('#webutler_' + folder + 'bakbutton').val(data[2]);
            wbjq('.webutler_boxesform').attr('action', 'index.php?page=' + locationPage);
            wbjq('#webutler_' + folder + 'bakcheck').prop('checked', false).attr('disabled', (data[0] == 'exists') ? false : true);
            wbjq('#webutler_' + folder + 'baklabel').html(data[1]);
            wbjq('#webutler_' + folder + 'bakpreview').click(function() {
                if(wbjq('#webutler_' + folder + 'bakcheck').is(':checked'))
                    WBeditbox_vorschau(folder + 'file=' + file + '.bak', '');
                else
                    WBeditbox_vorschau(folder + 'file=' + file, '');
            });
            wbjq('#webutler_' + folder + 'bakcheck').click(function() {
                if(wbjq(this).is(':checked')) {
                    wbjq('.webutler_boxesform').removeAttr('action');
                    wbjq('#webutler_' + folder + 'bakbutton').val(data[3]);
                }
                else {
                    wbjq('.webutler_boxesform').attr('action', 'index.php?page=' + locationPage);
                    wbjq('#webutler_' + folder + 'bakbutton').val(data[2]);
                }
            });
        }
    });
}

function WBeditbox_opennewwin(url) {
	if(url != "") {
		var width = 900;
		var height = 500;
		var top = parseInt( ( window.screen.height - height ) / 2, 10 );
		var left = parseInt( ( window.screen.width  - width ) / 2, 10 );
		
    	var showwin = window.open(url, 'fenster', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=yes');
    	showwin.focus();
	}
}

function WBeditbox_checkfile(file) {
    wbjq.ajax({
        type: 'post',
        url: 'admin/system/save.php',
        data: 'checkfileexists=' + file,
        success: function(data) {
            if(file != 'offline') {
                if(data != '') {
                    alert(data);
                }
                else {
                    if(file == 'forms')
                        WBeditbox_open('webutler_forms');
                    if(file == 'langs')
                        WBeditbox_open('webutler_langs', 'langtrcodes=1');
                    if(file == 'categories')
                        WBeditbox_open('webutler_categories');
                    if(file == 'linkhighlite')
                        WBeditbox_open('webutler_linkhighlite', 'highfilestr=1');
                }
            }
        }
    });
    return false;
}

function WBeditbox_open(win, sub, posts) {
    if(win == 'webutler_newpage') {
        wbjq.ajax({
            type: 'post',
            url: 'admin/system/save.php',
            data: 'checkfileexists=offline'
        });
    }
    
	var blockerbg = wbjq('#webutler_blockerbackground');
	var blockerdiv = wbjq('#webutler_blockerdiv');
	var sliderdiv = wbjq('#webutler_sliderdiv');
	var boxwindow = wbjq('#webutler_boxwindow');
	
    if(blockerbg.is(':hidden'))
        blockerbg.animate({ opacity: 0.6 }, 50).fadeIn('fast');
    if(blockerdiv.is(':hidden'))
    	blockerdiv.fadeIn('fast');
    
    var ajaxurl, ajaxpost;
    
	if(win == 'webutler_access') {
	    ajaxurl = 'admin/system/boxuser.php';
        if(sub != '' && sub != undefined) ajaxurl += '?' + sub;
        ajaxpost = win + '=1&locationpage=' + locationPage;
        if(posts != '' && posts != undefined) ajaxpost += '&' + posts;
    }
    else if(win == 'webutler_langs' && sub == '') {
        WBeditbox_open(win, 'langtrcodes=1');
        return false;
    }
    else if(win == 'webutler_linkhighlite' && sub == '') {
        WBeditbox_open(win, 'highfilestr=1');
        return false;
    }
    else {
        ajaxurl = 'admin/system/boxes.php';
        ajaxpost = win + '=1&locationpage=' + locationPage;
        if(sub != '' && sub != undefined) ajaxpost += '&' + sub;
    }
    
    wbjq.ajax({
        type: 'post',
        url: ajaxurl,
        data: ajaxpost,
        success: function(data) {
            boxwindow.html(data);
            boxwindow.show(function() {
                if(win == 'webutler_categories' && !wbjq('#webutler_showeditcat').is(':visible')) {
                    wbjq('#webutler_showeditcat').removeAttr('style');
            	}
                if(win == 'webutler_langs' && !wbjq('#webutler_showeditlang').is(':visible')) {
                    wbjq('#webutler_showeditlang').removeAttr('style');
            	}
                if(win == 'webutler_pagecats') {
                    wbjq('#webutler_oldcategory').val(wbjq('#webutler_newcategory').val());
                }
                wbjq('.langposup').bind('click', function() {
                    wbjq.ajax({
                        type: 'post',
                        url: 'admin/system/save.php',
                        data: 'langposup=' + wbjq(this).attr('name'),
                        success: function() {
                            WBeditbox_open(win, 'langtrcodes=1');
                        }
                    });
                    return false;
                });
                wbjq('.langposdown').bind('click', function() {
                    wbjq.ajax({
                        type: 'post',
                        url: 'admin/system/save.php',
                        data: 'langposdown=' + wbjq(this).attr('name'),
                        success: function() {
                            WBeditbox_open(win, 'langtrcodes=1');
                        }
                    });
                    return false;
                });
                wbjq('.webutler_delhighlitefile').bind('click', function() {
                    wbjq.ajax({
                        type: 'post',
                        url: 'admin/system/save.php',
                        data: 'delhighlitefile=' + wbjq(this).attr('name'),
                        success: function(data) {
                            wbjq('#' + win).find('.webutler_winmeldung').html(data).fadeIn(200).delay(2000).fadeOut(200, function() {
                                WBeditbox_open(win, 'highfilestr=1');
                            });
                        }
                    });
                    return false;
                });
                wbjq('.webutler_delhighlitefolder').bind('click', function() {
                    wbjq.ajax({
                        type: 'post',
                        url: 'admin/system/save.php',
                        data: 'delhighlitefolder=' + wbjq(this).attr('name'),
                        success: function(data) {
                            wbjq('#' + win).find('.webutler_winmeldung').html(data).fadeIn(200).delay(2000).fadeOut(200, function() {
                                WBeditbox_open(win, 'highfoldertr=1');
                            });
                        }
                    });
                    return false;
                });
                wbjq('.webutler_boxesform').bind('submit', function() {
                    var form = wbjq(this);
                    var fact = form.attr('action');
                    if(typeof fact == 'undefined') {
                        var items = new Array();
                        var submitname = wbjq(form).find('input[type="submit"]').attr('name');
                        items.push(submitname + '=1');
                        
                        items.push('locationpage=' + locationPage);
                        wbjq.each(wbjq(form).find(':input').serializeArray(), function(i, field) {
                            if(field.type == 'radio' || field.type == 'checkbox') {
                                /*
                                var fieldval = '';
                                if(field.value == '') fieldval = wbjq(field).attr('checked');
                                else fieldval = field.value;
                                items.push(field.name + '=' + fieldval);
                                */
                                items.push(field.name + '=' + wbjq(field).attr('checked'));
                            }
                            else
                                items.push(field.name + '=' + field.value);
                        });
                        
                        var submititems = items.join('&');
                        
                        if(win == 'webutler_access') {
                            if(submitname == 'makeuserconfig')
                            	WBeditbox_open(win, '', submititems);
                            else if(submitname == 'update_blocked_pages')
                            	WBeditbox_open(win, 'showtr=blocks', submititems);
                            else if(submitname == 'make_update_group' || submitname == 'make_new_group' || submitname == 'update_edit_group')
                            	WBeditbox_open(win, 'showtr=gruppen', submititems);
                            else if(submitname == 'make_new_user')
                            	WBeditbox_open(win, 'showtr=zugang&subtr=newuser', submititems);
                            else if(submitname == 'update_newregs_user' || submitname == 'update_newreg_user')
                            	WBeditbox_open(win, 'showtr=zugang&subtr=newregs', submititems);
                            else if(submitname == 'make_update_user' || submitname == 'update_edit_user')
                            	WBeditbox_open(win, 'showtr=zugang&subtr=edituser', submititems);
                            else if(submitname == 'update_config_file')
                            	WBeditbox_open(win, 'showtr=usersets', submititems);
                            
                            return false;
                        }
                        else {
							if(submitname == 'savelangcode' && !WBeditbox_requestlangdelete()) {
	                            return false;
	                        }
	                        else if(submitname == 'openformnew' || submitname == 'openformedit') {
	                            WBeditbox_open(win, submititems);
	                            return false;
	                        }
	                        else {
	                            wbjq.ajax({
	                                type: 'post',
	                                url: 'admin/system/save.php',
	                                data: submititems,
	                                success: function(datas) {
	                                    var data = datas.split('###');
	                                    var thisform;
	                                    if(win == 'webutler_forms')
	                                        thisform = wbjq('.webutler_winmeldung');
	                                    else
	                                        thisform = wbjq(form).find('.webutler_winmeldung');
										
	                                    thisform.html(data[0]).fadeIn(200).delay(2000).fadeOut(200, function() {
	                                    	if(win == 'webutler_newpage' && typeof(data[1]) != 'undefined') {
	                                        	if(confirm(data[1]))
	                                                window.location = data[2];
	                                        }
	                                        else if((win == 'webutler_pagecats' || win == 'webutler_delpage' || win == 'webutler_rename' || win == 'webutler_pagelang') && typeof(data[1]) != 'undefined') {
                                                alert(data[1]);
                                                window.location = data[2];
                                        	}
                                        	else {
                                                if((submitname == 'savenewform' || submitname == 'saveeditform') && data[1] == 'error')
                                                    return false;
                                                else if(submitname == 'savelangcode')
                                                    WBeditbox_open(win, 'langtrcodes=1');
                                                else if(submitname == 'savelanghomes')
                                                    WBeditbox_open(win, 'langtrfronts=1');
                                                else if(submitname == 'savehighlitefile')
                                                    WBeditbox_open(win, 'highfilestr=1');
                                                else if(submitname == 'savehighlitefolder')
                                                    WBeditbox_open(win, 'highfoldertr=1');
                                                else
                                                    WBeditbox_open(win);
                                        	}
	                                    });
	                                }
	                            });
	                            return false;
	                        }
	                    }
                    }
                });
            	if(win == 'webutler_newmenu') {
                    WBeditbox_checkbak('menu');
                }
            	if(win == 'webutler_newblock') {
                    WBeditbox_checkbak('block');
                }
            	if(win == 'webutler_advanced') {
                    var winheight = wbjq(window).height();
                    //var boxheight = wbjq(this).height();
                    var checkheight = parseInt(winheight-200);
                    wbjq('#webutler_advancedscroller').css({ 'height': checkheight + 'px' });
                    wbjq('#webutler_advancedscrollarea').css({ 'height': checkheight + 'px' });
                    wbjq('#webutler_advancedviewport').css({ 'height': checkheight + 'px' });
                    wbjq('#webutler_advancedscrollbar').css({ 'height': checkheight + 'px', 'opacity': '0' });
					var opened = false;
                    wbjq('#webutler_advanced').hover(
                        function() {
							if(!opened)
								opened = WBadvancedbox_vscrollbar('advanced');
                            wbjq('#webutler_advancedscrollbar').animate({
                                opacity: 1.0
                            }, 500);
                        }, 
                        function() {
                            wbjq('#webutler_advancedscrollbar').animate({
                                opacity: 0.5
                            }, 500);
                        }
                    );
            	}
                wbjq('#webutler_deletecolumnsbutton').bind('click', function() {
					WBeditbox_close();
					wbjq('#webutler_logo').fadeOut(200);
					wbjq('#webutler_editbox').fadeOut(200);
					wbjq('#webutler_hidebox').fadeOut(200);
					WBcolumns_hoverdeletedivs();
					
					wbjq('.webutler_canceldelcolumns').click(function() {
						wbjq('.wb_columnselement, .wb_contentelement').off('mouseenter');
						wbjq('#webutler_logo').fadeIn(200);
						wbjq('#webutler_editbox').fadeIn(200);
						wbjq('#webutler_hidebox').fadeIn(200);
						wbjq('#webutler_deletecolumnsbefore').css('display', 'none');
						wbjq('#webutler_deletecolumnsafter').css('display', 'none');
					});
                });
            	if(win == 'webutler_columns') {
					sliderdiv.css({
						'width': '702px',
						'margin-left': '-371px'
					});
					
                    wbjq('#webutler_columnsscrollbar').css({ 'opacity': 0 });
					
					wbjq('#webutler_colnumb').on('click', 'li', function() {
						wbjq('#webutler_columnsinfotext').css({'display': 'none'});
						wbjq('#webutler_columnsviewcontent').css({ 'width': '' });
						wbjq('#webutler_colnumb li').removeClass('active');
						wbjq(this).addClass('active');
						
						wbjq('#webutler_columnsviewport, #webutler_columnsscroller').off('hover');
						wbjq('#webutler_columnsscrollbar').stop( true, true ).animate({
							opacity: 0
						}, 100);
						
						var numb = parseInt(wbjq(this).text());
						WBeditbox_loadcolumnconf(numb, function() {
							var contentwidth = wbjq('#webutler_columnsviewcontent > table:first-child').width() + 'px' || '';
							wbjq('#webutler_columnsviewcontent').css({ 'width': contentwidth });
							
							var opened = false;
							if(wbjq('#webutler_columnsviewcontent').width() > wbjq('#webutler_columnsviewport').width()) {
								wbjq('#webutler_columnsviewport, #webutler_columnsscroller').hover(
									function() {
										if(!opened)
											opened = WBadvancedbox_hscrollbar('columns');
										wbjq('#webutler_columnsscrollbar').animate({
											opacity: 1.0
										}, 500);
									}, 
									function() {
										wbjq('#webutler_columnsscrollbar').animate({
											opacity: 0.5
										}, 500);
									}
								);
							}
						});
					});
					
					wbjq('#webutler_insertcolumnsbutton').on('click', function() {
						if(wbjq('#webutler_colnumb li').hasClass('active')) {
							var result = '';
							var collength;
							//if(wbjq('#webutler_singlecolumn').find('.webutler_columnssingletext').length > 0) {
							if(wbjq('#webutler_singlecolumn').hasClass('webutler_columnssingletext')) {
								collength = 1;
								webutler_columnscss = wbjq('#webutler_rowcss').val() != '' ? '{"row":"' + wbjq('#webutler_rowcss').val() + '"}' : '';
								result += '{"single":"true"}';
							}
							else {
								var columnscss = {};
								var columneditors = {};
								
								columnscss['row'] = wbjq('#webutler_rowcss').val() != '' ? wbjq('#webutler_rowcss').val() : '';
								columnscss['cols'] = {};
								
								collength = wbjq('#webutler_columnsviewcontent').find('.webutler_colconf').length;
								var columns = {};
								for(var i = 0; i < collength; i++) {
									var num = parseInt(i+1);
									
									columns['col' + num] = {};
									columns['col' + num]['align'] = wbjq('#webutler_col_align_' + num + ' option:selected').val();
									columns['col' + num]['small'] = wbjq('#webutler_col_small_' + num + ' option:selected').val();
									columns['col' + num]['medium'] = wbjq('#webutler_col_medium_' + num + ' option:selected').val();
									columns['col' + num]['large'] = wbjq('#webutler_col_large_' + num + ' option:selected').val();
									columns['col' + num]['order'] = {};
									columns['col' + num]['order']['s'] = wbjq('#webutler_order_small_' + num).val();
									columns['col' + num]['order']['m'] = wbjq('#webutler_order_medium_' + num).val();
									columns['col' + num]['order']['l'] = wbjq('#webutler_order_large_' + num).val();
									
									if(wbjq('#webutler_coleditor_' + num).is(':checked'))
										columneditors['editor' + num] = 'set';
									
									if(wbjq('#webutler_colcss_' + num).val() != '')
										columnscss['cols']['col' + num] = wbjq('#webutler_colcss_' + num).val();
								}
								result += JSON.stringify(columns);
								
								webutler_columneditors = JSON.stringify(columneditors);
								webutler_columnscss = JSON.stringify(columnscss);
							}
							
							webutler_columnsconfig = result;
							
							WBeditbox_close();
							wbjq('#webutler_insertcolumnsbefore, #webutler_insertcolumnsafter').find('.webutler_columnsname').text(collength + ' ');
							wbjq('#webutler_logo').fadeOut(200);
							wbjq('#webutler_editbox').fadeOut(200);
							wbjq('#webutler_hidebox').fadeOut(200);
							WBcolumns_hoverbodydivs();
						}
					});
					
					wbjq('.webutler_cancelcolumns').click(function() {
						wbjq('.wb_columnselement, .wb_contentelement').off('mouseenter');
						if(insertColumnsOnElements == 'full')
							wbjq('.wb_menuelement, .wb_blockelement').off('mouseenter');
						wbjq('#webutler_logo').fadeIn(200);
						wbjq('#webutler_editbox').fadeIn(200);
						wbjq('#webutler_hidebox').fadeIn(200);
						wbjq('#webutler_insertcolumnsbefore').css('display', 'none');
						wbjq('#webutler_insertcolumnsafter').css('display', 'none');
					});
            	}
            });
            if(sliderdiv.is(':hidden'))
            	sliderdiv.fadeIn('slow');
            wbjq('.webutler_winmeldung').hide();
        }
    });
    return false;
}

function WBeditbox_loadcolumnconf(numb, callback) {
	if(numb == 1) {
		//wbjq('#webutler_columnsrowcss').css({'display': 'none'});
		wbjq('#webutler_columnsscroller').css({ 'display': 'none' });
		wbjq('#webutler_singlecolumn').addClass('webutler_columnssingletext').css({'display': 'block'});
		wbjq('#webutler_columnsrowcss').css({'display': 'block'});
	}
	else {
		var collist = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
		var template = wbjq('#webutler_coltemp').html();
		var findnum = '###NUM###';
		var numreg = new RegExp(findnum, 'g');
		var optsel = numb == 2 || numb == 3 || numb == 4 || numb == 6 ? 12/numb : 1;
		var findopt = '<option value="' + optsel + '">';
		var optreg = new RegExp(findopt, 'g');
		for(var i = 0; i < numb; i++) {
			collist += '<td>';
			collist += template.replace(numreg, parseInt(i+1)).replace(optreg, '<option value="' + optsel + '" selected="selected">');
			collist += '</td>';
		}
		collist += '</tr></table>';
		
		wbjq('#webutler_singlecolumn').removeClass('webutler_columnssingletext').css({'display': 'none'});
		wbjq('#webutler_columnsviewcontent').html(collist);
		wbjq('#webutler_columnsrowcss').css({'display': 'block'});
		wbjq('#webutler_columnsscroller').css({ 'display': 'block' });
		
	}
	
	callback();
}

function WBcolumns_hoverdeletedivs() {
	var deletepoints = '.wb_columnselement, .wb_contentelement';
	
	wbjq(deletepoints).on('mouseenter', function() {
		wbjq('.webutler_deletecolumns').unbind('click');
		
		var elem;
		if(wbjq(this).closest('.wb_columnselement').length > 0) {
			elem = wbjq(this).closest('.wb_columnselement');
		}
		else if(wbjq(this).closest('.wb_contentelement').length > 0) {
			elem = wbjq(this).closest('.wb_contentelement');
		}
		else {
			elem = wbjq(this);
		}

		var position = elem.position();
		var top = parseInt(position.top + parseInt(elem.css('marginTop')));
		var left = parseInt(position.left + parseInt(elem.css('marginLeft')));
		var height = parseInt(elem.height() + parseInt(elem.css('paddingTop')) + parseInt(elem.css('paddingBottom')));
		var width = parseInt(elem.width() + parseInt(elem.css('paddingLeft')) + parseInt(elem.css('paddingRight')));
		
		wbjq('#webutler_deletecolumnsbefore').css({
			'display': 'block',
			'left': left + 'px',
			'top': (top-7 < 5 ? 5 : top-7) + 'px',
			'width': width + 'px'
		});
		wbjq('#webutler_deletecolumnsafter').css({
			'display': 'block',
			'left': left + 'px',
			'top': parseInt(top+height-7) + 'px',
			'width': width + 'px'
		});
		
		wbjq('.webutler_deletecolumns').bind('click', function() {
			wbjq('#webutler_loadingscreen').css('display', 'block');
			var columnsindex = elem.index(deletepoints);
			WBcolumns_delete(columnsindex);
		});
	});
}

function WBcolumns_delete(divindex) {
	wbjq.ajax({
		type: 'post',
		url: 'admin/system/save.php',
		data: 'deletecolumnsfrompage=1&divindex=' + divindex + '&getpage=' + locationPage,
		success: function(datas) {
			var data = datas.split('###');
			alert(data[1]);
			if(data[0] == 'ok') {
				location.reload(true);
			}
		}
	});
}

function WBcolumns_hoverbodydivs() {
	var insertpoints = '.wb_columnselement, .wb_contentelement';
	if(insertColumnsOnElements == 'full')
		insertpoints += ', .wb_menuelement, .wb_blockelement';
	
	wbjq(insertpoints).on('mouseenter', function() {
		wbjq('.webutler_insertcolumns').unbind('click');
		
		var elem;
		if(wbjq(this).closest('.wb_columnselement').length > 0) {
			elem = wbjq(this).closest('.wb_columnselement');
		}
		else if(wbjq(this).closest('.wb_contentelement').length > 0) {
			elem = wbjq(this).closest('.wb_contentelement');
		}
		else if(insertColumnsOnElements == 'full' && wbjq(this).closest('.wb_menuelement').length > 0) {
			elem = wbjq(this).closest('.wb_menuelement');
		}
		else if(insertColumnsOnElements == 'full' && wbjq(this).closest('.wb_blockelement').length > 0) {
			elem = wbjq(this).closest('.wb_blockelement').first();
		}
		else {
			elem = wbjq(this);
		}

		var position = elem.position();
		var top = parseInt(position.top + parseInt(elem.css('marginTop')));
		var left = parseInt(position.left + parseInt(elem.css('marginLeft')));
		var height = parseInt(elem.height() + parseInt(elem.css('paddingTop')) + parseInt(elem.css('paddingBottom')));
		var width = parseInt(elem.width() + parseInt(elem.css('paddingLeft')) + parseInt(elem.css('paddingRight')));
		
		wbjq('#webutler_insertcolumnsbefore').css({
			'display': 'block',
			'left': left + 'px',
			'top': (top-7 < 5 ? 5 : top-7) + 'px',
			'width': width + 'px'
		});
		wbjq('#webutler_insertcolumnsafter').css({
			'display': 'block',
			'left': left + 'px',
			'top': parseInt(top+height-7) + 'px',
			'width': width + 'px'
		});
		
		wbjq('#webutler_insertcolumnsbefore').find('.webutler_insertcolumns').bind('click', function() {
			wbjq('#webutler_loadingscreen').css('display', 'block');
			var columnsindex = elem.index(insertpoints);
			WBcolumns_insertnew(columnsindex, encodeURIComponent(webutler_columnsconfig), encodeURIComponent(webutler_columnscss), encodeURIComponent(webutler_columneditors), 'before');
		});
		
		wbjq('#webutler_insertcolumnsafter').find('.webutler_insertcolumns').bind('click', function() {
			wbjq('#webutler_loadingscreen').css('display', 'block');
			var columnsindex = elem.index(insertpoints);
			WBcolumns_insertnew(columnsindex, encodeURIComponent(webutler_columnsconfig), encodeURIComponent(webutler_columnscss), encodeURIComponent(webutler_columneditors), 'after');
		});
	});
}

function WBcolumns_insertnew(divindex, columnsconfig, columnscss, columneditors, position) {
	var margin = insertColumnsPromtText == 'disabled' ? 'false' : prompt(insertColumnsPromtText, '');
	
	wbjq.ajax({
		type: 'post',
		url: 'admin/system/save.php',
		data: 'insertcolumnsatposition=1&divindex=' + divindex + '&columnsconfig=' + columnsconfig + '&columnscss=' + columnscss + '&columneditors=' + columneditors + '&margin=' + margin + '&position=' + position + '&getpage=' + locationPage,
		success: function(datas) {
			var data = datas.split('###');
			alert(data[1]);
			if(data[0] == 'ok') {
				location.reload(true);
			}
		}
	});
}

function WBadvancedbox_vscrollbar(scrollerwin)
{
	var scrollarea = wbjq('#webutler_' + scrollerwin + 'scrollarea');
	var viewport = wbjq('#webutler_' + scrollerwin + 'viewport');
	var viewcontent = wbjq('#webutler_' + scrollerwin + 'viewcontent');
	var scrollbar = wbjq('#webutler_' + scrollerwin + 'scrollbar');
	var track = wbjq('#webutler_' + scrollerwin + 'track');
	var viewHeight = viewport.height();
	var contentHeight = viewcontent.height();
	var scrollHeight = scrollbar.height();
	var mean = 30; //speed
	var current = 0;
	var dragit = '_false';
	
	track.css({
		'height': Math.round(viewHeight / contentHeight * scrollHeight) + 'px',
		'top': (viewport.scrollTop() / contentHeight * scrollHeight) + 'px'
	});
	
	viewport.on('mousewheel DOMMouseScroll', function(evt) {
		var delta = Math.max(-1, Math.min(1, (evt.originalEvent.wheelDelta || -evt.originalEvent.detail)));
		
		viewport.scrollTop(viewport.scrollTop() - delta * mean);
		track.css({
			'top': (viewport.scrollTop() / contentHeight * scrollHeight) + 'px'
		});

		evt.preventDefault();
	});
	
	track.on('mousedown', function(evt) {
		scrollarea.addClass('noSelect');
		var posY = evt.clientY;
		var divTop = parseInt(wbjq('#webutler_' + scrollerwin + 'track').css('top'));
		var diffY = posY - divTop;
		wbjq(document).on('mousemove', function(evt) {
			var posY = evt.clientY;
			var divY = posY - diffY;
			var minY = 0;
			var maxY = scrollHeight - parseInt(track.height() - 2);
			
			if(divY >= minY && divY < maxY) {
				var scrolltop = parseInt(divY + 2) * (contentHeight / scrollHeight);
				viewport.scrollTop(scrolltop);
				track.css({
					'top': divY + 'px'
				});
			}
		});
	});
	
	wbjq(document).on('mouseup', function(evt) {
		scrollarea.removeClass('noSelect');
		wbjq(document).off('mousemove');
	});
	
	return true;
}

function WBadvancedbox_hscrollbar(scrollerwin)
{
	var scrollarea = wbjq('#webutler_' + scrollerwin + 'scrollarea');
	var viewport = wbjq('#webutler_' + scrollerwin + 'viewport');
	var viewcontent = wbjq('#webutler_' + scrollerwin + 'viewcontent');
	var scrollbar = wbjq('#webutler_' + scrollerwin + 'scrollbar');
	var track = wbjq('#webutler_' + scrollerwin + 'track');
	var viewWidth = viewport.width();
	var contentWidth = viewcontent.width();
	var scrollWidth = scrollbar.width();
	var mean = 30; //speed
	var current = 0;
	var dragit = '_false';
	
	track.css({
		'width': Math.round(viewWidth / contentWidth * scrollWidth) + 'px',
		'left': (viewport.scrollLeft() / contentWidth * scrollWidth) + 'px'
	});
	
	viewport.on('mousewheel DOMMouseScroll', function(evt) {
		var delta = Math.max(-1, Math.min(1, (evt.originalEvent.wheelDelta || -evt.originalEvent.detail)));
		
		viewport.scrollLeft(viewport.scrollLeft() - delta * mean);
		track.css({
			'left': (viewport.scrollLeft() / contentWidth * scrollWidth) + 'px'
		});

		evt.preventDefault();
	});
	
	track.on('mousedown', function(evt) {
		scrollarea.addClass('noSelect');
		var posX = evt.clientX;
		var divLeft = parseInt(wbjq('#webutler_' + scrollerwin + 'track').css('left'));
		var diffX = posX - divLeft;
		wbjq(document).on('mousemove', function(evt) {
			var posX = evt.clientX;
			var divX = posX - diffX;
			var minX = 0;
			//var maxX = scrollWidth - parseInt(track.width() - 2);
			var maxX = scrollWidth - track.width();
			
			if(divX >= minX && divX < maxX) {
				//var scrollleft = parseInt(divX + 2) * (contentWidth / scrollWidth);
				var scrollleft = divX * (contentWidth / scrollWidth);
				viewport.scrollLeft(scrollleft);
				track.css({
					'left': divX + 'px'
				});
			}
		});
	});
	
	wbjq(document).on('mouseup', function(evt) {
		scrollarea.removeClass('noSelect');
		wbjq(document).off('mousemove');
	});
	
	return true;
}

function WBeditbox_close() {
	wbjq('#webutler_blockerbackground').fadeOut('medium');
	wbjq('#webutler_blockerdiv').fadeOut('medium');
	wbjq('#webutler_sliderdiv').fadeOut('medium', function() {
        wbjq(this).css({
            'width': '',
			'margin-left': '',
            'left': '',
            'top': ''
        });
    });
}

function WBeditbox_auto_check() {
	if(wbjq('#webutler_check_auto').is(':checked')) {
		wbjq('#webutler_sub_auto').css('display', 'block');
	}
	else {
		wbjq('#webutler_sub_auto').css('display', 'none');
	}
}

function WBeditbox_switch(on, off) {
	wbjq('#' + on).css('display', 'block');
	wbjq('#' + off).css('display', 'none');
}

function WBeditbox_setpagemodus(txt, datei, modus, isoff) {
    if(modus == 'off')
        WBeditbox_checkfile('offline');
    
	if(confirm(txt)) {
        wbjq.ajax({
            type: 'post',
            url: 'admin/system/save.php',
            data: 'setmodus=' + modus + '&filename=' + datei,
            success: function(data) {
                if(modus == 'on') {
                    wbjq('#webutler_hidegetpage').removeAttr('style');
                    wbjq('#webutler_showgetpage').css({'display': 'none'});
					if(isoff != '') {
						if(data == 'noaccess')
							wbjq('#webutler_pageisoff').fadeOut( 200, function() { wbjq(this).text(isoff).fadeIn(200); } );
						else
							wbjq('#webutler_pageisoff').fadeOut( 500, function() { wbjq(this).remove(); } );
					}
                }
                else if(modus == 'off') {
                    wbjq('#webutler_hidegetpage').css({'display': 'none'});
                    wbjq('#webutler_showgetpage').removeAttr('style');
					if(isoff != '') {
						if(wbjq('#webutler_pageisoff').length > 0)
							wbjq('#webutler_pageisoff').fadeOut( 200, function() { wbjq(this).text(isoff).fadeIn(200); } );
						else
							wbjq('body').prepend(wbjq('<div id="webutler_pageisoff">' + isoff + '</div>\n').fadeIn(500));
					}
                }
            }
        });
        return false;
	}
}

function WBeditbox_lastversion(datum, datei) {
	if(confirm(datum)) {
		window.location = "admin/system/save.php?page=" + datei + "&version=getlast"; 
	}
}

function setimgfromcode(value) {
	wbjq('#imgfromcode').css('backgroundImage', 'url(includes/language/icons/' + value + '.png)');
}

function WBeditbox_hidesubtrs(show, hide) {
	var hides = hide.split('|');
	for(var i = 0; i < hides.length; i++) {
		wbjq('#' + hides[i]).css('display', 'none');
	}
	wbjq('#' + show).css('display', '');
}

function WBeditbox_hidecattrs(show, hide) {
	wbjq('#webutler_categories').find('.webutler_select').each(function() {
        wbjq(this).prop('selectedIndex', 0);
    });
	wbjq('#webutler_categories').find('.webutler_input').each(function() {
        wbjq(this).val('');
    });
	wbjq('.webutler_tr' + hide + 'cat').css({'display': 'none'});
	wbjq('.webutler_tr' + show + 'cat').css({'display': ''});
	wbjq('#savecategoriesbutton').attr('name', 'save' + show + 'categories');
	wbjq('#savecategoriesbutton').val(wbjq('#webutler_txt' + show + 'button').text());
}

function WBeditbox_hidehighlitetrs(show, hide) {
	wbjq('.webutler_trhigh' + hide).css({'display': 'none'});
	wbjq('.webutler_trhigh' + show).css({'display': ''});
}

/*
function WBeditbox_hidealertcells() {
	if(document.getElementById('webutler_errorcell') && document.getElementById('webutler_errorcell').style.display != 'none')
        document.getElementById('webutler_errorcell').style.display = 'none';
    
	if(document.getElementById('webutler_alertcell') && document.getElementById('webutler_alertcell').style.display != 'none')
        document.getElementById('webutler_alertcell').style.display = 'none';
}
*/

function WBeditbox_enableforcer() {
    if((wbjq('#config_regby2').length > 0 && !wbjq('#config_regby2').is(':checked')) || !wbjq('#config_regon').is(':checked'))
        wbjq('#config_regtouser').attr('disabled', true);
    else
        wbjq('#config_regtouser').attr('disabled', false);
}

function WBeditbox_showexplaination(text) {
    if(wbjq('#webutler_explainusers').length > 0) {
        wbjq('#webutler_explainusers').html(text);
    }
    else {
        wbjq('#webutler_access').append('<div id="webutler_explainusers">' + text + '</div>');
    }

	wbjq('#webutler_explainusers').click(function () {
        //wbjq('#webutler_access').removeChild(wbjq('#webutler_explainusers'));
		wbjq('#webutler_explainusers').remove();
	});
}

function WBeditbox_openmedia() {
    var iWidth = mediabrowserWindowWidth;
	if ( typeof iWidth == 'string' && iWidth.length > 1 && iWidth.substr( iWidth.length - 1, 1 ) == '%' )
		iWidth = parseInt( window.screen.width * parseInt( iWidth, 10 ) / 100, 10 );
    
    var iHeight = mediabrowserWindowHeight;
	if ( typeof iHeight == 'string' && iHeight.length > 1 && iHeight.substr( iHeight.length - 1, 1 ) == '%' )
		iHeight = parseInt( window.screen.height * parseInt( iHeight, 10 ) / 100, 10 );
	
	if(iWidth < 640) iWidth = 640;
	if(iHeight < 420) iHeight = 420;
	
	var iTop = parseInt( ( window.screen.height - iHeight ) / 2, 10 );
	var iLeft = parseInt( ( window.screen.width  - iWidth ) / 2, 10 );
	
	var WindowFeatures = 'width=' + iWidth + ',height=' + iHeight + ',left=' + iLeft + ',top=' + iTop + ',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=no';
	var BrowseUrl = 'admin/browser/index.php';

	var popupWindow = window.open( '', 'CKBrowseNoneDialog', WindowFeatures, true );

	if ( !popupWindow )
		return false;

	try {
		popupWindow.moveTo( iLeft, iTop );
		popupWindow.resizeTo( iWidth, iHeight );
		popupWindow.focus();
		popupWindow.location.href = BrowseUrl;
	}
	catch ( e ) {
		popupWindow = window.open( BrowseUrl, 'CKBrowseNoneDialog', WindowFeatures, true );
	}
}

wbjq(document).ready(function()
{
    var WBboxvar_dragsliderbox = false;
    
    wbjq('#webutler_slider').mousedown(function(e) {
    	WBboxvar_dragsliderbox = true;
		var divLeft = e.clientX - parseInt(wbjq('#webutler_sliderdiv').css('left'));
        wbjq(document.body).mousemove(function(e) {
            //e.preventDefault();
            if(WBboxvar_dragsliderbox == true) {
                wbjq('#webutler_sliderdiv').css({
                    opacity: 0.8,
                    top: parseInt(e.clientY - 26),
                    left: parseInt(e.clientX - divLeft)
                });
            }
        });
    }).mouseup(function() {
    	WBboxvar_dragsliderbox = false;
    	wbjq('#webutler_sliderdiv').css({ opacity: 1.0 });
    });
    
    wbjq('#webutler_logo').click(function() {
        wbjq(this).animate({
            opacity: 0
        }, 500, function() {
			wbjq(this).css({
				'display': 'none',
				'opacity': ''
			});
		});
    });
    
    wbjq('#webutler_showbox').click(function() {
        wbjq(this).fadeOut(200, function() {
            wbjq('#webutler_editbox').animate({
                marginLeft: '23px'
            }, 500, function() {
                wbjq('#webutler_hidebox').fadeIn(200);
                wbjq.ajax({
                    type: 'post',
                    url: 'admin/system/save.php',
                    data: 'editboxzustand=show'
                });
                return false;
            });
        });
    });
    
    wbjq('#webutler_hidebox').click(function() {
        wbjq(this).fadeOut(200, function() {
        	var wbeditboxleft = parseInt(wbjq('#webutler_editbox').outerWidth()+10);
            wbjq('#webutler_editbox').animate({
                marginLeft: '-' + wbeditboxleft + 'px'
            }, 500, function() {
                wbjq('#webutler_showbox').fadeIn(200);
                wbjq.ajax({
                    type: 'post',
                    url: 'admin/system/save.php',
                    data: 'editboxzustand=hide'
                });
                return false;
            });
        });
    });
    
    wbjq('#webutler_hidebox').mouseover(function () {
		wbjq('.webutler_submenu').each(function() {
			var menu = wbjq(this).find('.webutler_submenudiv');
			if(menu.is(':visible')) {
				menu.stop(true,true).fadeTo(10, 0).css({'display': 'none'});
			}
		});
    });
    
    wbjq('.webutler_submenu').hover(
        function () {
            wbjq(this).addClass('hasFocus');
            wbjq(this).find('.webutler_adminicon').css('backgroundPosition', '-16px 0px');
			var sub = wbjq(this);
			wbjq('.webutler_submenu').each(function() {
				var menu = wbjq(this).find('.webutler_submenudiv');
				if(sub != wbjq(this) && menu.is(':visible') && !wbjq(this).hasClass('hasFocus')) {
					menu.stop(true,true).fadeTo(10, 0).css({'display': 'none'});
				}
			});
            if(!wbjq('#webutler_editbox').is(':animated')) {
				wbjq(this).find('.webutler_submenudiv').stop(true,true).fadeTo(300, 1).css({'display': 'inline-block'});
			}
        },
        function () {
			wbjq(this).removeClass('hasFocus');
			wbjq(this).find('.webutler_adminicon').css('backgroundPosition', '0px 0px');
			var menu = wbjq(this).find('.webutler_submenudiv');
			if(menu.is(':visible') && !wbjq(this).hasClass('hasFocus')) {
				menu.stop(true,true).delay(400).fadeTo(300, 0, function() {
					menu.css({'display': 'none'});
				});
			}
        }
    );
	
    wbjq('#webutler_loadingscreen').css('display', 'none');
});

