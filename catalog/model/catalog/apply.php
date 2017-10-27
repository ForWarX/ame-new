<?php
class ModelCatalogApply extends Model {
	public function getProductUPCs() {
		$query = $this->db->query("SELECT DISTINCT upc FROM " . DB_PREFIX . "product WHERE upc != ''");

		$rt = array();
		if ($query->num_rows) {
			foreach ($query->rows as $result) {
				$rt[] = $result['upc'];
			}
		}
		return $rt;
	}

	public function getProductByUPC($upc) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, (SELECT p2c.category_id FROM " . DB_PREFIX . "product_to_category p2c WHERE p.product_id = p2c.product_id LIMIT 1) AS category_id, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class  FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.upc = '" . $this->db->escape($upc) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW()");
	
		if ($query->num_rows) {
			$result = array(
					'product_id'       => $query->row['product_id'],
					'name'             => $query->row['name'],
					'description'      => $query->row['description'],
					'meta_title'       => $query->row['meta_title'],
					'meta_description' => $query->row['meta_description'],
					'meta_keyword'     => $query->row['meta_keyword'],
					'tag'              => $query->row['tag'],
					'model'            => $query->row['model'],
					'sku'              => $query->row['sku'],
					'upc'              => $query->row['upc'],
					'ean'              => $query->row['ean'],
					'jan'              => $query->row['jan'],
					'isbn'             => $query->row['isbn'],
					'mpn'              => $query->row['mpn'],
					'location'         => $query->row['location'],
					'quantity'         => $query->row['quantity'],
					'category_id'      => isset($query->row['category_id']) ? $query->row['category_id'] : 0,
					'manufacturer_id'  => isset($query->row['manufacturer_id']) ? $query->row['manufacturer_id'] : 0,
					'manufacturer'     => isset($query->row['manufacturer']) ? $query->row['manufacturer'] : '',
					'price'            => $query->row['price'],
					'date_available'   => $query->row['date_available'],
					'weight'           => $query->row['weight'],
					'weight_class_id'  => $query->row['weight_class_id'],
					'length'           => $query->row['length'],
					'width'            => $query->row['width'],
					'height'           => $query->row['height'],
					'length_class_id'  => $query->row['length_class_id'],
					'minimum'          => $query->row['minimum'],
					'sort_order'       => $query->row['sort_order'],
					'status'           => $query->row['status'],
					'date_added'       => $query->row['date_added'],
					'date_modified'    => $query->row['date_modified']
			);

			if ($result['category_id'] != 0) {
                $query = $this->db->query("SELECT canMix FROM " . DB_PREFIX . "category WHERE category_id='" . $result['category_id'] . "'");
                if ($query->num_rows) {
                    $result['canMix'] = isset($query->row['canMix']) ? $query->row['canMix'] : 1; // 该商品品类是否可以跟别的品类混合下单，默认允许
                }
            } else {
                $result['canMix'] = 1;
            }

            return $result;
		} else {
			return false;
		}
	}
	
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = 'user_added', sku = '', upc = '" . $this->db->escape($data['upc']) . "', ean = '', jan = '', isbn = '', mpn = '" . $this->db->escape($data['mpn']) . "', location = '', quantity = '1000000', minimum = '1', subtract = '0', stock_status_id = '7', date_available = '0000-00-00', manufacturer_id = '0', shipping = '1', price = '" . (float)$data['price'] . "', points = '0', weight = '0', weight_class_id = '5', length = '0', width = '0', height = '0', length_class_id = '2', status = '1', tax_class_id = '0', sort_order = '100', date_added = NOW()");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

        $this->load->model('localisation/language');
		$lang_cn_id = $this->model_localisation_language->getLanguageByCode("zh-CN")['language_id'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '1', name = '" . $this->db->escape($data['meta_title']) . "', description = '', tag = '" . $this->db->escape($data['tag']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '', meta_keyword = ''");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . $lang_cn_id . "', name = '" . $this->db->escape($data['name']) . "', description = '', tag = '" . $this->db->escape($data['tag']) . "', meta_title = '" . $this->db->escape($data['name']) . "', meta_description = '', meta_keyword = ''");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '59'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '0', layout_id = '0'");

		$this->cache->delete('product');

		return $product_id;
	}
}
