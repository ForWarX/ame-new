<?php

class ModelExtensionModuleIwatermark extends Model {
	public function initSessionClean() {
		// Read all persisted settings and populate the session if necessary.

		if (!empty($this->session->data['iwatermark_clean'])) {
			unset($this->session->data['iwatermark_clean']);
		}

		$this->session->data['iwatermark_clean']['settings'] = array();
		$this->session->data['iwatermark_clean']['progress'] = null;

		$dir = dirname(DIR_APPLICATION) . '/vendors/iwatermark/clean/';

		clearstatcache(true);

		$dh = opendir($dir);

		while (false !== ($entry = readdir($dh))) {
			if (in_array($entry, array('.', '..', 'index.html'))) {
			    continue;
			}

			$item = $dir . DIRECTORY_SEPARATOR . $entry;

			if (is_file($item) && is_readable($item)) {
				$data = json_decode(file_get_contents($item), true);

				$this->session->data['iwatermark_clean']['settings'][] = $data;
			}
		}

		closedir($dh);
	}

	public function cleanFinalize() {
		// Unlink all persisted settings because we are sure the cache cleaning is finished.
		$dir = dirname(DIR_APPLICATION) . '/vendors/iwatermark/clean/';

		clearstatcache(true);

		$dh = opendir($dir);

		while (false !== ($entry = readdir($dh))) {
			if (in_array($entry, array('.', '..', 'index.html'))) {
			    continue;
			}

			$item = $dir . DIRECTORY_SEPARATOR . $entry;

			if (is_file($item) && is_writable($item)) {
				@unlink($item);
			}
		}

		closedir($dh);

		unset($this->session->data['iwatermark_clean']);
	}

	public function persistSettingForCleaning($setting) {
		// The passed setting is persisted in a file in case the customer stop the process in the middle. This way we can stack many different setting histories and clean all relevant image/cache files only once if necessary.

		$dir = dirname(DIR_APPLICATION) . '/vendors/iwatermark/clean/';

		$data = json_encode($setting);

		$filename = md5($data);

		@file_put_contents($dir . '/' . $filename, $data);
	}

	public function cleanInitProgress() {
		// Initialize the cleaning progress. Fill the session variable $this->session->data['iwatermark_clean']['progress'] with all existing product groups.

		$result = array();

		$this->load->helper('vendor/isenselabs/watermark/OpenCartImageSymlink');

		$imageSymlink = new OpenCartImageSymlink($this->registry);

		if (false !== $dir = $imageSymlink->getGroupDir()) {
			$regex = '~(\d+-\d+)~i';

			clearstatcache(true);

			$dh = opendir($dir);
			
			$dir_image = realpath(DIR_IMAGE);

			while (false !== ($entry = readdir($dh))) {
				if (in_array($entry, array('.', '..'))) {
				    continue;
				}

				$real_item = $dir . DIRECTORY_SEPARATOR . $entry;
				$item = $dir_image . DIRECTORY_SEPARATOR . 'cache' . substr($real_item, strlen($dir_image));

				if (preg_match($regex, $entry) && is_dir($item) && is_writable($item)) {
					$result[] = $item;
				}
			}

			closedir($dh);
		}

		$this->session->data['iwatermark_clean']['progress'] = $result;
	}

	public function cleanProceed() {
		// Proceed with this step of the cleaning. Get the last item in $this->session->data['iwatermark_clean']['progress'], which is a product_id group in the form 1-1000. Find every product ID in this directory and clean its cache according to the settings.

		$progress = $this->session->data['iwatermark_clean']['progress'];

		$dir = $progress[count($progress) - 1];

		clearstatcache(true);

		$dh = opendir($dir);

		while (false !== ($entry = readdir($dh))) {
			if (in_array($entry, array('.', '..'))) {
			    continue;
			}

			$item = $dir . DIRECTORY_SEPARATOR . $entry;

			if (is_numeric($entry) && $this->passesAnyProductIdCondition($entry)) {
				$this->cleanImagesByDimensions($item . DIRECTORY_SEPARATOR . 'main');
				$this->cleanImagesByDimensions($item . DIRECTORY_SEPARATOR . 'additional');
			}
		}

		closedir($dh);

		array_pop($this->session->data['iwatermark_clean']['progress']);
	}

	private function cleanImagesByDimensions($dir) {
		// Iterate through all images in the $dir directory, extract their dimensions, and delete them if they match any of the settings.

		if (!is_dir($dir) || !is_writable($dir)) return;

		clearstatcache(true);

		$dh = opendir($dir);
		$regex = '~(\d+)x(\d+)~i';

		while (false !== ($entry = readdir($dh))) {
			if (in_array($entry, array('.', '..'))) {
			    continue;
			}

			$item = $dir . DIRECTORY_SEPARATOR . $entry;
			$matches = array();

			if (preg_match($regex, $entry, $matches)) {
				$width = $matches[1];
				$height = $matches[2];

				foreach ($this->session->data['iwatermark_clean']['settings'] as $setting) {
					if ($this->passesWidthHeightConditions($width, $height, $setting)) {
						clearstatcache(true);
						if (is_file($item) && is_writable($item)) {
							@unlink($item);
						}
					}
				}
			}
		}

		closedir($dh);
	}

	private function passesWidthHeightConditions($width, $height, $setting) {
		// Check if these settings match the provided width and height

		return $setting['LimitSizeType'] == 'all' || 
			(
				$setting['LimitSizeType'] == 'bigger_than' && 
				(
					(int)trim($width) > (int)trim($setting['LimitSizeWidth']) && empty($setting['LimitSizeHeight']) || 
					(int)trim($height) > (int)trim($setting['LimitSizeHeight']) && empty($setting['LimitSizeWidth']) || 
					(int)trim($width) > (int)trim($setting['LimitSizeWidth']) && (int)trim($height) > (int)trim($setting['LimitSizeHeight'])
				)
			)
			||
			(
				$setting['LimitSizeType'] == 'smaller_than' && 
				(
					(int)trim($width) < (int)trim($setting['LimitSizeWidth']) && empty($setting['LimitSizeHeight']) || 
					(int)trim($height) < (int)trim($setting['LimitSizeHeight']) && empty($setting['LimitSizeWidth']) || 
					(int)trim($width) < (int)trim($setting['LimitSizeWidth']) && (int)trim($height) < (int)trim($setting['LimitSizeHeight'])
				)
			);
	}

	private function passesAnyProductIdCondition($product_id) {
		// Iterate through all settings and see if any of them match this product_id

		$result = false;

		foreach ($this->session->data['iwatermark_clean']['settings'] as $setting) {
			if (!$result) {
				$result = $result || $this->passesProductIdConditions($product_id, $setting);
			}
		}

		return $result;
	}

	private function passesProductIdConditions($product_id, $setting) {
		// Check if settings match this specific product ID

		$meets_category_condition = true;
		$meets_product_condition = true;

		if (!empty($setting['LimitCategories']) && $setting['LimitCategories'] == 'specific') {
			if (!empty($setting['LimitCategoriesList'])) {
				$meets_category_condition = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product_to_category` WHERE product_id='" . (int)$product_id . "' AND category_id IN (" . implode(',', $setting['LimitCategoriesList']) . ")")->num_rows > 0;
			} else {
				$meets_category_condition = false;
			}
		}

		if (!empty($setting['LimitRelated']) && $setting['LimitRelated'] == 'specific') {
			if (!empty($setting['LimitRelatedList'])) {
				$meets_product_condition = in_array($product_id, $setting['LimitRelatedList']);
			} else {
				$meets_product_condition = false;
			}
		}

		return $meets_category_condition || $meets_product_condition;
	}

	public function deleteSetting($group, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($group) . "'");
	}
	
	public function returnMaxUploadSize($readable = false) {
		$upload = $this->return_bytes(ini_get('upload_max_filesize'));
		$post = $this->return_bytes(ini_get('post_max_size'));
		
		if ($upload >= $post) return $readable ? $this->sizeToString($post - 524288) : $post - 524288;
		else return $readable ? $this->sizeToString($upload) : $upload;
	}
	
	private function return_bytes($config) { //based on http://php.net/manual/en/function.ini-get.php
		$config = trim($config);
		$last = strtolower($config[strlen($config)-1]);
		$number = preg_replace('~[^0-9]~', '', $config);

		switch($last) {
			case 'g':
				$number *= 1024 * 1024 * 1024;
			case 'm':
				$number *= 1024 * 1024;
			case 'k':
				$number *= 1024;
		}
	
		return $number;
	}

	private function sizeToString($size) {
		$count = 0;
		for ($i = $size; $i >= 1024; $i /= 1024) $count++;
		switch ($count) {
			case 0 : $suffix = ' B'; break;
			case 1 : $suffix = ' KB'; break;
			case 2 : $suffix = ' MB'; break;
			case 3 : $suffix = ' GB'; break;
			case ($count >= 4) : $suffix = ' TB'; break;
		}
		return round($i, 2).$suffix;
	}
	
	public function getStandardFile($file, $arrayIndex = 0, $fieldName = 'Image') {
		$allowedExts = array("image/jpeg", "image/png");
		
		$name = $file['name'][$fieldName];
		$extension = $file['type'][$fieldName];
		$result = false;
		if ($file['size'][$fieldName] <= $this->returnMaxUploadSize() && in_array($extension, $allowedExts)) {
			if ($file['error'][$fieldName] > 0) throw new Exception($this->language->get('error_upload_error') . $file['error'][$fieldName]);

			$destFolder = dirname(DIR_SYSTEM).'/vendors/iwatermark/current_watermark/' . strval((int)$arrayIndex);
			$dest = $destFolder.'/'.$name;
			if(!file_exists($destFolder))
				mkdir($destFolder, 0755, true);
			
			$this->cleanFolder($destFolder);
			
			if (!move_uploaded_file($file['tmp_name'][$fieldName], $dest)) throw new Exception($this->language->get('error_unable_upload'));
			else $result = $dest;
			
		} else throw new Exception($this->language->get('error_invalid_file'));
		
		return array(
			'image' => preg_replace('~^https?:~', '', HTTP_CATALOG) . '/vendors/iwatermark/current_watermark/' . strval((int)$arrayIndex) . '/' . $name,
			'path' => $dest
		);
	}

	public function cleanFolder($tempDir) {
		if (empty($tempDir)) return false;
		$files = scandir($tempDir);
		foreach ($files as $file) {
			if (!in_array($file, array('.', '..', 'index.html'))) {
				if (is_file($tempDir.'/'.$file)) unlink ($tempDir.'/'.$file);
				if (is_dir($tempDir.'/'.$file)) {
					$this->cleanFolder($tempDir.'/'.$file);	
					rmdir($tempDir.'/'.$file);
				}
			}
		}
	}
	
	public function hex2rgb($hex) { // from http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
		$hex = str_replace("#", "", $hex);
		
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array('r' => $r, 'g' => $g, 'b' => $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}

	public function handleEvent($function, $product_id) {
		$this->load->helper('vendor/isenselabs/watermark/OpenCartImageSymlink');

		$imageSymlink = new OpenCartImageSymlink($this->registry);

		if (in_array($function, array('addProduct', 'editProduct'))) {
            $imageSymlink->update($product_id);
        } else if ($function == 'deleteProduct') {
        	$imageSymlink->deleteProductDir($product_id);
        }
    }
}