<?php

// define where to put alipay interface log
define('ALIPAY_LOG_DIR', DIR_LOGS . "/alipay");
require_once(DIR_SYSTEM . "library/alipay/cross_border/alipay_service.class.php");
require_once(DIR_SYSTEM . "library/alipay/cross_border/alipay_notify.class.php");

function log_alipay_result($word) {
    $fp = fopen(ALIPAY_LOG_DIR . "/log_alipay_cross_border_" . strftime("%Y%m%d", time()) . ".txt", "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "[" . strftime("%Y-%m-%d %H:%I:%S", time()) . "][$word]\t\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

class ControllerExtensionPaymentAlipayCrossBorder extends Controller {
    private $order_id_prefix = 'PRD-';
    public function index() {
        // variable $alipay_config is defined in this file
        require_once(DIR_SYSTEM . "library/alipay/cross_border/alipay.config.php");
        $this->language->load('extension/payment/alipay_cross_border');
        
        // Init Alipay Configuration (Extensions => Payments => Alipay (Cross Border) => Edit)
        $security_code = $this->config->get('alipay_cross_border_security_code'); // security code
        $partner = $this->config->get('alipay_cross_border_partner');    // partner id
        $currency_code = $this->config->get('alipay_cross_border_currency_code');    //currency code
        
        // Define the Url for Alipay Notify and Return
        $return_url = HTTPS_SERVER . 'index.php?route=extension/payment/alipay_cross_border/return_url';
        $notify_url = HTTPS_SERVER . 'index.php?route=extension/payment/alipay_cross_border/notify';

        // Get Order Detail Information
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        // Prepare Parameters for Alipay
        $p_order_id = $this->order_id_prefix . $order_info['order_id'];
        $p_subject = sprintf("%s - Order #%s", $this->config->get('config_name'), $p_order_id);
        $p_body = $p_subject;
        $p_total_fee = $order_info['total'] * $order_info['currency_value'];
        $p_currency = $currency_code;
        if ($currency_code == '') {
            $p_currency = 'CNY';
        }

        $parameter = array(
            "service" => "create_forex_trade", // alipay cross border service name
            "partner" => $partner,
            "out_trade_no" => $p_order_id,
            "currency" => $p_currency,         // Define the payment currency
            "subject" => $p_subject,           // Describe the payment
            "body" => $p_body,                 // Product List Can be Added
            "return_url" => $return_url,
            "notify_url" => $notify_url,
            "_input_charset" => $alipay_config['input_charset'],
        );

        if ($order_info['currency_code'] == 'CNY') {
            $parameter["rmb_fee"] = sprintf("%.2f", $p_total_fee);
        } else {
            $parameter["total_fee"] = sprintf("%.2f", $this->currency->convert($p_total_fee, $order_info['currency_code'],$p_currency));    
        }
        
        // variable alipay_config is defined in alipay.config.php file
        $alipay_config['partner'] = $partner;
        $alipay_config['key'] = $security_code;
        $alipay = new AlipayService($alipay_config);
        $form_data = $alipay->buildRequestPara($parameter);

        // parepare data for template[alipay_cross_border.tpl]
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['title_success_to_pay_or_not'] = $this->language->get('title_success_to_pay_or_not');
        $data['alipay_confirmation_notice'] = $this->language->get('alipay_confirmation_notice');
        $data['button_success_pay'] = $this->language->get('button_success_pay');
        $data['button_fail_pay'] = $this->language->get('button_fail_pay');
        $data['order_id'] = $order_id;
        $data['sign'] = $alipay->buildSignStr(array('order_id' => $order_id));
        
        $data['action'] = $alipay->get_gateway().'_input_charset='.$alipay_config['input_charset'];
        $data['para'] = $form_data;
        

        //if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/alipay_cross_border.tpl')) {
        //    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/alipay_cross_border.tpl', $data));
        //} else {
        //    $this->response->setOutput($this->load->view('default/template/extension/payment/alipay_cross_border.tpl', $data));
        //}
        return $this->load->view('extension/payment/alipay_cross_border', $data);
    }

    /**
     * synchronous callback
     * 
     * @return 
     */
    public function return_url() {
        $log_id = time();
        log_alipay_result("[$log_id]alipay call return_url start[" . urldecode(filter_input(INPUT_SERVER, "QUERY_STRING")) . "]");
        $contact_email = $this->config->get('alipay_cross_border_seller_email');  // seller email
        $store_url = $this->config->get('config_url');
        header("Content-type:text/html;charset=utf-8");
        
        require_once(DIR_SYSTEM . "library/alipay/cross_border/alipay.config.php");
        $this->language->load('extension/payment/alipay_cross_border');
        
        // 1. verify parameter first
        if (!filter_input(INPUT_GET, 'order_id')) {
            // request from alipay return_rul
            if (!filter_input(INPUT_GET, 'out_trade_no') || !filter_input(INPUT_GET, 'trade_no')) {
                log_alipay_result("[$log_id]Invalid reponse from alipay");
                $this->rendMessage(sprintf($this->language->get('text_invalid_reponse'), $contact_email, $log_id));

                return;    // excpetion
            }
            
            // Get Parameters from Return Url Request
            // $trade_no = filter_input(INPUT_GET, 'trade_no');
            $order_id = filter_input(INPUT_GET, 'out_trade_no');
            if (strpos($order_id, $this->order_id_prefix) !== false) {
                $order_id = substr($order_id, strlen($this->order_id_prefix));
            }
        } else {
            // request from store payment confirmation
            $order_id = filter_input(INPUT_GET, 'order_id');
        }
        
        // 2. verify request, set alipay configuration
        $partner = $this->config->get('alipay_cross_border_partner');
        $security_code = $this->config->get('alipay_cross_border_security_code');
        $alipay_config['partner'] = $partner;
        $alipay_config['key'] = $security_code;

        // verify the request if it is from alipay server
        $alipayNotify = new AlipayNotify($alipay_config);
        
        if (filter_input(INPUT_GET, 'out_trade_no')) {
            // verify request from alipay
            $verify_result = $alipayNotify->verifyReturn();

            if (!$verify_result) {
                log_alipay_result("[$log_id] return request is not valid");
                $this->rendMessage(sprintf($this->language->get('return_request_not_valid'), $order_id, $contact_email, $log_id));

                return;   // excpetion
            }
        } else  {
            // verify request from store payment confirmation
            $verify_result = $alipayNotify->verifyConfirmation(array('order_id' => $order_id));
            if (!$verify_result) {
                log_alipay_result("[$log_id] store payment confirmation request is not valid");
                $this->rendMessage(sprintf($this->language->get('confirmation_request_not_valid'), $order_id, $contact_email, $log_id));

                return;   // excpetion
            }
        }
        
        // Init Order Information
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info) {
            log_alipay_result("[$log_id]order not exist in system");
            $this->rendMessage(sprintf($this->language->get('text_order_not_exist'), $log_id, $order_id));
            
            return;   // excpetion
        }
        
        $order_status_id = $order_info["order_status_id"];

        $alipay_cross_border_order_status_id = $this->config->get('alipay_cross_border_order_status_id');
        $alipay_cross_border_trade_finished = $this->config->get('alipay_cross_border_trade_finished');
        $alipay_cross_border_trade_closed = $this->config->get('alipay_cross_border_trade_closed');
        
        if ($order_status_id == $alipay_cross_border_trade_finished) {
            log_alipay_result("[$log_id] order fishied before");
            $this->rendMessage(sprintf($this->language->get('text_success'), $store_url));
            return;
        }
        
        // set default status
        if (1 > $order_status_id) {
            log_alipay_result("[$log_id]order confirm to status" . $order_status_id);
            $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_order_status_id);
        }

        // check the trade in alipay server using 'single_trade_query' service
        $parameter = array(
            "service" => "single_trade_query",       // query single trade information
            "partner" => $partner,
            "_input_charset" => $alipay_config['input_charset'],
            "out_trade_no" => $this->order_id_prefix . $order_id, //you must change it, this is the NO. related the transaction which you want to query
        );

        $alipay = new AlipayService($alipay_config);
        $alipay_response = $alipay->buildRequestHttp($parameter);
        log_alipay_result("[$log_id]return xml: " . str_replace(array("\r\n", "\r", "\n"), "", $alipay_response));
        
        // fail to curl_exec
        if (!$alipay_response) {
            log_alipay_result("[$log_id] unable to connect to alipay server");
            $this->rendMessage(sprintf($this->language->get('connect_alipay_server_failed'), $order_id, $contact_email, $log_id));
            return ;
        }
        
        $is_success = 'F';
        $trade_status = '';
        
        # parse reponse
        if (preg_match("/<trade_status>(.*)<\/trade_status>/i", $alipay_response, $matches)) {
            $trade_status = $matches[1];
        }

        if (preg_match("/<is_success>([T|F|])<\/is_success>/i", $alipay_response, $matches)) {
            $is_success = $matches[1];
        }

        if ($order_status_id != $alipay_cross_border_trade_finished) {
            if ($is_success == 'T' || $trade_status == 'TRADE_FINISHED') {
                log_alipay_result("[$log_id] order update status to [TRADE_FINISHED]");
                $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_trade_finished);

                //$this->rendMessage(sprintf($this->language->get('text_success'), $store_url));
                $this->response->redirect($this->url->link('checkout/success'));
            } else {
                log_alipay_result("[$log_id] order update status to [TRADE_CLOSED]");
                $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_trade_closed);

                $this->rendMessage(sprintf($this->language->get('text_failed'), $contact_email, $log_id));
            }
        } else {
            log_alipay_result("[$log_id] duplicated operation, update order status to [TRADE_FINISHED]");
            //$this->rendMessage(sprintf($this->language->get('text_success'), $store_url));
            $this->response->redirect($this->url->link('checkout/success'));
        }
    }
    
    public function rendMessage($message) {
        $this->document->setTitle($this->language->get('text_title'));
        $data['heading_title'] = $this->language->get('heading_title');
		$data['alipay_message'] = $message;
//        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/alipay_cross_border_return.tpl')) {
//            $this->template = $this->config->get('config_template') . '/template/extension/payment/alipay_cross_border_return.tpl';
//        } else {
//            $this->template = 'default/template/extension/payment/alipay_cross_border_return.tpl';
//        }
        $this->template = 'extension/payment/alipay_cross_border_return';
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view($this->template, $data));
    }

    /**
     * Asynchronous callback
     * 
     */
    public function notify() {
        $log_id = time();
        log_alipay_result("[$log_id]alipay call notify_url start[" . urldecode(http_build_query($_POST)) . "]");
        
        // Init Alipay Secure Configuration
        require_once("system/library/alipay/cross_border/alipay_config.php");
        $alipay_config['partner'] = $this->config->get('alipay_cross_border_partner');
        $alipay_config['key'] = $this->config->get('alipay_cross_border_security_code');        
        
        // check if the notify is valid from alipay
        $alipay = new AlipayNotify($alipay_config);
        $verify_result = $alipay->verifyNotify();
        
        if (!$verify_result) {
            echo "fail";
            log_alipay_result("[$log_id] verify_failed");

            return;
        }
        
        // success to verify the notify request, just log it 
        log_alipay_result("[$log_id] verify successfullY ------");
        $order_id = filter_input(INPUT_POST, 'out_trade_no');     // order id
        // $trade_no = filter_input(INPUT_POST, 'trade_no');     // transaction id at alipay side
        // $total = filter_input(INPUT_POST, 'total_fee');     // get total price

        // Get Order Detail Information
        if (strpos($order_id, $this->order_id_prefix) !== false) {
            $order_id = substr($order_id, strlen($this->order_id_prefix));
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info) {
            log_alipay_result(sprintf("[$log_id]order[%s] not exist ----------------", $order_id));
            echo "success";
            return;
        }    
        
        // request verify passed
        $order_status_id = $order_info["order_status_id"];
        $alipay_cross_border_order_status_id = $this->config->get('alipay_cross_border_order_status_id');
        $alipay_cross_border_trade_finished = $this->config->get('alipay_cross_border_trade_finished');
        $alipay_cross_border_trade_closed = $this->config->get('alipay_cross_border_trade_closed');

        if (1 > $order_status_id) {
            log_alipay_result("[$log_id]order confirm with status" . $order_status_id);
            $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_order_status_id);
        }

        // avoid duplicated notification request
        $post_trade_status = filter_input(INPUT_POST, 'trade_status');
        if (strcmp($post_trade_status, 'TRADE_FINISHED') == 0 
                || strcmp($post_trade_status, 'TRADE_CLOSED') == 0) {
            if ($order_status_id != $alipay_cross_border_trade_finished) {
                if (strcmp($post_trade_status, 'TRADE_FINISHED') == 0) {
                    log_alipay_result('[$log_id]alipay notify [TRADE_FINISHED]');
                    $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_trade_finished);
                } elseif (strcmp($post_trade_status, 'TRADE_CLOSED') == 0) {
                    log_alipay_result('[$log_id]alipay notify [TRADE_CLOSED]');
                    $this->model_checkout_order->addOrderHistory($order_id, $alipay_cross_border_trade_closed);
                }
            }
        }
        echo "success";
    }
}

?>
