<?php 
class ControllerExtensionPaymentAlipayCrossBorder extends Controller {
	private $error = array(); 

	public function index() {
		// Load language files
		$this->language->load('extension/payment/alipay_cross_border');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		// submit form data
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			//$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('alipay_cross_border', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		// add language data for template
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
        $data['text_edit'] = $this->language->get('text_edit');
		
		$data['entry_seller_email'] = $this->language->get('entry_seller_email');
		$data['entry_security_code'] = $this->language->get('entry_security_code');
		$data['entry_partner'] = $this->language->get('entry_partner');
		$data['entry_currency_code'] = $this->language->get('entry_currency_code');
		$data['entry_order_status'] = $this->language->get('entry_order_status');	
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_trade_closed'] = $this->language->get('entry_trade_closed');
		$data['entry_trade_finished'] = $this->language->get('entry_trade_finished');
        
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['secrity_code'])) {
			$data['error_secrity_code'] = $this->error['secrity_code'];
		} else {
			$data['error_secrity_code'] = '';
		}

		if (isset($this->error['currency_code'])) {
			$data['error_currency_code'] = $this->error['currency_code'];
		} else {
			$data['error_currency_code'] = '';
		}

		if (isset($this->error['partner'])) {
			$data['error_partner'] = $this->error['partner'];
		} else {
			$data['error_partner'] = '';
		}

		// construct backend menu
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/alipay_cross_border', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = HTTPS_SERVER . 'index.php?route=extension/payment/alipay_cross_border&token=' . $this->session->data['token'];
		
		$data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		// init form data
		if (isset($this->request->post['alipay_cross_border_seller_email'])) {
			$data['alipay_cross_border_seller_email'] = $this->request->post['alipay_cross_border_seller_email'];
		} else {
			$data['alipay_cross_border_seller_email'] = $this->config->get('alipay_cross_border_seller_email');
		}

		if (isset($this->request->post['alipay_cross_border_security_code'])) {
			$data['alipay_cross_border_security_code'] = $this->request->post['alipay_cross_border_security_code'];
		} else {
			$data['alipay_cross_border_security_code'] = $this->config->get('alipay_cross_border_security_code');
		}

		if (isset($this->request->post['alipay_cross_border_partner'])) {
			$data['alipay_cross_border_partner'] = $this->request->post['alipay_cross_border_partner'];
		} else {
			$data['alipay_cross_border_partner'] = $this->config->get('alipay_cross_border_partner');
		}		

		if (isset($this->request->post['alipay_cross_border_currency_code'])) {
			$data['alipay_cross_border_currency_code'] = $this->request->post['alipay_cross_border_currency_code'];
		} else {
			$data['alipay_cross_border_currency_code'] = $this->config->get('alipay_cross_border_currency_code');
		}
		
		if (isset($this->request->post['alipay_cross_border_order_status_id'])) {
			$data['alipay_cross_border_order_status_id'] = $this->request->post['alipay_cross_border_order_status_id'];
		} else {
			$data['alipay_cross_border_order_status_id'] = $this->config->get('alipay_cross_border_order_status_id'); 
		} 


		if (isset($this->request->post['alipay_cross_border_trade_finished'])) {
			$data['alipay_cross_border_trade_finished'] = $this->request->post['alipay_cross_border_trade_finished'];
		} else {
			$data['alipay_cross_border_trade_finished'] = $this->config->get('alipay_cross_border_trade_finished'); 
		} 
		
		if (isset($this->request->post['alipay_cross_border_trade_closed'])) {
			$data['alipay_cross_border_trade_closed'] = $this->request->post['alipay_cross_border_trade_closed'];
		} else {
			$data['alipay_cross_border_trade_closed'] = $this->config->get('alipay_cross_border_trade_closed'); 
		} 		

		$this->load->model('localisation/order_status');		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
        if (isset($this->request->post['alipay_cross_border_geo_zone_id'])) {
			$data['alipay_cross_border_geo_zone_id'] = $this->request->post['alipay_cross_border_geo_zone_id'];
		} else {
			$data['alipay_cross_border_geo_zone_id'] = $this->config->get('alipay_cross_border_geo_zone_id');
		}
        
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['alipay_cross_border_status'])) {
			$data['alipay_cross_border_status'] = $this->request->post['alipay_cross_border_status'];
		} else {
			$data['alipay_cross_border_status'] = $this->config->get('alipay_cross_border_status');
		}
		
		if (isset($this->request->post['alipay_cross_border_sort_order'])) {
			$data['alipay_cross_border_sort_order'] = $this->request->post['alipay_cross_border_sort_order'];
		} else {
			$data['alipay_cross_border_sort_order'] = $this->config->get('alipay_cross_border_sort_order');
		}
		
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/payment/alipay_cross_border.tpl', $data));
	}

	// validate form data
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/alipay_cross_border')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['alipay_cross_border_seller_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['alipay_cross_border_security_code']) {
			$this->error['secrity_code'] = $this->language->get('error_secrity_code');
		}

		if (!$this->request->post['alipay_cross_border_partner']) {
			$this->error['partner'] = $this->language->get('error_partner');
		}

		if (!$this->request->post['alipay_cross_border_currency_code']) {
			$this->error['currency_code'] = $this->language->get('error_currency_code');
		}		
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>