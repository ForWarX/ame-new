<?php
class ModelLocalisationStorage extends Model {
	public function getStorage($storage_id) {
		$query = $this->db->query("SELECT storage_id, name, address, geocode, telephone, fax, image, open, comment FROM " . DB_PREFIX . "storage WHERE storage_id = '" . (int)$storage_id . "'");

		return $query->row;
	}
}