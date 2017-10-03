<?php
class ModelExtensionTotalBoxType extends Model {
    public function getTotal($total) {
    }

    // 获取所有箱子种类
    public function getBoxTypes() {
        $box_types = $this->config->get("box_type");
        $box_types = str_replace("&quot;", '"', $box_types);
        $box_types = json_decode($box_types, true);
        return $box_types;
    }
}