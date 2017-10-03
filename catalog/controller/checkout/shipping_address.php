<?php
class ControllerCheckoutShippingAddress extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		$data['text_address_existing'] = $this->language->get('text_address_existing');
		$data['text_address_new'] = $this->language->get('text_address_new');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_china_id'] = $this->language->get('entry_china_id');
        $data['entry_china_id_front'] = $this->language->get('entry_china_id_front');
        $data['entry_china_id_back'] = $this->language->get('entry_china_id_back');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_upload'] = $this->language->get('button_upload');

		if (isset($this->session->data['shipping_address']['address_id'])) {
			$data['address_id'] = $this->session->data['shipping_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}

		$this->load->model('account/address');

		$data['addresses'] = $this->model_account_address->getAddresses();

		if (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		if (isset($this->session->data['shipping_address']['custom_field'])) {
			$data['shipping_address_custom_field'] = $this->session->data['shipping_address']['custom_field'];
		} else {
			$data['shipping_address_custom_field'] = array();
		}

		$this->response->setOutput($this->load->view('checkout/shipping_address', $data));
	}

	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
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
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!$json) {
			if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
				$this->load->model('account/address');

				if (empty($this->request->post['address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					// Default Shipping Address
					$this->load->model('account/address');

					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}
			} else {
				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error']['firstname'] = $this->language->get('error_firstname');
				}

				/*
				if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
					$json['error']['lastname'] = $this->language->get('error_lastname');
				}*/

				if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
					$json['error']['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
					$json['error']['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
					$json['error']['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '') {
					$json['error']['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
					$json['error']['zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				$this->load->model('account/custom_field');

				$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

				foreach ($custom_fields as $custom_field) {
					if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                        $json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                    }
				}

				if (!$json) {
					// Default Shipping Address
					$this->load->model('account/address');

					$para = $this->request->post;

                    // Front
                    if (!empty($this->request->files['chinaid_front']['name']) && is_file($this->request->files['chinaid_front']['tmp_name'])) {
                        $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['chinaid_front']['name'], ENT_QUOTES, 'UTF-8')));

                        // Validate the filename length
                        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                            $json['error']['err_message'] = 'Filename error';
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
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Allowed file mime types
                        $allowed = array();
                        $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
                        $filetypes = explode("\n", $mime_allowed);
                        foreach ($filetypes as $filetype) {
                            $allowed[] = trim($filetype);
                        }

                        if (!in_array($this->request->files['chinaid_front']['type'], $allowed)) {
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Check to see if any PHP files are trying to be uploaded
                        $content = file_get_contents($this->request->files['chinaid_front']['tmp_name']);
                        if (preg_match('/\<\?php/i', $content)) {
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Return any upload error
                        if ($this->request->files['chinaid_front']['error'] != UPLOAD_ERR_OK) {
                            $json['error']['err_message'] = 'Filename upload error';
                        }

                        if (empty($json['error']['err_message'])) {
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
                            $json['error']['err_message'] = 'Filename error';
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
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Allowed file mime types
                        $allowed = array();
                        $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
                        $filetypes = explode("\n", $mime_allowed);
                        foreach ($filetypes as $filetype) {
                            $allowed[] = trim($filetype);
                        }

                        if (!in_array($this->request->files['chinaid_back']['type'], $allowed)) {
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Check to see if any PHP files are trying to be uploaded
                        $content = file_get_contents($this->request->files['chinaid_back']['tmp_name']);
                        if (preg_match('/\<\?php/i', $content)) {
                            $json['error']['err_message'] = 'Filename type error';
                        }

                        // Return any upload error
                        if ($this->request->files['chinaid_back']['error'] != UPLOAD_ERR_OK) {
                            $json['error']['err_message'] = 'Filename upload error';
                        }

                        if (empty($json['error']['err_message'])) {
                            $tempnam = tempnam(DIR_IMAGE . "upload/", 'idimg');
                            $para['chinaid_back'] = $tempnam . "." . $file_ext;
                            rename($this->request->files['chinaid_back']['tmp_name'], $para['chinaid_back']);
                            $len = strlen($_SERVER['DOCUMENT_ROOT']);
                            $para['chinaid_back'] = substr($para['chinaid_back'], $len);
                        }
                    }

					$address_id = $this->model_account_address->addAddress($para);

					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);

					if ($this->config->get('config_customer_activity')) {
						$this->load->model('account/activity');

						$activity_data = array(
							'customer_id' => $this->customer->getId(),
							'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
						);

						$this->model_account_activity->addActivity('address_add', $activity_data);
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}