<?php

if (!function_exists('imagerotate')) {

	if(class_exists('Imagick')):
		function imagerotate( $source_image, $angle, $bgd_color='black' ) {
			if($angle < 0) 360+($angle%360);
	        $angle = 360-$angle; // GD rotates CCW, imagick rotates CW
	        
	        $temp_src = tempnam(sys_get_temp_dir(), 'temp_src');//dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR.'temp_src'.microtime(true).'.png';
	        $temp_dst = tempnam(sys_get_temp_dir(), 'temp_dst');//dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR.'temp_dst'.microtime(true).'.png';
	        if (!imagepng($source_image,$temp_src)){
	            return false;
	        }
	        $imagick = new Imagick();
	        $imagick->readImage($temp_src);
	        $imagick->rotateImage(new ImagickPixel($bgd_color?$bgd_color:'black'), $angle);
	        $imagick->writeImage($temp_dst);
	        //trigger_error( 'imagerotate(): could not write to ' . $file1 . ', original image returned', E_USER_WARNING );
	        $result = imagecreatefrompng($temp_dst);
	        unlink($temp_dst);
	        unlink($temp_src);
	        return $result;
	    }
	else:
	    function imagerotate_rotateX($x, $y, $theta) {
	        return $x * cos($theta) - $y * sin($theta);
	    }
	
	    function imagerotate_rotateY($x, $y, $theta) {
	        return $x * sin($theta) + $y * cos($theta);
	    }
	
	    function imagerotate($srcImg, $angle, $bgcolor = 0, $ignore_transparent = 0) {
	        $srcw = imagesx($srcImg);
	        $srch = imagesy($srcImg);
	
	        //Normalize angle
	        $angle %= 360;
	        //Set rotate to clockwise
	        $angle = -$angle;
	
	        if ($angle == 0) {
	            if ($ignore_transparent == 0) {
	                imagesavealpha($srcImg, true);
	            }
	            return $srcImg;
	        }
	
	        // Convert the angle to radians
	        $theta = deg2rad($angle);
	
	        //Standart case of rotate
	        if ((abs($angle) == 90) || (abs($angle) == 270)) {
	            $width = $srch;
	            $height = $srcw;
	            if (($angle == 90) || ($angle == -270)) {
	                $minX = 0;
	                $maxX = $width;
	                $minY = -$height+1;
	                $maxY = 1;
	            } else if (($angle == -90) || ($angle == 270)) {
	                $minX = -$width+1;
	                $maxX = 1;
	                $minY = 0;
	                $maxY = $height;
	            }
	        } else if (abs($angle) === 180) {
	            $width = $srcw;
	            $height = $srch;
	            $minX = -$width+1;
	            $maxX = 1;
	            $minY = -$height+1;
	            $maxY = 1;
	        } else {
	            // Calculate the width of the destination image.
	            $temp = array(
	                imagerotate_rotateX(0, 0, 0 - $theta),
	                imagerotate_rotateX($srcw, 0, 0 - $theta),
	                imagerotate_rotateX(0, $srch, 0 - $theta),
	                imagerotate_rotateX($srcw, $srch, 0 - $theta)
	            );
	            $minX = floor(min($temp));
	            $maxX = ceil(max($temp));
	            $width = $maxX - $minX;
	
	            // Calculate the height of the destination image.
	            $temp = array(
	                imagerotate_rotateY(0, 0, 0 - $theta),
	                imagerotate_rotateY($srcw, 0, 0 - $theta),
	                imagerotate_rotateY(0, $srch, 0 - $theta),
	                imagerotate_rotateY($srcw, $srch, 0 - $theta)
	            );
	            $minY = floor(min($temp));
	            $maxY = ceil(max($temp));
	            $height = $maxY - $minY;
	        }
	
	        $destimg = imagecreatetruecolor($width, $height);
	        if ($ignore_transparent == 0) {
	            imagefill($destimg, 0, 0, imagecolorallocatealpha($destimg, 255,255, 255, 127));
	            imagesavealpha($destimg, true);
	        }
	
	        // sets all pixels in the new image
	        for ($x = $minX; $x < $maxX; $x++) {
	            for ($y = $minY; $y < $maxY; $y++) {
	                // fetch corresponding pixel from the source image
	                $srcX = round(imagerotate_rotateX($x, $y, $theta));
	                $srcY = round(imagerotate_rotateY($x, $y, $theta));
	                if ($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
	                    $color = imagecolorat($srcImg, $srcX, $srcY);
	                } else {
	                    $color = $bgcolor;
	                }
	                imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
	            }
	        }
	
	        return $destimg;
	    }
	endif;
}


class SimpleImage {
	
	var $image;
	var $image_type;
 
 	function __construct($file=null){
 		if(is_string($file))
			$this->load($file);
		elseif($file!==null)
			$this->set_image($file);
 	}
 	
 	function set_image($img) {
 		$this->image = $img;
		$this->image_type = IMAGETYPE_PNG;
		return $this;
 	}
	 
	function load($filename) {
		if(strstr($filename, 'http') !== 0 && !file_exists($filename))
			return trigger_error("SimpleImage unable to read file: $filename ", E_USER_WARNING);
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
		return $this;
 	}
	function get_loaded() {
		return $this->image;
	}
	function get_file() {
		return $this->get_loaded();
	}
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

        // do this or they'll all go to jpeg
        $image_type=$this->image_type;

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            // need this for transparent png to work
            imagealphablending($this->image, false);
            imagesavealpha($this->image,true);
            imagepng($this->image,$filename);
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }
	function output($image_type=IMAGETYPE_PNG,$set_header=false) {
		if($set_header) $this->set_header($image_type);
		if( $image_type == IMAGETYPE_JPEG || $image_type=='jpeg' || $image_type == 'jpg' ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF || $image_type=='gif' ) {
			imagegif($this->image);			
		} elseif( $image_type == IMAGETYPE_PNG || $image_type == 'png') {
			imagepng($this->image);
		}	
	}
	function set_header($image_type=IMAGETYPE_PNG) {
		if(headers_sent()) return false;
		if( $image_type == IMAGETYPE_JPEG || $image_type=='jpeg' || $image_type == 'jpg' ) {
			header("Content-Type: image/jpg");
			return true;
		} elseif( $image_type == IMAGETYPE_GIF || $image_type=='gif' ) {
			header("Content-Type: image/gif");
			return true;
		} elseif( $image_type == IMAGETYPE_PNG || $image_type == 'png') {
			header("Content-Type: image/png");
			return true;
		}
		return false;
	}
	function getWidth() {
		return imagesx($this->image);
	}
	function getHeight() {
		return imagesy($this->image);
	}
	function resizeToHeight($height) {
		if($height > $this->getHeight()) return false;
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
		return $this;
 	}
	function resizeToWidth($width) {
		if($width>$this->getWidth()) return false;
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width,$height);
		return $this;
 	}
	function scale($scale) {
		if(!is_float($scale)) $scale /= 100;
		$width = $this->getWidth() * $scale;
		$height = $this->getHeight() * $scale; 
		$this->resize($width,$height);
		return $this;
 	}
    function resize($width,$height,$forcesize='n') {

        /* optional. if file is smaller, do not resize. */
        if ($forcesize == 'n') {
            if ($width > $this->getWidth() && $height > $this->getHeight()){
                $width = $this->getWidth();
                $height = $this->getHeight();
            }
        }

        $new_image = imagecreatetruecolor($width, $height);
        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($this->image_type == IMAGETYPE_GIF) || ($this->image_type==IMAGETYPE_PNG)){
            imagealphablending($new_image, false);
            imagesavealpha($new_image,true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
        }
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        $this->image = $new_image;
    }

        function crop_center($width,$height){
		$x = intval(($this->getWidth()-$width)/2);
		$y = intval(($this->getHeight()-$height)/2);
		$this->crop($x,$y,$width,$height);
		return $this;
 	}
	
	function crop($x,$y=0,$width=NULL,$height=NULL){
		if(is_array($x)){
			$y = $x['y'];
			$width = $x['w'];
			$height = $x['h'];
			$x = $x['x'];
		}
		$mW = $this->getWidth()-$x;
		$mH = $this->getHeight()-$y;
		if($width === NULL || $width > $mW) $width = $mW;
		if($height === NULL || $height > $mH) $height = $mH;
		$new_image = imagecreatetruecolor($width, $height);
		imagealphablending($new_image, false);
		imagesavealpha($new_image,true);
		$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
		imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $width, $height, $width, $height);
		$this->image = $new_image;	
		return $this;
 	}
	

	function sample($mark, $x, $y)
	{
		imagealphablending($this->image, true);
		imagesavealpha($this->image, true);
		imagecopy($this->image, $mark->get_loaded(), $x, $y, 0, 0, $mark->getWidth(), $mark->getHeight());
		return $this;
 	}
	
	function text($text, $font, $font_size=10, $x=0, $y=0, $angle=0, $color=array('r'=>255,'g'=>255,'b'=>255), $align = 'left', $lineheight=1 )
	{
		if(!function_exists('imagettftext'))	throw new Exception('SimpleImage->text: function imagettftext doese not exists');
		if(!file_exists($font))					throw new Exception('SimpleImage->text: fontfile '.$font.' dose not exist');
		if(!is_array($color))$color = array();
		$color = $this->c(array_merge(array('r'=>255, 'g'=>255, 'b'=>255),$color));
		$align = strtolower($align);
		
		
		$text_w = $this->text_w($font_size, $angle, $font, $text);
		//FW::log($text_w,'width');
		$x += ($align=='left'?0:($align=='right'?$text_w[2]:ceil($text_w[2]/2)));
		$text = str_replace("\n\r", "\n", $text);
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);
		$text_a = explode("\n", $text);
		$lineOff = 0;
		for($i=0;$i<count($text_a);$i++){
			$txt = $text_a[$i];
			if(strlen($txt)==0) $txt = ' ';
			if($i>0) $lineOff += $this->text_nl_spacing($font_size, $font, $txt);
			$txt_h = $this->text_h($font_size, $angle, $font, $txt);
			$txt_w = $this->text_w($font_size, $angle, $font, $txt);
			$yOff = $txt_h[0]+$lineOff;
			$xOff = ($align=='left'?0:($align=='right'?$txt_w[2]:ceil($txt_w[2]/2)))*-1;
			if(strlen($txt))
				imagettftext($this->image, $font_size, $angle, $x+$xOff, $y+$yOff, $color, $font, $txt);
			$lineOff += $txt_h[0];
		}
		return $this;
 	}
	
	function text_nl_spacing($fontsize, $font, $text='o'){
		$a = $this->text_h($fontsize, 0, $font, "o\n".$text);
		$h = $this->text_h($fontsize, 0, $font, $text);
		$b = $this->text_h($fontsize, 0, $font, "o");
		return $a[2] - $b[2] - $h[2];
	}
	function text_h($fontsize, $rot, $font, $txt){
		if(strlen($txt)==0) $txt = ' ';
		$bbox_tmp = imageftbbox($fontsize, $rot, $font, $txt);
		$h1 = abs(min(array($bbox_tmp[1],$bbox_tmp[3],$bbox_tmp[5],$bbox_tmp[7])));
		$h2 = abs(max(array($bbox_tmp[1],$bbox_tmp[3],$bbox_tmp[5],$bbox_tmp[7])));
		$h3 = $h1+$h2;
		return array($h1, $h2, $h3);
	}
	function text_w($fontsize, $rot, $font, $txt){
		if(strlen($txt)==0) $txt = ' ';
		$bbox_tmp = imageftbbox($fontsize, $rot, $font, $txt);
		$w1 = abs(min(array($bbox_tmp[0],$bbox_tmp[2],$bbox_tmp[4],$bbox_tmp[6])));
		$w2 = abs(max(array($bbox_tmp[0],$bbox_tmp[2],$bbox_tmp[4],$bbox_tmp[6])));
		$w3 = $w1+$w2;
		return array($w1, $w2, $w3);
		
	}
	
	
	function rgba($r,$g,$b,$a){
		return array(
			'r'	=> ($r>255?$r%255:$r),
			'g'	=> ($g>255?$g%255:$g),
			'b'	=> ($b>255?$b%255:$b),
			'a'	=> ($a/255)*127 
		);
	}
	function rgb($r,$g,$b){
		return array(
			'r'	=> ($r>255?$r%255:$r),
			'g'	=> ($g>255?$g%255:$g),
			'b'	=> ($b>255?$b%255:$b) 
		);
	}
	
	function c($color){
		if(isset($color['a']))
			return imagecolorallocatealpha($this->image, $color['r'], $color['g'], $color['b'], $color['a']);
		else
			return imagecolorallocate($this->image, $color['r'], $color['g'], $color['b']);
	}
	
	function draw_line($x1, $y1, $x2, $y2, $color){
		return imageline($this->image, $x1, $y1, $x2, $y2, $this->c($color));
	}
	
	function create_canvas($width=10,$height=10,$color='alpha'){
		
		if(!is_array($color) && $color != 'alpha'){
			$color = array();
			$color = array_merge(array('r'=>255, 'g'=>255, 'b'=>255),$color);
		}
		$new_image = imagecreatetruecolor($width, $height);
		if($color == 'alpha'){
			imagealphablending($new_image, false);
			imagesavealpha($new_image,true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		}else{
			$backgroundcolor = imagecolorallocate($new_image, $color['r'], $color['g'], $color['b']);
			imagefill($new_image, 0, 0, $backgroundcolor);
		}
		$this->image = $new_image;
		return $this;
 	}
	
	function new_canvas($padding=array('left'=>0,'right'=>0,'top'=>0,'bottom'=>0),$color=array('r'=>255,'g'=>255,'b'=>255)) {
		$offset = $padding;
		if(!is_array($offset))$offset = array();
			$offset = array_merge(array('left'=>0, 'right'=>0, 'top'=>0, 'bottom'=>0), $offset);
		if(!is_array($color) && $color != 'alpha')
		{
			$color = array();
			$color = array_merge(array('r'=>255, 'g'=>255, 'b'=>255),$color);
		}
		$width = $this->getWidth() + $offset['left'] + $offset['right'];
		$height = $this->getHeight() + $offset['top'] + $offset['bottom'];
		$new_image = imagecreatetruecolor($width, $height);
		if($color == 'alpha'){
			imagealphablending($new_image, false);
			imagesavealpha($new_image,true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		}else{
			$backgroundcolor = imagecolorallocate($new_image, $color['r'], $color['g'], $color['b']);
			imagefill($new_image, 0, 0, $backgroundcolor);
		} 
		imagecopyresampled(
			$new_image, $this->image, 
			$offset['left'], $offset['top'], 0, 0, 
			$this->getWidth(), $this->getHeight(), 
			$this->getWidth(), $this->getHeight());
		$this->image = $new_image;	
		return $this;
 	}
	
	public function __clone() {
        $new = new SimpleImage();
		$new->create_canvas($this->getWidth(),$this->getHeight());
		$new->merge($this);
		return $new;
    }
	
	function blur() {
		$imagex = $this->getWidth();
	    $imagey = $this->getHeight();
	    $dist = 5;
	
	    for ($x = 0; $x < $imagex; ++$x) {
	        for ($y = 0; $y < $imagey; ++$y) {
				$newr = 0;
				$newg = 0;
				$newb = 0;
				$colours = array();
				$thiscol = imagecolorat($this->image, $x, $y);
				for ($k = $x - $dist; $k <= $x + $dist; ++$k) {
					for ($l = $y - $dist; $l <= $y + $dist; ++$l) {
						if ($k < 0) { $colours[] = $thiscol; continue; }
						if ($k >= $imagex) { $colours[] = $thiscol; continue; }
						if ($l < 0) { $colours[] = $thiscol; continue; }
						if ($l >= $imagey) { $colours[] = $thiscol; continue; }
						$colours[] = imagecolorat($this->image, $k, $l);
					}
				}
				
				foreach($colours as $colour) {
					$newr += ($colour >> 16) & 0xFF;
					$newg += ($colour >> 8) & 0xFF;
					$newb += $colour & 0xFF;
				}
				$numelements = count($colours);
				$newr /= $numelements;
				$newg /= $numelements;
				$newb /= $numelements;
				$newcol = imagecolorallocate($this->image, $newr, $newg, $newb);
				imagesetpixel($this->image, $x, $y, $newcol);
			}
		}
		return $this;
 	}

	function create_watermark($watermark, $alpha = 100, $upper=false, $left=false, $padding = 0) {
		$alpha_level			= $alpha/100;	# convert 0-100 (%) alpha to decimal
		$main_img_obj			= $this->image;
		$watermark_img_obj		= $watermark->get_loaded();
		$main_img_obj_w			= imagesx( $main_img_obj );
		$main_img_obj_h			= imagesy( $main_img_obj );
		$watermark_img_obj_w	= imagesx( $watermark_img_obj );
		$watermark_img_obj_h	= imagesy( $watermark_img_obj );
		
		if($left)
			$main_img_obj_min_x = 0 + $padding;
		else
			$main_img_obj_min_x = $main_img_obj_w - $watermark_img_obj_w - $padding;
			
		if($upper)
			$main_img_obj_min_y = 0 + $padding;
		else
			$main_img_obj_min_y = $main_img_obj_h - $watermark_img_obj_h - $padding;
		$this->merge($watermark, $main_img_obj_min_x, $main_img_obj_min_y, $alpha);
		return $this;
 	}
	
	
	function merge($mark, $x = 'center', $y = 'center', $alpha = 100) {
		$alpha_level			= $alpha/100;	# convert 0-100 (%) alpha to decimal
		$main_img_obj			= $this->image;
		$watermark_img_obj		= $mark->get_loaded();
		$main_img_obj_w			= imagesx( $main_img_obj );
		$main_img_obj_h			= imagesy( $main_img_obj );
		$watermark_img_obj_w	= imagesx( $watermark_img_obj );
		$watermark_img_obj_h	= imagesy( $watermark_img_obj );
		if($x === 'center')
			$x = floor(($main_img_obj_w - $watermark_img_obj_w)/2);
		if($y === 'center')
			$y = floor(($main_img_obj_h - $watermark_img_obj_h)/2);
		if($alpha = 100) return $this->sample($mark, $x, $y);
			
		$main_img_obj_min_x = $x;
		$main_img_obj_min_y = $y;
		
		$return_img	= imagecreatetruecolor( $main_img_obj_w, $main_img_obj_h );
		
		//build some aaplpha settings
		imagealphablending($return_img, false);
		imagesavealpha($return_img,true);
		$transparent = imagecolorallocatealpha($return_img, 255, 255, 255, 127);
		imagefilledrectangle($return_img, 0, 0, $main_img_obj_w, $main_img_obj_h, $transparent);
		
		for( $y = 0; $y < $main_img_obj_h; $y++ ) {
			for( $x = 0; $x < $main_img_obj_w; $x++ ) {
				$return_color	= NULL;
				
				$watermark_x	= $x - $main_img_obj_min_x;
				$watermark_y	= $y - $main_img_obj_min_y;
				
				$main_rgb = imagecolorsforindex( $main_img_obj, imagecolorat( $main_img_obj, $x, $y ) );
				
				
				if(	$watermark_x	>= 0 && $watermark_x < $watermark_img_obj_w &&
					$watermark_y	>= 0 && $watermark_y < $watermark_img_obj_h ) {
					$watermark_rbg	= imagecolorsforindex( $watermark_img_obj, imagecolorat( $watermark_img_obj, $watermark_x, $watermark_y ) );
					$watermark_alpha	= round( ( ( 127 - $watermark_rbg['alpha'] ) / 127 ), 2 );
					$watermark_alpha	= $watermark_alpha * $alpha_level;
					$avg_red		= $this->_get_ave_color( $main_rgb['red'],	$watermark_rbg['red'],		$watermark_alpha );
					$avg_green		= $this->_get_ave_color( $main_rgb['green'],$watermark_rbg['green'],	$watermark_alpha );
					$avg_blue		= $this->_get_ave_color( $main_rgb['blue'],	$watermark_rbg['blue'],		$watermark_alpha );
					$return_color	= $this->_get_image_color( $return_img, $avg_red, $avg_green, $avg_blue );
				} else {
					$return_color	= imagecolorat( $main_img_obj, $x, $y );
				}
				imagesetpixel( $return_img, $x, $y, $return_color );
			}
		}
		$this->image = $return_img;
		return $this;
 	}
	
	function rotate($deg,$safe_size = true,$background='transparent'){
		$w = $this->getWidth();
		$h = $this->getHeight();
		$this->image = imagerotate($this->image, $deg, $background);
		if($safe_size)
			$this->crop_center($w, $h);
		return $this;
 	}
	
	function asImagick(){
		if(!class_exists('Imagick'))
			return FALSE;
		ob_start();
		imagepng($this->image);
		$im = new Imagick();
		$im->readImageBlob(ob_get_contents());
		ob_end_clean();
		return $im;
		
		// imagepng($this->image,"php://memory/simpleimage.png");
	    // $im = new Imagick();
		// $im->readimage("php://memory/simpleimage.png");
		// return $im;
	}
	
	function fromImagick($im){
		ob_start();
	    echo $im;
	    $rawim = ob_get_contents();
	    ob_end_clean();
		$this->image = imagecreatefromstring($rawim);
	}
	
	/**
	* creates a GD destination object and projects each corner of the $source to the specified position on the destination 
	* $destination_width=max( $a['x'], $b['x'], $c['x'], $y['x']);
	* $destination_height=max( $a['y'], $b['y'], $c['y'], $y['y']);
	* @param array( x float, y float) $a*, destination point of the upper left source 
	* @param array( x float, y float) $b*, destination point of the upper right source 
	* @param array( x float, y float) $c*, destination point of the lower right source 
	* @param array( x float, y float) $d*, destination point of the lower left source
	* @return GD-Object the destination object; 
	*/
	function  transform($a,$b,$c,$d){
		
	    $swidth = $this->getWidth(); 
	    $sheight = $this->getHeight();
		
	    $minx = min($a['x'], $b['x'], $c['x'], $d['x']);
	    $maxx = max($a['x'], $b['x'], $c['x'], $d['x']);
	    $miny = min($a['y'], $b['y'], $c['y'], $d['y']);
	    $maxy = max($a['y'], $b['y'], $c['y'], $d['y']);
	    $diffx = $maxx - $minx;
	    $diffy = $maxy - $miny;
	        
	    
	    $scalex = $swidth / $diffx;
	    $scaley = $swidth / $diffy;    
	        
	    $ax = round(($a['x'] - $minx) * $scalex);
	    $ay = round(($a['y'] - $miny) * $scaley);
	    $bx = round(($b['x'] - $minx) * $scalex);
	    $by = round(($b['y'] - $miny) * $scaley);
	    $cx = round(($c['x'] - $minx) * $scalex);
	    $cy = round(($c['y'] - $miny) * $scaley);
	    $dx = round(($d['x'] - $minx) * $scalex);
	    $dy = round(($d['y'] - $miny) * $scaley);    
	    
	    // ob_start();
	    // imagepng($source);
	    // $rawim = ob_get_contents();
	    // ob_end_clean();
	    
	    // imagepng($source,"php://memory");
	    // $im = new Imagick();
	    
	    /* @var $im  Imagick*/
		$im = $this->asImagick();
		
	    //$im->readImageBlob($rawim, 'icon.png');
	    
	    /* Fill background area with transparent */
	    $im->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
	    // /* Activate matte */
	    $im->setImageMatte(true);
	    // Fix for older versions of ImageMagick known to have a distort bug.
	    $im->borderImage(new ImagickPixel("none"), 1, 1);
	
	    /* Control points for the distortion */
	    $controlPoints = array( 0, 0,
	                            $ax, $ay,
	                            
	                            0, $sheight,
	                            $dx, $dy,
	                            
	                            $swidth, 0,
	                            $bx, $by,
	                        
	                            $swidth, $sheight,
	                            $cx, $cy);
	
	
	        
	    /* Perform the distortion */ 
	    $im->distortImage(Imagick::DISTORTION_PERSPECTIVE, $controlPoints, true);
	    $im->scaleImage($diffx, $diffy);
	    // ob_start();
	    // echo $im;
	    // $rawim = ob_get_contents();
	    // ob_end_clean();
	    $this->fromImagick($im);
	    $im->destroy();
		return $this;
 	}
	
	function _get_ave_color( $color_a, $color_b, $alpha_level ) {
		return round( ( ( $color_a * ( 1 - $alpha_level ) ) + ( $color_b * $alpha_level ) ) );
	}
	
	function _get_image_color($im, $r, $g, $b) {
		$c=imagecolorexact($im, $r, $g, $b);
		if ($c!=-1) return $c;
		$c=imagecolorallocate($im, $r, $g, $b);
		if ($c!=-1) return $c;
		return imagecolorclosest($im, $r, $g, $b);
	}
}
?>