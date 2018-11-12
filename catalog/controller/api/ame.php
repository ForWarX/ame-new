<?php
class ControllerApiAme extends Controller {
	/**
	 * Get shipping price
	 * 
	 * check shipping price. the post parameter can be decided later.
	 * Now I just make it as is
	 * 
	 * post parameter:
	 *     mid  : string. the ID for each shipping client. for example, "AuroraTDInc"
	 *     products : json array. list for each shipping products it include product's ID, product name (may be not?), width (in inch or cm ?), height, length, weight (in g or lb?)...
	 *     number : total product count
	 *     address : shipping address.
	 *     city : shipping city.
	 *     provence : shipping provence.
	 *     country : shiiping country.
	 *     postcode : postcode.
	 *     vcode : verify code. md5 val for passed parameter. this value is calculated in both server / client side. if it is not match, just drop the request. It is generated as following, the veriable sequence must be followed:
	 *             md5("address=".$adderss."&city=".$city."&country=".$country."&mid=".$mid."&number=".$number."&postcode=".$postcode."&provence=".$provence)
	 *             for example: md5("address=23haolou 1001 xingjieko&city=beijing&country=CN&mid=AuroraTDInc&number=3&postcode=100012&provence=beijing")
	 *             
	 */
	public function index() {
		$this->load->language('api/ame');	// Put your language file in catalog/language/api folder named as ame.php

		$json = array("status" => 0, "message" => $this->language->get('unknown_error'));	// Default error

		$this->load->model('checkout/ame');	// Put you model file in catalog/model/checkout folder named as ame.php

		// Verify post parameter
		$post = $this->model_checkout_ame->verifyParameter($this->request->post); // check vcode

		if ($post) {
			// calculate shipping fee
			$result = $this->model_checkout_ame->shippingFee($post);
			if (is_array($result)) {
				// Success on return an array include price, number of box...
				$json['status'] = 1;	// Success
				$json['message'] = $this->language->get('success');
				$json['result'] = $result;
			} else {
				// On error return a string with error message
				$json['message'] = $result;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	/**
	 * Confirm with shipping order
	 * 
	 * check shipping price. the post parameter can be decided later.
	 * Now I just make it as is
	 * 
	 * post parameter:
	 *     mid  : string. the ID for each shipping client. for example, "AuroraTDInc"
	 *     products : json array. list for each shipping products it include product's ID, product name (may be not?), width (in inch or cm ?), height, length, weight (in g or lb?)...
	 *     number : total product count
	 *     order : order number
	 *     price : confirmed price
	 *     address : shipping address.
	 *     city : shipping city.
	 *     provence : shipping provence.
	 *     country : shiiping country.
	 *     postcode : postcode.
	 *     vcode : verify code. md5 val for passed parameter. this value is calculated in both server / client side. if it is not match, just drop the request. It is generated as following, the veriable sequence must be followed:
	 *             md5("address=".$adderss."&city=".$city."&country=".$country."&mid=".$mid."&number=".$number."&postcode=".$postcode."&provence=".$provence)
	 *             for example: md5("address=23haolou 1001 xingjieko&city=beijing&country=CN&mid=AuroraTDInc&number=3&postcode=100012&provence=beijing")
	 *             
	 */
	public function confirm() {
		$this->load->language('api/ame');	// Put your language file in catalog/language/api folder named as ame.php
		
		$json = array("status" => 0, "message" => $this->language->get('unknown_error'));	// Default error
		
		$this->load->model('checkout/ame');	// Put you model file in catalog/model/checkout folder named as ame.php
		
		// Verify post parameter
		$post = $this->model_checkout_ame->verifyParameter($this->request->post); // check vcode
		
		if ($post) {
			// Record the confirm if you want to......
			$result = $this->model_checkout_ame->shippingConfirm($post);
			if (is_array($result)) {
				// Success on return an array include price, number of box...
				$json['status'] = 1;	// Success
				$json['message'] = $this->language->get('success');
				$json['result'] = $result;
			} else {
				// On error return a string with error message
				$json['message'] = $result;
			}
		} else {
			// record unknow source for debug...
			$json['error'] = $this->language->get('error_confirm');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

		}

	public function getShippingFee() {

		$this->load->language('api/login');

		$json = array();

		$this->load->model('account/api');


		//$key = isset($this->request->post['key']) ? $this->request->post['key'] : '';
		//$myid = isset($this->request->post['myid']) ? $this->request->post['myid'] : '';



		// Login with API Key
		$api_info = $this->model_account_api->getApiByKey($this->request->post['key']);

		if ( $api_info ) {
			// Check if IP is allowed
			$ip_data = array();

			$results = $this->model_account_api->getApiIps($api_info['api_id']);

			foreach ( $results as $result ) {
				$ip_data[] = trim($result['ip']);
			}


			if (!$json) {
			//$json['success'] = $this->language->get('text_success');


				// We want to create a seperate session so changes do not interfere with the admin user.

				$session_id_new = $this->session->createId();

				$this->session->start('api', $session_id_new);

				$this->session->data['api_id'] = $api_info['api_id'];

				// Close and write the new session.
				//$session->close();

				$this->session->start('default');

				// Create Token
				$token = 1;

			} else {
				$json['success'] = 0;
				//$json['error']['key'] = $this->language->get('error_key');
			}
		}
		//--------------------------------------------------------------------------------------------------------
		$this->load->language('api/cart');

		if (!$token) {
			$json['success'] = 0;

		} else {

			//everytime clear the cart's products firstly

			$this->cart->clear();

			$products = isset($this->request->post['products']) ? $this->request->post['products'] : '';

			$verifycode = isset($this->request->post['verifycode']) ? $this->request->post['verifycode'] : '';
			if (md5($products."AmeshippingFee") === $verifycode) {
				  $json['success'] = 1;
			  }
			  else{
				  $json['success'] = 0;
			  }

			if(isset($this->request->post['products'])) {

				$products = str_replace('&quot;', '"', $products);

				$productArr = json_decode($products, true);

				if ($productArr) {

					foreach($productArr as $product) {

						$this->load->model('catalog/product');

						$product_info = $this->model_catalog_product -> getProduct($product['product_id']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}
						if($product_info) {
							$this->cart->add($product['product_id'], $product['quantity'], $option);
						}
					}

				}
				/*
				$productArr = json_decode($products, true);

				if ($productArr) {

					foreach($productArr as $product) {

						$product_info = $this->model_catalog_product->getProduct($product['product_id']);

						if ( isset( $product['option'] )) {
							$option = array_filter($product['option']);
						} else {
							$option = array();
						}
						if($product_info) {
							$this->cart->add($product['product_id'], $product['quantity'], $option);
						}
					}

				}
				*/
			}

	//		if(isset($this->request->post['product_quantity'])) {

	//		      $count = $this->request->post['product_quantity'];

	//			while( $count > 0 ){

	//	        if (isset( $this->request->post['product_id'.$count])) {

	//			            $this->load->model('catalog/product');

	//				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id'.$count]);

	//			if	 ( $product_info ) {
	//				if ( isset($this->request->post['quantity'.$count]) ) {
	//					$quantity = $this->request->post['quantity'.$count];
	//				} else {
	//					$quantity = 1;
	//				}

	//				if ( isset( $this->request->post['option'] )) {
	//					$option = array_filter($this->request->post['option']);
	//				} else {
	//					$option = array();
	//				}

	//				$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id'.$count]);

	//				foreach ( $product_options as $product_option ) {
	//					if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
	//						$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
	//					}
	//				}

	//				if (!isset($json['error']['option'])) {
	//					$this->cart->add($this->request->post['product_id'.$count], $quantity, $option);

	//					$token2 = 1;
	//					unset($this->session->data['shipping_method']);
	//					unset($this->session->data['shipping_methods']);
	//					unset($this->session->data['payment_method']);
	//					unset($this->session->data['payment_methods']);
	//				}
	//			} else {
	//				$json['error']['store'] = $this->language->get('error_store');
	//			}
	//		  }

	//			$count--;
	//		}
	//	  }
		}
      //------------------------------------------------------------------------------------------------------
		if (!$token) {
			$json['success'] = 0;

			//$json['error']['warning'] = $this->language->get('error_permission');

		}else{
			// Shipping Methods
			$json['shipping_methods'] = array();

			$this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('shipping');

			$this->session->data['shipping_address'] = array(
				'firstname'      =>'' ,
				'lastname'       => '',
				'company'        =>'' ,
				'address_1'      => '',
				'address_2'      => '',
				'postcode'       =>'' ,
				'city'           => '',
				'zone_id'        => '',
				'zone'           => '',
				'zone_code'      =>'',
				'country_id'     => '',
				'country'        => '',
				'iso_code_2'     =>'',
				'iso_code_3'     => '',
				'address_format' => '',
				'custom_field'   => ''
			);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {

					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
					     	 $quote_shpping_way = $quote['quote'];
						     $quote_shpping_way = array_shift($quote_shpping_way);
					         $json['shipping_methods'] = $quote_shpping_way['cost'];

					}
				}
			}

			if ($json['shipping_methods']) {
				$this->session->data['shipping_methods'] = $json['shipping_methods'];
			} else {
				$json['error'] = $this->language->get('error_no_shipping');
			}
		}
		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: POST');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function addorder() {

		$this->load->language('api/login');

		$json = array();

		$this->load->model('account/api');

		// Login with API Key
		$api_info = $this->model_account_api->getApiByKey($this->request->post['key']);

		if ( $api_info ) {
			// Check if IP is allowed
			$ip_data = array();

			$results = $this->model_account_api->getApiIps($api_info['api_id']);

			foreach ( $results as $result ) {
				$ip_data[] = trim($result['ip']);
			}

		

			if (!$json) {

				// We want to create a seperate session so changes do not interfere with the admin user.

				$session_id_new = $this->session->createId();

				$this->session->start('api', $session_id_new);

				$this->session->data['api_id'] = $api_info['api_id'];

				// Close and write the new session.
				//$session->close();

				$this->session->start('default');

				// Create Token
				$token = 1;


			} else {
				$json['error']['key'] = $this->language->get('error_key');
			}

		}
		//--------------------------------------------------------------------------------------------------
		//add cart
		$this->load->language('api/cart');

		if (!$token) {
			$json['success'] = 0;
			//$json['error']['warning'] = $this->language->get('error_permission');

		} else {

			$this->cart->clear();

			$products = isset($this->request->post['products']) ? $this->request->post['products'] : '';

			$verifycode = isset($this->request->post['verifycode']) ? $this->request->post['verifycode'] : '';
			if (md5($products."AmeshippingFee") === $verifycode) {
			}
			else{
				$json['success'] = 0;
			}

			if(isset($this->request->post['products'])) {
				$products = str_replace('&quot;', '"', $products);

				$productArr = json_decode($products, true);

				if ($productArr) {

					foreach($productArr as $product) {

						$this->load->model('catalog/product');

						$product_info = $this->model_catalog_product->getProduct($product['product_id']);

						if ( isset( $product['option'] )) {
							$option = array_filter($product['option']);
						} else {
							$option = array();
						}
						if($product_info) {

							$this->cart->add($product['product_id'], $product['quantity'], $option);

						}
					}

				}
			}
		}
		/*
		else {

			//everytime clear the cart's products firstly

			$this->cart->clear();

			if(isset($this->request->post['product_quantity'])) {

				$count = $this->request->post['product_quantity'];

				while( $count > 0 ){

					if (isset( $this->request->post['product_id'.$count])) {

						$this->load->model('catalog/product');

						$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id'.$count]);

						if	 ( $product_info ) {
							if ( isset($this->request->post['quantity'.$count]) ) {
								$quantity = $this->request->post['quantity'.$count];
							} else {
								$quantity = 1;
							}

							if ( isset( $this->request->post['option'] )) {
								$option = array_filter($this->request->post['option']);
							} else {
								$option = array();
							}

							$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id'.$count]);

							foreach ( $product_options as $product_option ) {
								if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
									$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
								}
							}

							if (!isset($json['error']['option'])) {
								$this->cart->add($this->request->post['product_id'.$count], $quantity, $option);

								unset($this->session->data['shipping_method']);
								unset($this->session->data['shipping_methods']);
								unset($this->session->data['payment_method']);
								unset($this->session->data['payment_methods']);
							}
						} else {
							$json['error']['store'] = $this->language->get('error_store');
						}
					}

					$count--;
				}
			}
		}
		*/

		//-----------------------------------------------------------------------------------------------------------
		//add order
		$this->load->language('api/order');

		$json = array();

		if (!$token) {
			$json['success'] = 0;
			//$json['error'] = $this->language->get('error_permission');
		} else {

			// Payment Address
			if (!isset($this->session->data['payment_address'])) {
				//$json['error'] = $this->language->get('error_payment_address');
			}
			// Shipping Address
			if (!isset($this->session->data['shipping_address'])) {
				//$json['error'] = $this->language->get('error_shipping_address');
			}


			// Cart
			if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
				$json['error'] = $this->language->get('error_stock');
			}

			// Validate minimum quantity requirements.
			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

					break;
				}
			}

			if (!$json) {
				//$json['success'] = $this->language->get('text_success');
				$json['success'] = 1;
				$order_data = array();
				$this->load->model('sale/order');
				// Store Details

				$order_data['invoice_prefix'] = $this->model_sale_order->createOrderNumber();
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');

				// Customer Details
				if (isset($this->request->post['customer_id'])) {
					$order_data['customer_id'] = ($this->request->post['customer_id']);
				}else{
					$order_data['customer_id'] = '';
				}

				if (isset($this->request->post['customer_group_id'])) {
					$order_data['customer_group_id'] = ($this->request->post['customer_group_id']);
				}else{
					$order_data['customer_group_id'] = '';
				}
				if (isset($this->request->post['customer_firstname'])) {
					$order_data['firstname'] = ($this->request->post['customer_firstname']).' '.($this->request->post['customer_lastname']);
				}else{
					$order_data['firstname'] = 'haitao';
				}
				    $order_data['lastname'] = '';
				if (isset($this->request->post['customer_email'])) {
					$order_data['email'] = ($this->request->post['customer_email']);
				}else{
					$order_data['email'] = '';
				}
				if (isset($this->request->post['customer_telephone'])) {
					$order_data['telephone'] = ($this->request->post['customer_telephone']);
				}else{
					$order_data['telephone'] = '';
				}
				if (isset($this->request->post['customer_fax'])) {
					$order_data['fax'] = ($this->request->post['customer_fax']);
				}else{
					$order_data['fax'] = '';
				}
				if (isset($this->request->post['customer_custom_field'])) {
					$order_data['custom_field'] = ($this->request->post['customer_custom_field']);
				}else{
					$order_data['custom_field'] = '';
				}


				// Payment Details
				if (isset($this->request->post['payment_firstname'])) {
					$order_data['payment_firstname'] = ($this->request->post['payment_firstname']).' '.($this->request->post['payment_lastname']);
				    }else{
					$order_data['payment_firstname'] = '';
				}
			      	$order_data['payment_lastname'] = '';
				if (isset($this->request->post['payment_company'])) {
					$order_data['payment_company'] = $this->request->post['payment_company'];
				    }else{
					$order_data['payment_company'] = '';
				}
				if (isset($this->request->post['payment_address_1'])) {
					$order_data['payment_address_1'] = $this->request->post['payment_company'];
				}else{
					$order_data['payment_address_1'] = '';
				}
				if (isset($this->request->post['payment_address_2'])) {
					$order_data['payment_address_2'] = $this->request->post['payment_address_2'];
				}else{
					$order_data['payment_address_2'] = '';
				}
				if (isset($this->request->post['payment_city'])) {
					$order_data['payment_city'] = $this->request->post['payment_city'];
				}else{
					$order_data['payment_city'] = '';
				}
				if (isset($this->request->post['payment_phone'])) {
					$order_data['payment_phone'] = $this->request->post['payment_phone'];
				}else{
					$order_data['payment_phone'] = '';
				}
				if (isset($this->request->post['payment_postcode'])) {
					$order_data['payment_postcode'] = $this->request->post['payment_postcode'];
				}else{
					$order_data['payment_postcode'] = '';
				}
				if (isset($this->request->post['payment_zone'])) {
					$order_data['payment_zone'] = $this->request->post['payment_zone'];
				}else{
					$order_data['payment_zone'] = '';
				}
				if (isset($this->request->post['payment_zone_id'])) {
					$order_data['payment_zone_id'] = $this->request->post['payment_zone_id'];
				}else{
					$order_data['payment_zone_id'] = '';
				}
				if (isset($this->request->post['payment_country'])) {
					$order_data['payment_country'] = $this->request->post['payment_country'];
				}else{
					$order_data['payment_country'] = '';
				}
				if (isset($this->request->post['payment_country_id'])) {
					$order_data['payment_country_id'] = $this->request->post['payment_country_id'];
				}else{
					$order_data['payment_country_id'] = '';
				}
				if (isset($this->request->post['payment_address_format'])) {
					$order_data['payment_address_format'] = $this->request->post['payment_address_format'];
				}else{
					$order_data['payment_address_format'] = '';
				}
				if (isset($this->request->post['payment_firstname'])) {
					$order_data['payment_firstname'] = ($this->request->post['payment_firstname']).' '.($this->request->post['payment_lastname']);
				}else{
					$order_data['payment_firstname'] = '';
				}

				    $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

				if (isset($this->session->data['payment_method']['title'])) {
					$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = 'Haitao';
				}

				if (isset($this->session->data['payment_method']['code'])) {
					$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}

				// Shipping Details
				if ($this->cart->hasShipping()) {
					if (isset($this->request->post['shipping_firstname'])) {
						$order_data['shipping_firstname'] = ($this->request->post['shipping_firstname']).' '.($this->request->post['shipping_lastname']);
					} else {
						$order_data['shipping_firstname'] = '';
					}
					    $order_data['shipping_lastname'] = '';

					if (isset($this->request->post['shipping_company'])) {
						$order_data['shipping_company'] = $this->request->post['shipping_company'];
					} else {
						$order_data['shipping_company'] = '';
					}
					if (isset($this->request->post['shipping_address_1'])) {
						$order_data['shipping_address_1'] = $this->request->post['shipping_address_1'];
					} else {
						$order_data['shipping_address_1'] = '';
					}
					if (isset($this->request->post['shipping_address_2'])) {
						$order_data['shipping_address_2'] = $this->request->post['shipping_address_2'];
					} else {
						$order_data['shipping_address_2'] = '';
					}
					if (isset($this->request->post['shipping_city'])) {
						$order_data['shipping_city'] = $this->request->post['shipping_city'];
					} else {
						$order_data['shipping_city'] = '';
					}
					if (isset($this->request->post['shipping_postcode'])) {
						$order_data['shipping_postcode'] = $this->request->post['shipping_postcode'];
					} else {
						$order_data['shipping_postcode'] = '';
					}
					if (isset($this->request->post['shipping_zone'])) {
						$order_data['shipping_zone'] = $this->request->post['shipping_zone'];
					} else {
						$order_data['shipping_zone'] = '';
					}
					if (isset($this->request->post['shipping_zone_id'])) {
						$order_data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
					} else {
						$order_data['shipping_zone_id'] = '';
					}
					if (isset($this->request->post['shipping_country'])) {
						$order_data['shipping_country'] = $this->request->post['shipping_country'];
					} else {
						$order_data['shipping_country'] = '';
					}
					if (isset($this->request->post['shipping_address_format'])) {
						$order_data['shipping_country_id'] = $this->request->post['shipping_address_format'];
					} else {
						$order_data['shipping_country_id'] = '';
					}
					if (isset($this->request->post['shipping_district'])) {
						$order_data['shipping_district'] = $this->request->post['shipping_district'];
					} else {
						$order_data['shipping_district'] = '';
					}
					if (isset($this->request->post['shipping_chinaid'])) {
						$order_data['shipping_chinaid'] = $this->request->post['shipping_chinaid'];
					} else {
						$order_data['shipping_chinaid'] = '';
					}
					if (isset($this->request->post['shipping_phone'])) {
						$order_data['shipping_phone'] = $this->request->post['shipping_phone'];
					} else {
						$order_data['shipping_phone'] = '';
					}
					if (isset($this->request->post['shipping_address_format'])) {
						$order_data['shipping_address_format'] = $this->request->post['shipping_address_format'];
					} else {
						$order_data['shipping_address_format'] = '';
					}

					    $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

					if (isset($this->session->data['shipping_method']['title'])) {
						$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
					} else {
						$order_data['shipping_method'] = 'AME_shipping';
					}

					if (isset($this->session->data['shipping_method']['code'])) {
						$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
					} else {
						$order_data['shipping_code'] = '';
					}
				} else {
					$order_data['shipping_firstname']      = '';
					$order_data['shipping_company']        = '';
					$order_data['shipping_address_1']      = '';
					$order_data['shipping_address_2']      = '';
					$order_data['shipping_city']            = '';
					$order_data['shipping_postcode']        = '';
					$order_data['shipping_zone']            = '';
					$order_data['shipping_zone_id']         = '';
					$order_data['shipping_country']         = '';
					$order_data['shipping_country_id']      = '';
					$order_data['shipping_address_format']  = '';
					$order_data['shipping_custom_field']    = array();
					$order_data['shipping_method']           = '';
					$order_data['shipping_code']             = '';
				}
				//------------------------------------------------------------------------------------
				// Products
				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'        => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'                 => $option['option_id'],
							'option_value_id'          => $option['option_value_id'],
							'name'                      => $option['name'],
							'value'                     => $option['value'],
							'type'                      => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}

				// Order Totals
				$this->load->model('extension/extension');

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				// Because __call can not keep var references so we put them into an array.
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($total_data['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data['totals']);

				$order_data = array_merge($order_data, $total_data);

				if (isset($this->request->post['comment'])) {
					$order_data['comment'] = $this->request->post['comment'];
				} else {
					$order_data['comment'] = '';
				}

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->cart->getSubTotal();

					// Affiliate
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this ->model_affiliate_affiliate ->getAffiliate($this->request->post['affiliate_id']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					// Marketing
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}

				$this->load->model('checkout/order');

				$json['order_id'] = $this->model_checkout_order->addOrder($order_data);
				$json['order_ameno'] = $order_data['invoice_prefix'];
				// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}

				$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);

				// clear cart since the order has already been successfully stored.
				//$this->cart->clear();
			}
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: POST');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}




}

