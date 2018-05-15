<?php
class Image {
	private $file;
	private $image;
	private $width;
	private $height;
	private $bits;
	private $mime;

	public function __construct($file) {
		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->width  = $info[0];
			$this->height = $info[1];
			$this->bits = isset($info['bits']) ? $info['bits'] : '';
			$this->mime = isset($info['mime']) ? $info['mime'] : '';

			if ($this->mime == 'image/gif') {
				$this->image = imagecreatefromgif($file);
			} elseif ($this->mime == 'image/png') {
				$this->image = imagecreatefrompng($file);
			} elseif ($this->mime == 'image/jpeg') {
				$this->image = imagecreatefromjpeg($file);
			}
		} else {
			exit('Error: Could not load image ' . $file . '!');
		}
	}

	public function getFile() {
		return $this->file;
	}

	public function getImage() {
		return $this->image;
	}

	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}

	public function getBits() {
		return $this->bits;
	}

	public function getMime() {
		return $this->mime;
	}

	public function save($file, $quality = 90) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif ($extension == 'png') {
				imagepng($this->image, $file);
			} elseif ($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}

	public function resize($width = 0, $height = 0, $default = '') {
		if (!$this->width || !$this->height) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->width;
		$scale_h = $height / $this->height;

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h') {
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->mime != 'image/png') {
			return;
		}

		$new_width = (int)($this->width * $scale);
		$new_height = (int)($this->height * $scale);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if ($this->mime == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $width;
		$this->height = $height;
	}
// Begin WaterMark - from the modification "WaterMark by iSenseLabs"
    
                public function iwatermark(&$setting) {
                    if ($setting['Enabled'] != 'true') {
                        return;
                    }
                    
                    $text_size = $setting['FontSize'];
                    
                    $font = dirname(DIR_SYSTEM) . '/vendors/iwatermark/font/'.$setting['Font'];
                    
                    if ($setting['Type'] == 'text') {
                        $text = $setting['Text'];
                        
                        $size = imagettfbbox($text_size, 0, $font, $text);
                    
                        $watermark_width = ($size[4] - $size[6]) + 10;
                        $watermark_height = 1.2 * ($size[1] - $size[7]) + 20;
                        
                        $im = imagecreatetruecolor($watermark_width, $watermark_height);
                        $backgroundalpha = imagecolorallocatealpha($im,0xFF,0xFF,0xFF,127); 
                        $coloralpha = imagecolorallocatealpha($im,$setting['ColorRGB']['r'],$setting['ColorRGB']['g'],$setting['ColorRGB']['b'],round(127*(100-$setting['Opacity'])/100)); 
                        
                        imagefill($im, 0, 0, $backgroundalpha);
                        
                        if (function_exists('imagettftext')) {
                            imagettftext($im, $text_size, 0, 0, $text_size + 15, $coloralpha, $font, $text);
                        } else {
                            imagestring($im, 5, 5, 5, $text, $coloralpha);
                        }
                        
                        $setting['UseImageOpacity'] = true;
                    } else if ($setting['Type'] == 'image') {
                        $type = strtolower(substr($setting['ImagePath'], strrpos($setting['ImagePath'], '.') + 1));
                        if ($type == 'jpg' || $type == 'jpeg') $im = imagecreatefromjpeg($setting['ImagePath']);
                        else if ($type == 'png') $im = imagecreatefrompng($setting['ImagePath']);
                        
                        if (isset($im)) {
                            $watermark_width = imagesx($im);
                            $watermark_height = imagesy($im);
                        
                            if (empty($setting['UseImageOpacity'])) {
                                $background = imagecreatetruecolor($watermark_width, $watermark_height);
                                $white = imagecolorallocatealpha($background, 0xFF, 0xFF, 0xFF, 127);
                                imagefill($background, 0, 0, $white);
                                
                                imagecopy($background, $im, 0, 0, 0, 0, $watermark_width, $watermark_height);
                                $im = $background;
                            }
                        }
                    }
                    
                    // Rotate watermark
                    $im = imagerotate($im, (int)$setting['Rotation'], imagecolorallocatealpha($im,0xFF,0xFF,0xFF,127));
                    
                    $watermark_width = imagesx($im);
                    $watermark_height = imagesy($im);
                    
                    //Version check     
                    if (VERSION >= '2.1.0.1') {
                        $WatermarkImageWidth = $this->width;
                        $WatermarkImageHeight = $this->height;
                    } else {
                        $WatermarkImageWidth = $this->info['width'];
                        $WatermarkImageHeight = $this->info['height'];
                    }
                    
                    if (empty($WatermarkImageWidth) || empty($WatermarkImageHeight)) {
                        return;
                    }

                    // Resize watermark
                    $imageWidth = $WatermarkImageWidth;
                    $imageHeight = $WatermarkImageHeight;
                    
                    if ($imageWidth < $watermark_width || $imageHeight < $watermark_height) {
                        if ($imageWidth < $watermark_width && $imageHeight < $watermark_height) {
                            if ($watermark_width > $watermark_height) {
                                $watermark_proportion = $watermark_width / $watermark_height;
                                $watermark_new_width = $imageWidth;
                                $watermark_new_height = $watermark_new_width / $watermark_proportion;
                            } else {
                                $watermark_proportion = $watermark_height / $watermark_width;
                                $watermark_new_height = $imageHeight;
                                $watermark_new_width = $watermark_new_height/$watermark_proportion;
                            }
                        } else if ($imageWidth < $watermark_width) {
                            $watermark_proportion = $watermark_width / $watermark_height;
                            $watermark_new_width = $imageWidth;
                            $watermark_new_height = $watermark_new_width / $watermark_proportion;
                        } else if ($imageHeight < $watermark_height) {
                            $watermark_proportion = $watermark_height / $watermark_width;
                            $watermark_new_height = $imageHeight;
                            $watermark_new_width = $watermark_new_height/$watermark_proportion;
                        }
                        
                        $im_new = imagecreatetruecolor($watermark_new_width, $watermark_new_height);
                        $coloralpha = imagecolorallocatealpha($im_new,0xFF,0xFF,0xFF,127); 
                        imagefill($im_new, 0, 0, $coloralpha);
                        imagecopyresampled($im_new, $im, 0, 0, 0, 0, $watermark_new_width, $watermark_new_height, $watermark_width, $watermark_height);
                        $watermark_width = $watermark_new_width;
                        $watermark_height = $watermark_new_height;
                        $im = $im_new;
                    }
                    
                    if (isset($im)) {
                        switch($setting['Position']) {
                            case 'top_left':
                                $watermark_pos_x = 0;
                                $watermark_pos_y = 0;
                                break;
                            case 'top_center':
                                $watermark_pos_x = floor((VERSION >= '2.1.0.1' ? $this->width : $this->info['width'])/2 - $watermark_width/2);
                                $watermark_pos_y = 0;
                                break;
                            case 'top_right':
                                $watermark_pos_x = (VERSION >= '2.1.0.1' ? $this->width : $this->info['width']) - $watermark_width;
                                $watermark_pos_y = 0;
                                break;
                            case 'right_center':
                                $watermark_pos_x = (VERSION >= '2.1.0.1' ? $this->width : $this->info['width']) - $watermark_width;
                                $watermark_pos_y = floor((VERSION >= '2.1.0.1' ? $this->height : $this->info['height'])/2 - $watermark_height/2);
                                break;
                            case 'center':
                                $watermark_pos_x = floor($WatermarkImageWidth/2 - $watermark_width/2);
                                $watermark_pos_y = floor($WatermarkImageHeight/2 - $watermark_height/2);
                                break;
                            case 'left_center':
                                $watermark_pos_x = 0;
                                $watermark_pos_y = floor((VERSION >= '2.1.0.1' ? $this->height : $this->info['height'])/2 - $watermark_height/2);
                                break;
                            case 'bottom_left':
                                $watermark_pos_x = 0;
                                $watermark_pos_y = $WatermarkImageHeight - $watermark_height;
                                break;
                            case 'bottom_center':
                                $watermark_pos_x = floor((VERSION >= '2.1.0.1' ? $this->width : $this->info['width'])/2 - $watermark_width/2);
                                $watermark_pos_y = (VERSION >= '2.1.0.1' ? $this->height : $this->info['height']) - $watermark_height;
                                break;
                            case 'bottom_right':
                                $watermark_pos_x = $WatermarkImageWidth - $watermark_width;
                                $watermark_pos_y = $WatermarkImageHeight - $watermark_height;
                                break;
                        }
                        
                        if ((VERSION >= '2.1.0.1' ? $this->mime : $this->info['mime']) == 'image/png') {
                            // Create a white background, the same size as the original.
                            $background = imagecreatetruecolor((VERSION >= '2.1.0.1' ? $this->width : $this->info['width']), (VERSION >= '2.1.0.1' ? $this->height : $this->info['height']));
                            $white = imagecolorallocate($background, 255, 255, 255);
                            imagefill($background, 0, 0, $white);

                            // Merge the two images.
                            imagecopyresampled(
                                $background, $this->image,
                                0, 0, 0, 0,
                                (VERSION >= '2.1.0.1' ? $this->width : $this->info['width']), (VERSION >= '2.1.0.1' ? $this->height : $this->info['height']),
                                (VERSION >= '2.1.0.1' ? $this->width : $this->info['width']), (VERSION >= '2.1.0.1' ? $this->height : $this->info['height']));

                            imagedestroy($this->image);
                            $this->image = $background;
                        }
                        
                        imagealphablending($this->image, true);
                        imagealphablending($im, true);
                        
                        if (!empty($setting['UseImageOpacity'])) {
                            imagecopy($this->image, $im, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
                        } else {
                            imagecopymerge($this->image, $im, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height, $setting['Opacity']);
                        }

                        imagedestroy($im);
                    }
                }
                
                // End WaterMark - from the modification "WaterMark by iSenseLabs"
            
	public function watermark($watermark, $position = 'bottomright') {
		switch($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = 0;
				break;
			case 'middleleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middlecenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middleright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
		}
		
		imagealphablending( $this->image, true );
		imagesavealpha( $this->image, true );
		imagecopy($this->image, $watermark->getImage(), $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());

		imagedestroy($watermark->getImage());
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $bottom_x - $top_x;
		$this->height = $bottom_y - $top_y;
	}

	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	private function filter() {
        $args = func_get_args();

        call_user_func_array('imagefilter', $args);
	}

	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	private function merge($merge, $x = 0, $y = 0, $opacity = 100) {
		imagecopymerge($this->image, $merge->getImage(), $x, $y, 0, 0, $merge->getWidth(), $merge->getHeight(), $opacity);
	}

	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}
}
