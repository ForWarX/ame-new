<?php
/**
 * @package		OpenCart
 * @author		Ziqi Miao
 * @copyright	Copyright (c) 2018 - 2022, itcg.ca (https://www.opencart.cn/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.cn
 */
/*
    This class will be used by OpenCart when listing the active payment methods during the checkout process. 
    During this process, OpenCart gathers the list of active payment methods from the back-end, 
    and for each method it'll check if the appropriate model class is available or not. 
    The payment method will be only listed if an associated model class is available.

    The important thing in this setup is the value of the code variable. 
    In our case, we've defined it to ott_alipay, which means that when you select the payment method and press Continue, 
    it will call the payment/ott_alipay URL internally, which eventually sets up the form for our payment gateway.
*/
class ModelExtensionPaymentOTTAlipay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/ott_alipay');

		$aliAuth = new AliAuth();
		$website = $_SERVER['SERVER_NAME'];
		$preSend = '{"website":"'.$website.'"}';
		$ottResponseJson = $aliAuth->toSendOttRequest($preSend);
		$decodes = json_decode($ottResponseJson);
		$resObj = $decodes;
		// $aliAuth->authLog($resObj);
		$status = false;
		if($resObj == null){
			$status = false;
		}else{
			if($resObj->website == $website){
				$status = true;
			}else {
				$status = false;
			}
		}


		$status = true;
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'ott_alipay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('ott_alipay_sort_order')
			);
		}

		return $method_data;
	}
}

class AliAuth{
	public static function authLog($logInfo){
		$myFile = "auth_log.txt";
		$fh = fopen($myFile, 'a+') or die("can't open file");
		// $stringData = $data['ott_alipay_status'];
		fwrite($fh, "log info: " . $logInfo . "\n");//."\n".$_SERVER['SERVER_NAME']."\n");
		fclose($fh);
	}
	public static function toSendOttRequest($preSend){

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://frontapi.itecht.ca/process",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $preSend,//"{\"website\":\"opencart.itecht.ca\"}",
		CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache",
		"content-transfer-encoding: UTF-8",
		"content-type: application/json"
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}

}