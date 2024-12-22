
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


CKEDITOR.plugins.addExternal( 'saveinline', 'wb_ckeplugins/saveinline/' );
CKEDITOR.plugins.addExternal( 'savetemppage', 'wb_ckeplugins/savetemppage/' );
CKEDITOR.plugins.addExternal( 'closeeditor', 'wb_ckeplugins/closeeditor/' );
CKEDITOR.plugins.addExternal( 'uioptselect', 'wb_ckeplugins/uioptselect/' );
CKEDITOR.plugins.addExternal( 'disabletoolbar', 'wb_ckeplugins/disabletoolbar/' );
CKEDITOR.plugins.addExternal( 'insertscript', 'wb_ckeplugins/insertscript/' );
CKEDITOR.plugins.addExternal( 'columns', 'wb_ckeplugins/columns/' );
CKEDITOR.plugins.addExternal( 'browser', 'wb_ckeplugins/browser/' );
CKEDITOR.plugins.addExternal( 'audio', 'wb_ckeplugins/audio/' );
CKEDITOR.plugins.addExternal( 'video', 'wb_ckeplugins/video/' );
CKEDITOR.plugins.addExternal( 'lightbox', 'wb_ckeplugins/lightbox/' );
CKEDITOR.plugins.addExternal( 'flashplayer', 'wb_ckeplugins/flashplayer/' );
CKEDITOR.plugins.addExternal( 'formchanges', 'wb_ckeplugins/formchanges/' );
CKEDITOR.plugins.addExternal( 'formfields', 'wb_ckeplugins/formfields/' );
CKEDITOR.plugins.addExternal( 'formsendto', 'wb_ckeplugins/formsendto/' );
CKEDITOR.plugins.addExternal( 'pagelinks', 'wb_ckeplugins/pagelinks/' );
CKEDITOR.plugins.addExternal( 'googlemap', 'wb_ckeplugins/googlemap/' );
CKEDITOR.plugins.addExternal( 'imageedit', 'wb_ckeplugins/imageedit/' );
CKEDITOR.plugins.addExternal( 'sortmenu', 'wb_ckeplugins/sortmenu/' );
CKEDITOR.plugins.addExternal( 'pastedupload', 'wb_ckeplugins/pastedupload/' );
CKEDITOR.plugins.addExternal( 'measuretool', 'wb_ckeplugins/measuretool/' );

//CKEDITOR.plugins.addExternal( 'maxmediawidth', 'wb_ckeplugins/maxmediawidth/' );
//CKEDITOR.plugins.addExternal( 'dragresize', 'wb_ckeplugins/dragresize/' );

CKEDITOR.plugins.addExternal( 'docprops', 'wb_ckeplugins/docprops/' );
CKEDITOR.plugins.addExternal( 'stylesheetparser', 'wb_ckeplugins/stylesheetparser/' );
CKEDITOR.plugins.addExternal( 'showprotected', 'wb_ckeplugins/showprotected/' );
CKEDITOR.plugins.addExternal( 'sourcedialog', 'wb_ckeplugins/sourcedialog/' );


CKEDITOR.editorConfig = function( config )
{
	config.docType = '<!DOCTYPE html>';
	config.skin = 'wbmoono,wb_ckeskin/wbmoono/';
	config.title = false;
	config.disableAutoInline = true;
	config.allowedContent = true;
	config.dialog_noConfirmCancel = true;
	config.toolbarCanCollapse = false;
	config.baseFloatZIndex = 9000;
	config.entities = false;
	config.basicEntities = true;
	config.entities_additional = '#39,quot';
	config.startupShowBorders = true;
	config.emailProtection = 'encode';
	config.stylesSet = [];
	
	config.disableObjectResizing = true;
	config.disableNativeTableHandles = true;
	
	config.fillEmptyBlocks = true;
	config.autoParagraph = false;
	config.ignoreEmptyParagraph = false; //true;
	config.forceEnterMode = true; // p on enter inside div
	
	//config.pasteFromWordRemoveStyles = true;
	//config.pasteFromWordRemoveFontStyles = true;
	config.forcePasteAsPlainText = true;
	config.magicline_everywhere = false;
	
	config.image_prefillDimensions = false;
	config.templates_replaceContent = false;
	
    config.templates = 'usertemplates';
    config.templates_files = [ 'content/pattern/js/cketemplates.js' ];
	
	config.protectedSource.push( /<\?[\s\S]*?\?>/gi );
	config.protectedSource.push( /<script[\s\S]*?\/script>/gi );
	
	config.format_tags = 'p;div;h1;h2;h3;h4;h5;h6';
	config.fontSize_sizes = '7 Pixel/7px;8 Pixel/8px;9 Pixel/9px;10 Pixel/10px;11 Pixel/11px;12 Pixel/12px;13 Pixel/13px;14 Pixel/14px;15 Pixel/15px;16 Pixel/16px;17 Pixel/17px;18 Pixel/18px;19 Pixel/19px;20 Pixel/20px;21 Pixel/21px;22 Pixel/22px;23 Pixel/23px;24 Pixel/24px;25 Pixel/25px;26 Pixel/26px;27 Pixel/27px;28 Pixel/28px;29 Pixel/29px;30 Pixel/30px;31 Pixel/31px;32 Pixel/32px';
	config.font_style =
    {
        element : 'span',
        styles : { 'font-family': '#(family)' },
        overrides : [ { element: 'font', attributes: { 'face': null } } ]
    };
	
	config.menu_groups = 'clipboard,tablecell,tablecellproperties,tablerow,tablecolumn,table,anchor,link,image,imgedit,editprotected,flash,inscode,div,form,formfelder,googlemaps,textfield,textarea,select,checkbox,radio,hiddenfield,button,imagebutton,sendtofield';
	
	config.imageUploadUrl = 'admin/browser/index.php?pasted=newfile&type=image';
    config.filebrowserBrowseUrl = 'admin/browser/index.php';
    config.filebrowserImageBrowseUrl = 'admin/browser/index.php?type=image&actualfolder=' + encodeURIComponent('/');
    config.filebrowserImageBrowseLinkUrl = 'admin/browser/index.php?types=image&actualfolder=' + encodeURIComponent('/');
    config.filebrowserFlashBrowseUrl = 'admin/browser/index.php?type=flash&actualfolder=' + encodeURIComponent('/');
	config.filebrowserAVBrowseUrl = 'admin/browser/index.php?type=track&av=only&actualfolder=' + encodeURIComponent('/');
	config.filebrowserWindowFeatures = 'directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=no';
    
    config.toolbar_EditPage =
    [
    	{ name: 'document', items: ['Savetemp','Save','Closer','DocProps','InsertScript','Columns','Templates'] },
    	{ name: 'clipboard', items: ['Cut','Copy','Paste'] },
    	{ name: 'editing', items: ['Undo','Redo','Find','Replace','SelectAll','RemoveFormat','Measure'] },
    	{ name: 'basicstyles', items: ['Bold','Italic','Underline','Strike','Subscript','Superscript'] },
    	{ name: 'paragraph', items: ['NumberedList','BulletedList','Outdent','Indent','BidiLtr', 'BidiRtl','Blockquote'] },
    	{ name: 'justify', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
    	{ name: 'colors', items: ['TextColor','BGColor'] },
    	{ name: 'links', items: ['Link','Unlink','Anchor','Iframe'] },
    	{ name: 'medias', items: ['Media','Image','gMap','Video','Audio','Embed','Flash'] },
    	{ name: 'insert', items: ['Table','CreateDiv','Form','HorizontalRule','ShowBlocks','SpecialChar'] },
    	{ name: 'styles', items: ['Styles','Format','Font','FontSize'] },
    	{ name: 'source', items: ['Sourcedialog'] }
    ] ;
    
    config.toolbar_EditMenu =
    [
    	{ name: 'document', items: ['Save','Closer'] },
    	{ name: 'menu', items: ['SortMenu'] },
    	{ name: 'clipboard', items: ['Cut','Copy','Paste'] },
    	{ name: 'editing', items: ['Undo','Redo','RemoveFormat'] },
    	{ name: 'basicstyles', items: ['Bold','Italic','Underline','Strike'] },
    	{ name: 'paragraph', items: ['BulletedList','Outdent','Indent','BidiLtr', 'BidiRtl'] },
    	{ name: 'justify', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
    	{ name: 'colors', items: ['TextColor','BGColor'] },
    	{ name: 'links', items: ['Link','Unlink'] },
    	{ name: 'medias', items: ['Image','ShowBlocks','SpecialChar'] },
    	{ name: 'styles', items: ['Styles'] },
    	{ name: 'source', items: ['Sourcedialog'] }
    ] ;
    
    config.toolbar_EditBlock =
    [
    	{ name: 'document', items: ['Save','Closer','Columns','Templates'] },
    	{ name: 'clipboard', items: ['Cut','Copy','Paste'] },
    	{ name: 'editing', items: ['Undo','Redo','Find','Replace','SelectAll','RemoveFormat'] },
    	{ name: 'basicstyles', items: ['Bold','Italic','Underline','Strike','Subscript','Superscript'] },
    	{ name: 'paragraph', items: ['NumberedList','BulletedList','Outdent','Indent','BidiLtr', 'BidiRtl','Blockquote'] },
    	{ name: 'justify', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
    	{ name: 'colors', items: ['TextColor','BGColor'] },
    	{ name: 'links', items: ['Link','Unlink','Anchor','Iframe'] },
    	{ name: 'medias', items: ['Media','Image','gMap','Video','Audio','Embed','Flash'] },
    	{ name: 'insert', items: ['Table','CreateDiv','Form','HorizontalRule','ShowBlocks','SpecialChar'] },
    	{ name: 'styles', items: ['Styles','Format','Font','FontSize'] },
    	{ name: 'source', items: ['Sourcedialog'] }
    ] ;

    config.toolbar_EditContent =
    [
    	{ name: 'document', items: ['Savetemp','Save','Closer','DocProps','InsertScript','Columns','Templates'] },
    	{ name: 'clipboard', items: ['Cut','Copy','Paste'] },
    	{ name: 'editing', items: ['Undo','Redo','Find','Replace','SelectAll','RemoveFormat','Measure'] },
    	{ name: 'basicstyles', items: ['Bold','Italic','Underline','Strike','Subscript','Superscript'] },
    	{ name: 'paragraph', items: ['NumberedList','BulletedList','Outdent','Indent','BidiLtr', 'BidiRtl','Blockquote'] },
    	{ name: 'justify', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
    	{ name: 'colors', items: ['TextColor','BGColor'] },
    	{ name: 'links', items: ['Link','Unlink','Anchor','Iframe'] },
    	{ name: 'medias', items: ['Media','Image','gMap','Video','Audio','Embed','Flash'] },
    	{ name: 'insert', items: ['Table','CreateDiv','Form','HorizontalRule','ShowBlocks','SpecialChar'] },
    	{ name: 'styles', items: ['Styles','Format','Font','FontSize'] },
    	{ name: 'source', items: ['Sourcedialog'] }
    ] ;
};

CKEDITOR.on('instanceReady', function( ev )
{
    var editor = ev.editor;
	var writer = editor.dataProcessor.writer;
	
	writer.setRules( 'link', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'script', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'header', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'nav', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'div', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'section', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'article', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
	writer.setRules( 'footer', { indent: true, breakAfterOpen : true, breakBeforeClose : true });
});

CKEDITOR.on('dialogDefinition', function( ev )
{
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;
    var editor = ev.editor;
    
	dialogDefinition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;
    
    if(dialogName === 'table')
    {
        var infoTab = dialogDefinition.getContents('info');
        var cellSpacing = infoTab.get('txtCellSpace');
        cellSpacing['default'] = "0";
        var cellPadding = infoTab.get('txtCellPad');
        cellPadding['default'] = "0";
        var border = infoTab.get('txtBorder');
        border['default'] = "0";
    }
    
    if(dialogName === 'flash')
    {
        var infoTab = dialogDefinition.getContents('info');
        var browseButton = infoTab.get('browse');
        browseButton['style'] = 'margin-top: 15px;';
        var previewField = infoTab.get('preview');
        previewField['style'] = 'width: 420px;';
        
        var propertiesTab = dialogDefinition.getContents('properties');
		var menuField = propertiesTab.get( 'menu' );
		menuField['default'] = false;
		var playField = propertiesTab.get( 'play' );
		playField['default'] = false;
		var loopField = propertiesTab.get( 'loop' );
		loopField['default'] = false;
		var allowFullScreenField = propertiesTab.get( 'allowFullScreen' );
		allowFullScreenField['default'] = true;
    }
    
    if(dialogName === 'image')
    {
        var infoTab = dialogDefinition.getContents('info');
        var browseButton = infoTab.get('browse');
        browseButton['style'] = 'margin-top: 15px;';
		
        var linkTab = dialogDefinition.getContents('Link');
        var browseButton = linkTab.get('browse');
        browseButton['style'] = 'float: right; margin: 10px 0px;';
    }
    
    if(dialogName === 'link')
    {
        var infoTab = dialogDefinition.getContents('info');
        var browseButton = infoTab.get('browse');
        browseButton['style'] = 'float: right;';
    }
    
    if(dialogName === 'templates')
    {
        var selTplTab = dialogDefinition.getContents('selectTpl');
		var chkInsertOptField = selTplTab.get( 'chkInsertOpt' );
        chkInsertOptField['default'] = false;
        chkInsertOptField['style'] = 'display: none;';
    }
    
    if(dialogName === 'docProps')
    {
		dialogDefinition.minHeight = 325;
		dialogDefinition.minWidth = 400;
		dialogDefinition.removeContents( 'general' );
		dialogDefinition.removeContents( 'design' );
		dialogDefinition.removeContents( 'preview' );
    }
    
    if(dialogName === 'form')
    {
        var url = window.location.href;
        var query = url.substring(url.indexOf('\?'), url.length);
        
        var infoTab = dialogDefinition.getContents('info');
        
		var actionField = infoTab.get( 'action' );
        actionField['default'] = 'index.php' + query;
		var dataField = infoTab.get( 'enctype' );
        dataField['default'] = 'text/plain';
		var methodField = infoTab.get( 'method' );
        methodField['default'] = 'post';
    }
});


