<?php
class ControllerProductApply extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('account/address');
		$this->load->model('catalog/apply');
		$this->load->model('catalog/product');

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
				$para['country_id'] = $this->request->post['country_id'];
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
					$para['country_id'] = $this->request->post['shipping_country_id'];
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

				$this->response->redirect($this->url->link('checkout/checkout'));
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
