<?PHP

/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * imagick PECL extension implementation for Image_Transform package
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Image
 * @package    Image_Transform
 * @subpackage Image_Transform_Driver_IMAGICK
 * @author     Alan Knowles <alan@akbkhome.com>
 * @author     Peter Bowyer <peter@mapledesign.co.uk>
 * @author     Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @copyright  2002-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: IMAGICK.php,v 1.6 2005/04/28 17:54:03 jausions Exp $
 * @link       http://pear.php.net/package/Image_Transform
 */


/**
 * imagick PECL extension implementation for Image_Transform package
 *
 * EXPERIMENTAL - please report bugs
 * Use the latest cvs version of imagick PECL
 *
 * @category   Image
 * @package    Image_Transform
 * @subpackage Image_Transform_Driver_IMAGICK
 * @author     Alan Knowles <alan@akbkhome.com>
 * @author     Peter Bowyer <peter@mapledesign.co.uk>
 * @author     Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @copyright  2002-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_Transform
 * @since      PHP 4.0
 */

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once "transform.php";

class Image_Transform_Driver_IMAGICK extends Image_Transform
{
    /**
     * Handler of the imagick image ressource
     * @var array
     */
    var $imageHandle = null;

    /**
     *
     */
    function Image_Transform_Driver_IMAGICK()
    {
        return true;
    } // End Image_Transform_Driver_IMAGICK

    /**
     * Loads an image
     *
     * @param string $image filename
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function load($image)
    {
        if (!($this->imageHandle = imagick_readimage($image))) {
            $this->free();
            return null;
        }
        if (imagick_iserror($this->imageHandle)) {
            return null;
        }
        $this->uid = md5($_SERVER['REMOTE_ADDR']);
		
        $this->image = $image;
        $result = $this->_get_image_details($image);

        return true;
    } // End load

    /**
     * Resize Action
     *
     * @param int   $new_x   New width
     * @param int   $new_y   New height
     * @param mixed $options Optional parameters
     *
     * @return bool|PEAR_Error TRUE or PEAR_Error object on error
     * @access protected
     */
    function _resize($new_x, $new_y, $options = null)
    {
        if (!imagick_scale($this->imageHandle, $new_x, $new_y, "!")) {
            return null;
        }
		
        $this->new_x = $new_x;
        $this->new_y = $new_y;
        return true;

    } // End resize

    /**
     * Rotates the current image
     * Note: color mask are currently not supported
     *
     * @param   int     Rotation angle in degree
     * @param   array   No options are currently supported
     *
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function rotate($angle, $options = null)
    {
				
        if (($angle % 360) == 0) {
            return true;
        }
		if (!(imagick_rotate($this->imageHandle, $angle))) {
            return null;
        }

        $this->new_x = imagick_getwidth($this->imageHandle);
        $this->new_y = imagick_getheight($this->imageHandle);
        return true;

    } // End rotate

    /**
     * addText
     *
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function addText($text, $x, $y, $size, $color, $font)
    {
		global $webutler_config;
		
        $color = $color ? $this->colorarray2colorhex($color) : strtolower($color);
		$font = $webutler_config['server_path']."/includes/webfonts/".$font.".ttf";

        imagick_begindraw($this->imageHandle ) ;

		if (!imagick_setfillcolor($this->imageHandle, $color)) {
			return null;
		}
		if (!imagick_setfontsize($this->imageHandle, $size)) {
			return null;
		}
		if (!imagick_setfontface($this->imageHandle, $font)) {
			return null;
		}

        if (!imagick_drawannotation($this->imageHandle, $x, $y, $text)) {
            return null;
        }

        return true;

    } // End addText


    /**
     * Saves the image to a file
     *
     * @param $filename string the name of the file to write to
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function savetmp($filename)
    {		
        if (!imagick_writeimage($this->imageHandle, $filename)) {
            return null;
        }
        $this->free();
        return true;

    }
    function save($filename, $type = '', $quality = null)
    {
        if($type == 'jpg' || $type == 'jpeg') {
			$breite = imagick_getwidth($this->imageHandle);
			$hoehe = imagick_getheight($this->imageHandle);
			
			$handle = imagick_getcanvas("#FFFFFF", $breite, $hoehe);
			
			imagick_composite($handle, IMAGICK_COMPOSITE_OP_OVER, $this->imageHandle, 0, 0);
        }
		
        if($type == 'jpg' || $type == 'jpeg' || $type == 'png') {
            imagick_setcompressionquality($handle, $quality);
		}
		
        if (!imagick_writeimage($handle, $filename)) {
            return null;
        }
		
        return true;

    } // End save

    /**
     * Adjusts the image gamma
     *
     * @param float $outputgamma
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function gamma($outputgamma = 1.0) {
        if ($outputgamma != 1.0) {
            imagick_gamma($this->imageHandle, $outputgamma);
        }
        return true;
    }


    /**
     * Crops the image
     *
     * @param int width Cropped image width
     * @param int height Cropped image height
     * @param int x X-coordinate to crop at
     * @param int y Y-coordinate to crop at
     *
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function crop($x = 0, $y = 0, $width, $height)
    {
        if (!imagick_crop($this->imageHandle, $x, $y, $width, $height)) {
            return null;
        }

        $this->new_x = $x;
        $this->new_y = $y;

        return true;
    }


    /**
     * Watermark the image
     *
	*
	* 	imagick-function:
	*	function watermark($image, $opacitypercent, $x_offset, $y_offset)
	*	{
	*		$comp_handle = imagick_readimage($image);	
	*		$opacity_image = imagick_getimagefromlist($comp_handle);
	*		$cloned_image = imagick_clonehandle($opacity_image);
	*		
	*		imagick_destroyhandle($comp_handle);
	*		
	*		imagick_convert($opacity_image, "PNG");
	*		
	*		$opacity = sprintf("%01.2f", $opacitypercent/100);
	*		
	*		imagick_setfillopacity($opacity_image, $opacity);
	*		
	*		imagick_composite($this->imageHandle, IMAGICK_COMPOSITE_OP_OVER, $opacity_image, $x_offset, $y_offset);
	*		
	*		imagick_destroyhandle($opacity_image);
	*		
	*		return true;
	*	}
	*
     */
     

	function watermark($image, $opacitypercent, $x_offset, $y_offset)
	{
		$comp_handle = imagick_readimage($image);	
		$opacity_image = imagick_getimagefromlist($comp_handle);
		$cloned_image = imagick_clonehandle($opacity_image);
		
		imagick_destroyhandle($comp_handle);
		
		imagick_convert($opacity_image, "PNG");
		
		$opacity = sprintf("%01.2f", $opacitypercent/100);
		
		imagick_setfillopacity($opacity_image, $opacity);
		
		imagick_composite($this->imageHandle, IMAGICK_COMPOSITE_OP_OVER, $opacity_image, $x_offset, $y_offset);
		
		imagick_destroyhandle($opacity_image);
		
		return true;
	}
	
    /**
     * Horizontal mirroring
     *
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function mirror()
    {
        if (!imagick_flop($this->imageHandle)) {
            return null;
        }
        return true;
    }

    /**
     * Vertical mirroring
     *
     * @return bool|PEAR_Error TRUE or a PEAR_Error object on error
     * @access public
     */
    function flip()
    {
        if (!imagick_flip($this->imageHandle)) {
            return null;
        }
        return true;
    }

    /**
     * Destroy image handle
     *
     * @access public
     */
    function free()
    {
        if (is_resource($this->imageHandle)) {
            imagick_destroyhandle($this->imageHandle);
        }
        $this->imageHandle = null;
    }

} // End class Image_Transform_Driver_IMAGICK

?>
