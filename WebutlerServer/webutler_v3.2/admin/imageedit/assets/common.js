
/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

(function()
{
	var d = document.domain ;

	while ( true )
	{
		// Test if we can access a parent property.
		try
		{
			var test = window.top.opener.document.domain ;
			break ;
		}
		catch( e )
		{}

		// Remove a domain part: www.mytest.example.com => mytest.example.com => example.com ...
		d = d.replace( /.*?(?:\.|$)/, '' ) ;

		if ( d.length == 0 )
			break ;		// It was not able to detect the domain.

		try
		{
			document.domain = d ;
		}
		catch (e)
		{
			break ;
		}
	}
})() ;

/*
window.onload = window.onresize = function() {
    if(document.all) {
        document.getElementById('contents').style.height = 
        document.getElementById('editor').style.height = 
            parseInt(document.body.offsetHeight-56) + 'px';
        document.getElementById('contents').style.width = 
        document.getElementById('editor').style.width = 
            parseInt(document.body.offsetWidth-58) + 'px';
    }
}
*/
