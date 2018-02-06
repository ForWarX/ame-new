<?php
class ModelSaleOrder extends Model {
    // 生成AME订单号
	public function createOrderNumber() {
		$query = $this->db->query("SELECT invoice_prefix FROM " . DB_PREFIX . "order ORDER BY order_id DESC LIMIT 1");

		$prefix = 'AME';
		$date = date("ymd", time());
    if (empty($query->row) || substr($query->row['invoice_prefix'], 0, 3) != $prefix) {
       $order_no = $prefix . $date . "0000";
      } else {
          $no = $query->row['invoice_prefix'];
           $no = substr($no, 9);
           $no = ((int)$no + 1) % 10000;
          $no = sprintf("%04d", $no);
            $order_no = $prefix . $date . $no;
     }

		return $order_no;
	}
    public function createOrderNumberForShop($order_id) {
        //$query = $this->db->query("SELECT invoice_prefix FROM " . DB_PREFIX . "order ORDER BY order_id DESC LIMIT 1");

        $prefix = 'AME';
        $date = date("ymd", time());
       // if (empty($query->row) || substr($query->row['invoice_prefix'], 0, 3) != $prefix) {
        //    $order_no = $prefix . $date . "0000";
       // } else {
           // $no = $query->row['invoice_prefix'];
           // $no = substr($no, 9);
         //   $no = ((int)$no + 1) % 10000;
          //  $no = sprintf("%04d", $no);
        $no = $order_id% 10000;

            $order_no = $prefix . $date . $no;
     //   }

        return $order_no;
    }
}