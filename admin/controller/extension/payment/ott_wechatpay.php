<?php
class ControllerExtensionPaymentOTTWechatpay extends Controller {
	private $error = array();

	public function index() {
		// load language file: ott_wechatpay
		$this->load->language('extension/payment/ott_wechatpay');
		// $_['heading_title']                  = 'OTT Wechatpay Pay';
		$this->document->setTitle($this->language->get('heading_title'));
		// load model setting.php
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ott_wechatpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_extension'] = $this->language->get('text_extension');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_edit'] = $this->language->get('text_edit');

		$data['text_ott_wechatpay'] = $this->language->get('text_ott_wechatpay');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_sandbox'] = $this->language->get('text_sandbox');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_ott_wechatpay_merchant_id'] = $this->language->get('entry_ott_wechatpay_merchant_id');
		$data['entry_ott_wechatpay_sign_key'] = $this->language->get('entry_ott_wechatpay_sign_key');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');
		
		$data['help_total'] = $this->language->get('help_total');
		$data['help_wechatpay_setup'] = $this->language->get('help_wechatpay_setup');

		$data['error_permission'] = $this->language->get('error_permission');
		$data['error_ott_wechatpay_merchant_id'] = $this->language->get('error_ott_wechatpay_merchant_id');
		$data['error_ott_wechatpay_sign_key'] = $this->language->get('error_ott_wechatpay_sign_key');

		$data['button_save'] = $this->language->get('button_save');
		// $data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		//-------------- check errors for user input -----------------------
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['ott_wechatpay_merchant_id'])){
			$data['error_ott_wechatpay_merchant_id'] = $this->error['ott_wechatpay_merchant_id'];
		} else {
			$data['error_ott_wechatpay_merchant_id'] = '';
		}

		if (isset($this->error['ott_wechatpay_sign_key'])){
			$data['error_ott_wechatpay_sign_key'] = $this->error['ott_wechatpay_sign_key'];
		} else {
			$data['error_ott_wechatpay_sign_key'] = '';
		}

		//----------- default tree header ----------------------------
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/ott_wechatpay', 'token=' . $this->session->data['token'], true)
		);
		//---------- default ends -------------------------

		//---------- data action -------------------------
		$data['action'] = $this->url->link('extension/payment/ott_wechatpay', 'token=' . $this->session->data['token'], true);
		//---------- data cancel -------------------------
		$data['cancel'] = $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		//---------- check and set ott merchant id ------------
		if( isset($this->request->post['ott_wechatpay_merchant_id'])) {
			$data['ott_wechatpay_merchant_id'] = $this->request->post['ott_wechatpay_merchant_id'];
		}else{
			$data['ott_wechatpay_merchant_id'] = $this->config->get('ott_wechatpay_merchant_id');
		}

		//---------- check and set ott sign key ---------------
		if( isset($this->request->post['ott_wechatpay_sign_key'])) {
			$data['ott_wechatpay_sign_key'] = $this->request->post['ott_wechatpay_sign_key'];
		}else{
			$data['ott_wechatpay_sign_key'] = $this->config->get('ott_wechatpay_sign_key');
		}

		//------------- load order status for extension edit page options -----------------
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['ott_wechatpay_completed_status_id'])) {
			$data['ott_wechatpay_completed_status_id'] = $this->request->post['ott_wechatpay_completed_status_id'];
		} else {
			$data['ott_wechatpay_completed_status_id'] = $this->config->get('ott_wechatpay_completed_status_id');
		}
		//--------------- set if this payment gateway is enable to use or not ------------------
		if (isset($this->request->post['ott_wechatpay_status'])) {
			$data['ott_wechatpay_status'] = $this->request->post['ott_wechatpay_status'];
		} else {
			$data['ott_wechatpay_status'] = $this->config->get('ott_wechatpay_status');
		}
		//--------------- set the sort order for items in order item list ----------------------------
		if (isset($this->request->post['ott_wechatpay_sort_order'])) {
			$data['ott_wechatpay_sort_order'] = $this->request->post['ott_wechatpay_sort_order'];
		} else {
			$data['ott_wechatpay_sort_order'] = $this->config->get('ott_wechatpay_sort_order');
		}

		//----------------- load default content for extension page -----------------------
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		//----------------- output the content page for this payment gateway
		$this->response->setOutput($this->load->view('extension/payment/ott_wechatpay', $data));
	}

	//--------------- check the input from user has error or not ---------------------
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/ott_wechatpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['ott_wechatpay_merchant_id']) {
			$this->error['ott_wechatpay_merchant_id'] = $this->language->get('error_ott_wechatpay_merchant_id');
		}
		if (!$this->request->post['ott_wechatpay_sign_key']) {
			$this->error['ott_wechatpay_sign_key'] = $this->language->get('error_ott_wechatpay_sign_key');
		}

		return !$this->error;
	}
}
