<?php

class ModelExtensionModuleIwatermark extends Model {
	public function getSetting() {
		$this->load->model('setting/setting');

		$data = $this->model_setting_setting->getSetting('iwatermark', $this->config->get('config_store_id')); 
		
		if (!empty($data['iwatermark']['Enabled']) && $data['iwatermark']['Enabled'] == 'true') {
			return $data['iwatermark'];
		}

		return false;
	}

	public function getProductImages($query) {
		if (false !== $setting = $this->getSetting()) {
			$this->load->helper('vendor/isenselabs/watermark/OpenCartImageSymlink');

			$imageSymlink = new OpenCartImageSymlink($this->registry);

			foreach ($query->rows as &$product_image) {
			    if (!empty($product_image['product_id']) && !empty($product_image['image'])) {

			        $product_image['image'] = $imageSymlink->linkImage($product_image['product_id'], $product_image['image'], true, false);
			    }
			}
		}

		return $query;
	}

	public function getProduct($query) {
		if (false !== $setting = $this->getSetting()) {
			if (!empty($query->row['product_id']) && !empty($query->row['image'])) {
				$this->load->helper('vendor/isenselabs/watermark/OpenCartImageSymlink');

				$imageSymlink = new OpenCartImageSymlink($this->registry);

				$query->row['image'] = $imageSymlink->linkImage($query->row['product_id'], $query->row['image'], false, false);
			}
		}

		return $query;
	}

	public function getWatermarkSetting($image, $width, $height) {
		if (false === $setting = $this->getSetting()) {
			return false;
		}

		$regex = '~/\d+-\d+/(\d+)/(main|additional)/.*~i';
		$matches = array();

		if (!preg_match($regex, $image, $matches)) {
			return false;
		}

		$product_id = $matches[1];

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

		if (!$meets_category_condition && !$meets_product_condition) {
			return false;
		}

		if ($setting['LimitSizeType'] == 'all' || 
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
			)
		) {
			return $setting;
		}

		return false;
	}
}