<?php
class ModelExtensionTotalBoxFee extends Model {
    public function getTotal($total) {
        if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
            $this->load->language('extension/total/box_fee');

            $box_fee = $this->config->get('box_fee');
            if (!empty($this->session->data['order_per_box'])) {
                $box_count = count($this->session->data['order_per_box']);
                $box_fee *= $box_count;
            }

            $total['totals'][] = array(
                'code'       => 'shipping',
                'title'      => $this->language->get('entry_box_fee'),
                'value'      => $box_fee,
                'sort_order' => $this->config->get('box_fee_sort_order')
            );

            /*
            if ($this->session->data['shipping_method']['tax_class_id']) {
                $tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

                foreach ($tax_rates as $tax_rate) {
                    if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
                        $total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
                    } else {
                        $total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
                    }
                }
            }*/



            $total['total'] += $box_fee;
        }
    }
}