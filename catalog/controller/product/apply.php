<?php
class ControllerProductApply extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('account/address');
		$this->load->model('catalog/apply');
		$this->load->model('catalog/product');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');

        $logged = $this->customer->isLogged();
        if ($logged) {
            $this->load->model('account/customer_group');
            $group_id = $this->customer->getGroupId();
            $group_info = $this->model_account_customer_group->getCustomerGroup($group_id);
            if ($group_info['name'] == 'Admin') {
                $data['admin_customer'] = $group_info['name'];
            }
        }
		
		$data['err_message'] = '';
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $address_id = $this->customer->getAddressId();
			if (empty($this->request->post['address_id'])) {
				// there is new address add it.
				$para = array();
				$para['address_id'] = 0;
				$para['email'] = $this->request->post['email'];
				$para['phone'] = $this->request->post['phone'];
				$para['firstname'] = $this->request->post['firstname'];
				$para['lastname'] = '';
				$para['company'] = $this->request->post['company'];
				$para['address_1'] = $this->request->post['address_1'];
				$para['address_2'] = '';
				$para['postcode'] = $this->request->post['postcode'];
				$para['city'] = $this->request->post['city'];
				$para['zone_id'] = $this->request->post['zone_id'];
				$para['zone'] = $this->model_localisation_zone->getZone($para['zone_id'])['name'];
				$para['country_id'] = $this->request->post['country_id'];
				$para['country'] = $this->model_localisation_country->getCountry($para['country_id'])['name'];
				$para['custom_field'] = '';
				$para['chinaid'] = '';
				$para['chinaid_front'] = '';
				$para['chinaid_back'] = '';
				if ($logged) {
					$address_id = $this->model_account_address->addAddress($para);
					if (!$address_id) {
						$data['err_message'] = "Can't add sender address";
					} else {
						$para['address_id'] = $address_id; 
					}
				}
				$this->session->data['payment_address'] = $para;
			} else {
			    $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);
            }
			
			if (empty($data['err_message'])) {
				$shipping = '';
				if ($this->request->post['shipping_address_id']) {
					// use user own address, check it
					$shipping_address_id = $this->request->post['shipping_address_id'];
					$shipping = $this->model_account_address->getAddress($shipping_address_id);
					if (empty($shipping)) {
						$data['err_message'] = "Unknown sender address";
					} else {
						$this->session->data['shipping_address'] = $shipping;
					}
				}
				if (empty($data['err_message']) && empty($shipping)) {
					// there is new address add it.
					$para = array();
					$para['address_id'] = 0;
					$para['email'] = $this->request->post['shipping_email'];
					$para['phone'] = $this->request->post['shipping_phone'];
					$para['firstname'] = $this->request->post['shipping_firstname'];
					$para['lastname'] = '';
					$para['company'] = $this->request->post['shipping_company'];
					$para['address_1'] = $this->request->post['shipping_address_1'];
					$para['address_2'] = '';
					$para['postcode'] = $this->request->post['shipping_postcode'];
					$para['city'] = $this->request->post['shipping_city'];
					$para['zone_id'] = $this->request->post['shipping_zone_id'];
                    $para['zone'] = $this->model_localisation_zone->getZone($para['zone_id'])['name'];
					$para['country_id'] = $this->request->post['shipping_country_id'];
                    $para['country'] = $this->model_localisation_country->getCountry($para['country_id'])['name'];
					$para['custom_field'] = '';
					$para['chinaid'] = $this->request->post['chinaid'];
					$para['chinaid_front'] = '';
					$para['chinaid_back'] = '';
	
					// Front
					if (!empty($this->request->files['chinaid_front']['name']) && is_file($this->request->files['chinaid_front']['tmp_name'])) {
						$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['chinaid_front']['name'], ENT_QUOTES, 'UTF-8')));
						
						// Validate the filename length
						if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
							$data['err_message'] = 'Filename error';
						}
						
						// Allowed file extension types
						$allowed = array();
						$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
						$filetypes = explode("\n", $extension_allowed);
						foreach ($filetypes as $filetype) {
							$allowed[] = trim($filetype);
						}
						
						$file_ext = strtolower(substr(strrchr($filename, '.'), 1));
						if (!in_array($file_ext, $allowed)) {
							$data['err_message'] = 'Filename type error';
						}
						
						// Allowed file mime types
						$allowed = array();
						$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
						$filetypes = explode("\n", $mime_allowed);
						foreach ($filetypes as $filetype) {
							$allowed[] = trim($filetype);
						}
						
						if (!in_array($this->request->files['chinaid_front']['type'], $allowed)) {
							$data['err_message'] = 'Filename type error';
						}
						
						// Check to see if any PHP files are trying to be uploaded
						$content = file_get_contents($this->request->files['chinaid_front']['tmp_name']);
						if (preg_match('/\<\?php/i', $content)) {
							$data['err_message'] = 'Filename type error';
						}
						
						// Return any upload error
						if ($this->request->files['chinaid_front']['error'] != UPLOAD_ERR_OK) {
							$data['err_message'] = 'Filename upload error';
						}
						
						if (empty($data['err_message'])) {
							$tempnam = tempnam(DIR_IMAGE . "upload/", 'idimg');
							$para['chinaid_front'] = $tempnam . "." . $file_ext;
							rename($this->request->files['chinaid_front']['tmp_name'], $para['chinaid_front']);
							$len = strlen($_SERVER['DOCUMENT_ROOT']);
							$para['chinaid_front'] = substr($para['chinaid_front'], $len);
						}
					}

					// back
					if (!empty($this->request->files['chinaid_back']['name']) && is_file($this->request->files['chinaid_back']['tmp_name'])) {
						$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['chinaid_back']['name'], ENT_QUOTES, 'UTF-8')));
							
						// Validate the filename length
						if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
							$data['err_message'] = 'Filename error';
						}
							
						// Allowed file extension types
						$allowed = array();
						$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
						$filetypes = explode("\n", $extension_allowed);
						foreach ($filetypes as $filetype) {
							$allowed[] = trim($filetype);
						}
							
						$file_ext = strtolower(substr(strrchr($filename, '.'), 1));
						if (!in_array($file_ext, $allowed)) {
							$data['err_message'] = 'Filename type error';
						}
							
						// Allowed file mime types
						$allowed = array();
						$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
						$filetypes = explode("\n", $mime_allowed);
						foreach ($filetypes as $filetype) {
							$allowed[] = trim($filetype);
						}
							
						if (!in_array($this->request->files['chinaid_back']['type'], $allowed)) {
							$data['err_message'] = 'Filename type error';
						}
							
						// Check to see if any PHP files are trying to be uploaded
						$content = file_get_contents($this->request->files['chinaid_back']['tmp_name']);
						if (preg_match('/\<\?php/i', $content)) {
							$data['err_message'] = 'Filename type error';
						}
							
						// Return any upload error
						if ($this->request->files['chinaid_back']['error'] != UPLOAD_ERR_OK) {
							$data['err_message'] = 'Filename upload error';
						}
						
						if (empty($data['err_message'])) {
							$tempnam = tempnam(DIR_IMAGE . "upload/", 'idimg');
							$para['chinaid_back'] = $tempnam . "." . $file_ext;
							rename($this->request->files['chinaid_back']['tmp_name'], $para['chinaid_back']);
							$len = strlen($_SERVER['DOCUMENT_ROOT']);
							$para['chinaid_back'] = substr($para['chinaid_back'], $len);
						}
					}
					
					if ($logged) {
						$shipping_address_id = $this->model_account_address->addAddress($para);
						if (!$shipping_address_id) {
							$data['err_message'] = "Can't add Recipient address";
						} else {
							$para['address_id'] = $shipping_address_id;
						}
					}
					$this->session->data['shipping_address'] = $para;
				} else {
                    $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping_address_id']);
                }
			}
			
			if (empty($data['err_message'])) {
				// Check upload production
				$upcs = $this->request->post['upc'];
				$mpns = $this->request->post['mpn'];
				$meta_titles = $this->request->post['meta_title'];
				$names = $this->request->post['name'];
				$tags = $this->request->post['tag'];
				$quantitys = $this->request->post['quantity'];
				$prices = $this->request->post['price'];
				$prod_ids = $this->request->post['prod_id'];
				$category_ids = $this->request->post['category_id'];
                $canMixs = $this->request->post['canMix'];
				$category_id = -1;
                $canMix = -1;
                $upc = "";
                $doAlert = false;
				$this->cart->clear();
				foreach ($prod_ids as $key => $product_id) {
					if (empty($product_id)) {
						// add this product
						$para = array();
						$para['upc'] = $upcs[$key];
						$para['mpn'] = $mpns[$key];
						$para['meta_title'] = $meta_titles[$key];
						$para['name'] = $names[$key];
						$para['tag'] = $tags[$key];
						$para['price'] = $prices[$key];
						$para['category_id'] = $category_ids[$key];
						if (empty($para['upc']) || empty($para['name']) || empty($para['price'])) {
							$data['err_message'] = "Please fillup required area";
							break;
						}
						$product_id = $this->model_catalog_apply->addProduct($para);
					}
					
					$product_info = $this->model_catalog_product->getProduct($product_id);
					if (!$product_info) {
						$data['err_message'] = "Can't find product (" . $product_id . ")";
						break;
					}
					
					$quantity = (int)$quantitys[$key];
					if ($quantity <= 0) {
						$data['err_message'] = "Quantity can't be zerro";
						break;
					}
					/*
					if ($category_id < 0) {
						$category_id = $category_ids[$key];
					} else if ($category_id != $category_ids[$key]) {
						$data['err_message'] = "UPC : " . $upcs[$key] . " should belong to different order";
						break;
					}*/

                    // 判断订单的混单品类是否正确
                    // 1. 可混单与不可混单不能混合
                    // 2. 不可混单的品类是否相同
                    /*
                    $mix = $canMixs[$key];
                    if ($mix == 0 && $category_id == -1) { // 记下首个不可混单的品类
                        $category_id = $category_ids[$key];
                        $upc = $upcs[$key];
                    }

                    if ($canMix == -1) { // 首个品类
                        $canMix = $mix;
                    }
                    else if ($mix != $canMix) $doAlert = true;
                    else if ($mix != 1) { // 首个品类不能混单的情况下，之后的品类出现与首个品类不相同的情况
                        $doAlert = $category_ids[$key] != $category_id;
                    }

                    if ($doAlert) {
                        $upc1 = $upcs[$key];
                        $txt = $upc == "" ? $upc : $upc . ", ";
                        $txt .= $upc1 . " must shipping in different package, please remove one and place it in different order";
                        $data['err_message'] = $txt;
                        break;
                    }*/


					$option = array();
					$recurring_id = 0;

					$this->cart->add($product_id, $quantity, $option, $recurring_id);
				}
			}
			
			if (empty($data['err_message'])) {
				// OK. got to checkout
				// Unset all shipping and payment methods
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				$this->session->data['order_type'] = "express"; // 下单方式 = 直接下单

                // 原本是走结算界面，暂且改成直接保存订单
				//$this->response->redirect($this->url->link('checkout/checkout'));

                // ============== 直接保存订单 开始 ==============
                $order_data = array();

                $order_data['weight'] = $this->request->post['admin_weight'];

                $totals = array();
                $taxes = $this->cart->getTaxes();
                $total = 0;

                // Because __call can not keep var references so we put them into an array.
                $total_data = array(
                    'totals' => &$totals,
                    'taxes'  => &$taxes,
                    'total'  => &$total
                );

                $this->load->model('extension/extension');

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

                // 因为没有加入购物车，所以价格全部为0，改为下单界面设置的价格
                if (!empty($this->request->post["admin_total"])) {
                    $total_data['total'] = $this->request->post["admin_total"];
                    foreach ($totals as $key => $value) {
                        if ($value['code'] == 'total') {
                            $totals[$key]['value'] = $this->request->post["admin_total"];
                            break;
                        }
                    }
                }

                $sort_order = array();

                foreach ($totals as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $totals);

                $order_data['totals'] = $totals;

                $this->load->language('checkout/checkout');

                $this->load->model('sale/order');

                // invoice_prefix没有用处，作为AME单号使用
                $order_data['invoice_prefix'] = $this->model_sale_order->createOrderNumber(); // $this->config->get('config_invoice_prefix');
                $order_data['store_id'] = $this->config->get('config_store_id');
                $order_data['store_name'] = $this->config->get('config_name');

                if ($order_data['store_id']) {
                    $order_data['store_url'] = $this->config->get('config_url');
                } else {
                    if ($this->request->server['HTTPS']) {
                        $order_data['store_url'] = HTTPS_SERVER;
                    } else {
                        $order_data['store_url'] = HTTP_SERVER;
                    }
                }

                if ($this->customer->isLogged()) {
                    $this->load->model('account/customer');
                    $this->load->model('account/customer_group');

                    $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

                    $order_data['customer_id'] = $this->customer->getId();
                    $order_data['customer_group_id'] = $customer_info['customer_group_id'];
                    $order_data['firstname'] = $customer_info['firstname'];
                    $order_data['lastname'] = $customer_info['lastname'];
                    $order_data['email'] = $customer_info['email'];
                    $order_data['telephone'] = $customer_info['telephone'];
                    $order_data['fax'] = $customer_info['fax'];
                    $order_data['custom_field'] = json_decode($customer_info['custom_field'], true);

                    // 检查是否是管理员账号
                    $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_info['customer_group_id']);
                    if ($customer_group_info['name'] == 'Admin') {
                        $data['customer_group'] = $customer_group_info['name'];
                    }
                } elseif (isset($this->session->data['guest'])) {
                    $order_data['customer_id'] = 0;
                    $order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                    $order_data['firstname'] = $this->session->data['guest']['firstname'];
                    $order_data['lastname'] = $this->session->data['guest']['lastname'];
                    $order_data['email'] = $this->session->data['guest']['email'];
                    $order_data['telephone'] = $this->session->data['guest']['telephone'];
                    $order_data['fax'] = $this->session->data['guest']['fax'];
                    $order_data['custom_field'] = $this->session->data['guest']['custom_field'];
                }

                $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
                $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
                $order_data['payment_company'] = $this->session->data['payment_address']['company'];
                $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
                $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
                $order_data['payment_city'] = $this->session->data['payment_address']['city'];
                $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
                $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
                $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
                $order_data['payment_country'] = $this->session->data['payment_address']['country'];
                $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
                $order_data['payment_address_format'] = isset($this->session->data['payment_address']['address_format']) ? $this->session->data['payment_address']['address_format'] : "";
                $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

                if (isset($this->session->data['payment_method']['title'])) {
                    $order_data['payment_method'] = $this->session->data['payment_method']['title'];
                } else {
                    $order_data['payment_method'] = 'Pay In Store';
                }

                if (isset($this->session->data['payment_method']['code'])) {
                    $order_data['payment_code'] = $this->session->data['payment_method']['code'];
                } else {
                    $order_data['payment_code'] = 'pay_in_store';
                }

                if ($this->cart->hasShipping()) {
                    $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                    $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                    $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                    $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                    $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                    $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                    $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                    $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                    $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                    $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                    $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                    $order_data['shipping_address_format'] = isset($this->session->data['shipping_address']['address_format']) ? $this->session->data['shipping_address']['address_format'] : "";
                    $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

                    if (isset($this->session->data['shipping_method']['title'])) {
                        $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                    } else {
                        $order_data['shipping_method'] = 'AME shipping';
                    }

                    if (isset($this->session->data['shipping_method']['code'])) {
                        $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                    } else {
                        $order_data['shipping_code'] = 'xshippingpro.xshippingpro1';
                    }
                } else {
                    $order_data['shipping_firstname'] = '';
                    $order_data['shipping_lastname'] = '';
                    $order_data['shipping_company'] = '';
                    $order_data['shipping_address_1'] = '';
                    $order_data['shipping_address_2'] = '';
                    $order_data['shipping_city'] = '';
                    $order_data['shipping_postcode'] = '';
                    $order_data['shipping_zone'] = '';
                    $order_data['shipping_zone_id'] = '';
                    $order_data['shipping_country'] = '';
                    $order_data['shipping_country_id'] = '';
                    $order_data['shipping_address_format'] = '';
                    $order_data['shipping_custom_field'] = array();
                    $order_data['shipping_method'] = '';
                    $order_data['shipping_code'] = '';
                }

                $order_data['products'] = array();

                foreach ($this->cart->getProducts() as $product) {
                    $option_data = array();

                    foreach ($product['option'] as $option) {
                        $option_data[] = array(
                            'product_option_id'       => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id'               => $option['option_id'],
                            'option_value_id'         => $option['option_value_id'],
                            'name'                    => $option['name'],
                            'value'                   => $option['value'],
                            'type'                    => $option['type']
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

                // Gift Voucher
                $order_data['vouchers'] = array();

                if (!empty($this->session->data['vouchers'])) {
                    foreach ($this->session->data['vouchers'] as $voucher) {
                        $order_data['vouchers'][] = array(
                            'description'      => $voucher['description'],
                            'code'             => token(10),
                            'to_name'          => $voucher['to_name'],
                            'to_email'         => $voucher['to_email'],
                            'from_name'        => $voucher['from_name'],
                            'from_email'       => $voucher['from_email'],
                            'voucher_theme_id' => $voucher['voucher_theme_id'],
                            'message'          => $voucher['message'],
                            'amount'           => $voucher['amount']
                        );
                    }
                }

                $order_data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : $this->request->post['admin_comment'];
                $order_data['total'] = $total_data['total'];

                if (isset($this->request->cookie['tracking'])) {
                    $order_data['tracking'] = $this->request->cookie['tracking'];

                    $subtotal = $this->cart->getSubTotal();

                    // Affiliate
                    $this->load->model('affiliate/affiliate');

                    $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

                    if ($affiliate_info) {
                        $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                        $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                    } else {
                        $order_data['affiliate_id'] = 0;
                        $order_data['commission'] = 0;
                    }

                    // Marketing
                    $this->load->model('checkout/marketing');

                    $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

                    if ($marketing_info) {
                        $order_data['marketing_id'] = $marketing_info['marketing_id'];
                    } else {
                        $order_data['marketing_id'] = 0;
                    }
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

                // 保存分单策略
                if (isset($this->session->data['split_strategy'])) {
                    $order_data['split_strategy'] = $this->session->data['split_strategy'];
                }

                $this->load->model('checkout/order');

                // 数据库添加订单
                $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1); // 订单的pending状态

                // 跳转到成功界面
                $this->response->redirect($this->url->link('checkout/success'));

                // ============== 直接保存订单 结束 ==============
			}
		}
		
		$this->load->language('product/product');
		$this->load->language('product/apply');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'Apply Order',
			'href' => $this->url->link('product/apply')
		);
		$this->document->addStyle('catalog/view/javascript/jquery/jquery-ui.css');
		$this->document->addScript('catalog/view/javascript/jquery/jquery-ui.js');
		
		$this->load->model('catalog/category');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		
		$category_info = $this->model_catalog_category->getCategory(0);

		$this->document->setTitle('Apply Order');
		$data['heading_title'] = 'Apply Order';

		$data['text_select'] = $this->language->get('text_select');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_stock'] = $this->language->get('text_stock');
		$data['text_discount'] = $this->language->get('text_discount');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_minimum'] = sprintf($this->language->get('text_minimum'), 1);
		$data['text_write'] = $this->language->get('text_write');
		$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
		$data['text_note'] = $this->language->get('text_note');
		$data['text_tags'] = $this->language->get('text_tags');
		$data['text_related'] = $this->language->get('text_related');
		$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['text_chinaid_front_helper'] = $this->language->get('text_chinaid_front_helper');
		$data['text_chinaid_back_helper'] = $this->language->get('text_chinaid_back_helper');

		$data['entry_qty'] = $this->language->get('entry_qty');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_review'] = $this->language->get('entry_review');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_good'] = $this->language->get('entry_good');
		$data['entry_bad'] = $this->language->get('entry_bad');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_continue'] = $this->language->get('button_continue');

		if ($this->customer->isLogged()) {
			$addressid = $this->customer->getAddressId();
			$data['user_address'] = $this->model_account_address->getAddress($addressid);
			$data['shipping_address_list'] = $this->model_account_address->getAddresses();
			// unset($data['customer_shipping_address'][$addressid]);
		} else {
			$data['user_address'] = array();
			$data['shipping_address_list'] = array();
		}
		
		$data['countries'] = $this->model_localisation_country->getCountries();
		$data['zones'] = $this->model_localisation_zone->getZonesByCountryId(isset($data["user_address"]["country_id"]) ? $data["user_address"]["country_id"] : 38);
		
		$data['upcs'] = $this->model_catalog_apply->getProductUPCs();
		
		$data['action_url'] = $this->url->link('product/apply');
		
		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('product/apply', $data));
	}

	public function verify() {
		$data['err_message'] = '';
		$data['status'] = 'Error';
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$address_id = $this->customer->getAddressId();
			$this->load->model('account/address');
			$logged = $this->customer->isLogged();
			if ($logged && ($this->request->post['address_id'] == $address_id)) {
				// use user own address, check it?
				//$data['err_message'] = "Unknown sender address";
			} else {
				if (false && empty($this->request->post['email'])) {
					$data['err_message'] = "Unknown sender email";
				} else if (empty($this->request->post['phone'])) {
					$data['err_message'] = "Unknown sender phone";
				} else if (empty($this->request->post['firstname'])) {
					$data['err_message'] = "Unknown sender name";
				} else if (empty($this->request->post['address_1'])) {
					$data['err_message'] = "Unknown sender address";
				} else if (empty($this->request->post['city'])) {
					$data['err_message'] = "Unknown sender city";
				} else if (empty($this->request->post['zone_id'])) {
					$data['err_message'] = "Unknown sender Province";
				} else if (empty($this->request->post['country_id'])) {
					$data['err_message'] = "Unknown sender country";
				}
			}
			
			if (empty($data['err_message'])) {
				if ($this->request->post['shipping_address_id']) {
					// use user own address, check it
					$shipping_address_id = $this->request->post['shipping_address_id'];
					$shipping = $this->model_account_address->getAddress($shipping_address_id);
					if (empty($shipping)) {
						$data['err_message'] = "Unknown recipient address";
					} else if (false && empty($this->request->post['shipping_email'])) {
						$data['err_message'] = "Unknown recipient email";
					} else if (empty($this->request->post['shipping_phone'])) {
						$data['err_message'] = "Unknown recipient phone";
					} else if (empty($this->request->post['shipping_firstname'])) {
						$data['err_message'] = "Unknown recipient name";
					} else if (empty($this->request->post['shipping_address_1'])) {
						$data['err_message'] = "Unknown recipient address";
					} else if (empty($this->request->post['shipping_city'])) {
						$data['err_message'] = "Unknown recipient city";
					} else if (empty($this->request->post['shipping_zone_id'])) {
						$data['err_message'] = "Unknown recipient Province";
					} else if (empty($this->request->post['shipping_country_id'])) {
						$data['err_message'] = "Unknown recipient country";
					} else if ($this->request->post['shipping_country_id'] == 44) {
						// China
						if (empty($this->request->post['chinaid'])) {
							$data['err_message'] = "Recipient missing China ID";
						}
					}
				}
			}
			
			if (empty($data['err_message'])) {
				// Check upload production
                $this->load->model('catalog/product');

				$upcs = $this->request->post['upc'];
				$mpns = $this->request->post['mpn'];
				$meta_titles = $this->request->post['meta_title'];
				$names = $this->request->post['name'];
				$tags = $this->request->post['tag'];
				$quantitys = $this->request->post['quantity'];
				$prices = $this->request->post['price'];
				$prod_ids = $this->request->post['prod_id'];
				$category_ids = $this->request->post['category_id'];
				$canMixs = $this->request->post['canMix'];
				$category_id = -1;
				$canMix = -1;
				$upc = "";
				$doAlert = false;
				$total_price = 0;
				foreach ($prod_ids as $key => $product_id) {
					if (!empty($product_id)) {
						$product_info = $this->model_catalog_product->getProduct($product_id);
						if (!$product_info) {
							$data['err_message'] = "Can't find product (" . $product_id . ")";
							break;
						}
					}
					
					if (empty($upcs[$key])) {
						$data['err_message'] = "UPC can't be empty";
						break;
					}
					$quantity = (int)$quantitys[$key];
					if ($quantity <= 0) {
						$data['err_message'] = "Quantity can't be zero";
						break;
					}
					if ((float)$prices[$key] <= 0) {
						$data['err_message'] = "Price can't be zero";
						break;
					} else {
                        $total_price += (float)$prices[$key] * $quantity;
                    }
                    /*
					if ($category_id < 0) {
						$category_id = $category_ids[$key];
					} else if ($category_id != $category_ids[$key]) {
						$data['err_message'] = "UPC : " . $upcs[$key] . " should belong to different order";
						break;
					}*/

                    // 判官订单的混单品类是否正确
                    // 1. 可混单与不可混单不能混合
                    // 2. 不可混单的品类是否相同
                    $mix = $canMixs[$key];
                    if ($mix == 0 && $category_id == -1) { // 记下首个不可混单的品类
                        $category_id = $category_ids[$key];
                        $upc = $upcs[$key];
                    }

                    if ($canMix == -1) { // 首个品类
                        $canMix = $mix;
                    }
                    else if ($mix != $canMix) $doAlert = true;
                    else if ($mix != 1) { // 首个品类不能混单的情况下，之后的品类出现与首个品类不相同的情况
                        $doAlert = $category_ids[$key] != $category_id;
                    }

                    if ($doAlert) {
                        $upc1 = $upcs[$key];
                        $txt = $upc == "" ? $upc : $upc . ", ";
                        $txt .= $upc1 . " must shipping in different package, please remove one and place it in different order";
                        $data['err_message'] = $txt;
                        break;
                    }
				}
				if (empty($data['err_message']) && $total_price > 330) {
                    $data['err_message'] = "Total price can't be over $330, please make another order.";
                }
			}
			
			if (empty($data['err_message'])) {
				$data['status'] = 'OK';
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function get() {
		$this->load->model('catalog/apply');
		$result = $this->model_catalog_apply->getProductByUPC($this->request->get['inputupc']);
		
		if ($result) {
			$json = array("status" => "OK", 'product' => $result);
		} else {
			$json = array("status" => "Fail", 'product' => $result);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function add() {
		$this->load->model('catalog/apply');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    /**
     * 复制旧订单，创建新订单
     */
    public function copy() {
        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $this->response->redirect($this->url->link('product/apply', '', true));
        }

        $logged = $this->customer->isLogged();
        if ($logged) {
            $this->load->model('account/customer_group');
            $group_id = $this->customer->getGroupId();
            $group_info = $this->model_account_customer_group->getCustomerGroup($group_id);
            if ($group_info['name'] == 'Admin') {
                $data['admin_customer'] = $group_info['name'];
            }
        }

        $this->load->model('account/order');
        $this->load->model('account/address');

        if ($this->customer->isLogged()) {
            $addressid = $this->customer->getAddressId();
            $data['user_address'] = $this->model_account_address->getAddress($addressid);
            $data['shipping_address_list'] = $this->model_account_address->getAddresses();
            // unset($data['customer_shipping_address'][$addressid]);
        } else {
            $data['user_address'] = array();
            $data['shipping_address_list'] = array();
        }

        $order_info = $this->model_account_order->getOrder($order_id);

        if ($order_info) {
            $this->load->model('catalog/product');
            $this->load->model('tool/upload');

            if ($this->customer->isLogged()) {
                $data['shipping_selected'] = $order_info['shipping_firstname'] . ' - ' . $order_info['shipping_address_1'];
            } else {
                $data['user_address']['address_id'] = '';
                $data['user_address']['firstname'] = $order_info['payment_firstname'];
                $data['user_address']['company'] = $order_info['payment_company'];
                $data['user_address']['city'] = $order_info['payment_city'];
                $data['user_address']['zone_id'] = $order_info['payment_zone_id'];
                $data['user_address']['address_1'] = $order_info['payment_address_1'];
                $data['user_address']['postcode'] = $order_info['payment_postcode'];
                $data['user_address']['country_id'] = $order_info['payment_country_id'];
                $data['user_address']['email'] = $order_info['email'];
                $data['user_address']['phone'] = $order_info['telephone'];
                $data['shipping_copy'] = array(
                    'name' => $order_info['shipping_firstname'],
                    'company' => $order_info['shipping_company'],
                    'city' => $order_info['shipping_city'],
                    'zone_id' => $order_info['shipping_zone_id'],
                    'address_1' => $order_info['shipping_address_1'],
                    'postcode' => $order_info['shipping_postcode'],
                    'country_id' => $order_info['shipping_country_id'],
                    'email' => '',
                    'phone' => $order_info['shipping_phone'],
                    'chinaid' => $order_info['shipping_chinaid'],
                );
            }

            // Products
            $data['products'] = array();

            $products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

            foreach ($products as $product) {
                $res = $this->model_catalog_product->getProduct($product['product_id']);
                $upc = $res['upc'];

                $data['products'][] = array(
                    'upc'      => $upc,
                    'name'     => $product['name'],
                    'model'    => $product['model'],
                    'quantity' => $product['quantity'],
                    'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                );
            }
        } else {
            $this->response->redirect($this->url->link('product/apply', '', true));
        }

        $this->load->model('catalog/apply');
        $this->load->model('catalog/product');

        $this->load->language('product/product');
        $this->load->language('product/apply');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => 'Apply Order',
            'href' => $this->url->link('product/apply')
        );
        $this->document->addStyle('catalog/view/javascript/jquery/jquery-ui.css');
        $this->document->addScript('catalog/view/javascript/jquery/jquery-ui.js');

        $this->load->model('catalog/category');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        $this->document->setTitle('Apply Order');
        $data['heading_title'] = 'Apply Order';

        $data['text_select'] = $this->language->get('text_select');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_model'] = $this->language->get('text_model');
        $data['text_reward'] = $this->language->get('text_reward');
        $data['text_points'] = $this->language->get('text_points');
        $data['text_stock'] = $this->language->get('text_stock');
        $data['text_discount'] = $this->language->get('text_discount');
        $data['text_tax'] = $this->language->get('text_tax');
        $data['text_option'] = $this->language->get('text_option');
        $data['text_minimum'] = sprintf($this->language->get('text_minimum'), 1);
        $data['text_write'] = $this->language->get('text_write');
        $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
        $data['text_note'] = $this->language->get('text_note');
        $data['text_tags'] = $this->language->get('text_tags');
        $data['text_related'] = $this->language->get('text_related');
        $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['text_chinaid_front_helper'] = $this->language->get('text_chinaid_front_helper');
        $data['text_chinaid_back_helper'] = $this->language->get('text_chinaid_back_helper');

        $data['entry_qty'] = $this->language->get('entry_qty');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_review'] = $this->language->get('entry_review');
        $data['entry_rating'] = $this->language->get('entry_rating');
        $data['entry_good'] = $this->language->get('entry_good');
        $data['entry_bad'] = $this->language->get('entry_bad');

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['button_continue'] = $this->language->get('button_continue');

        $data['countries'] = $this->model_localisation_country->getCountries();
        $data['zones'] = $this->model_localisation_zone->getZonesByCountryId(isset($data["user_address"]["country_id"]) ? $data["user_address"]["country_id"] : 38);
        if (isset($data['shipping_copy'])) {
            $data['shipping_zones'] = $this->model_localisation_zone->getZonesByCountryId(isset($data["shipping_copy"]["country_id"]) ? $data["shipping_copy"]["country_id"] : 38);
        }

        $data['upcs'] = $this->model_catalog_apply->getProductUPCs();

        $data['action_url'] = $this->url->link('product/apply');

        // Captcha
        if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
            $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
        } else {
            $data['captcha'] = '';
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('product/apply', $data));
    }
}
