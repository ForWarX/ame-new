<?php
/**
 * @copyright        2016 opencart.cn - All Rights Reserved
 * @link             http://www.guangdawangluo.com
 * @author           TL <mengwb@opencart.cn>
 * @created          2016-12-12 16:04:00
 * @modified         2016-12-12 16:39:36
 */

class ControllerExtensionPaymentQrcodeweipayQrcode extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/success');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/success')
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['qrcode_title'] = $this->language->get('qrcode_title');

        $data['code_url'] = $this->session->data['code_url'];    
            
        $data['order_id'] = $this->session->data['order_id'];
        
        $data['action_success'] = $this->url->link('checkout/success');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/payment/qrcodeweipay_qrcode', $data));
    }

    public function isOrderPaied()
    {
        $json = array();
        
        $json['result'] = false;

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
            
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            
            if ($order_info['order_status_id'] == $this->config->get('qrcodeweipay_order_status_id')) {
                $json['result'] = true;
            }
        } 
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
