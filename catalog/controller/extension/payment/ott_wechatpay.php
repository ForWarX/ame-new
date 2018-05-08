<?php
/**
 * @package		OpenCart
 * @author		Ziqi Miao
 * @copyright	Copyright (c) 2018 - 2022, itcg.ca
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.cn
 */

// error_reporting(-1);
class ControllerExtensionPaymentOTTWechatpay extends Controller {
	// check out page wechatpay option
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['redirect'] = $this->url->link('extension/payment/ott_wechatpay/qrcode');

		return $this->load->view('extension/payment/ott_wechatpay', $data);
	}

	// qrcode page for user to scan and pay
	public function qrcode() {
		$this->load->language('extension/payment/ott_wechatpay');

		$this->document->setTitle($this->language->get('heading_title'));
		// $this->document->addScript('catalog/view/javascript/qrcode.js');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_qrcode'),
			'href' => $this->url->link('extension/payment/ott_wechatpay/qrcode')
		);
		/* gather infos*/
		/*
		info list:
		1. order id
		2. total payment
		3. call back url
		4. biz_type 
		5. action 
		6. verion 
		7. merchant id 
		8. sign key 

		*/
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		/* No. 1 */
		$order_id = trim($order_info['order_id']);
		$data['order_id'] = $order_id;
		$orderId =  date('YmdHis').$order_id;

		/* No. 2*/
		$total_amount = $order_info['total'];
		$totalPayment = $total_amount;
		$amount = $totalPayment * 100;

		/* No. 3*/
		$notify_url = "http://americo.itecht.ca/americoWechatpay";//$this->url->link('extension/payment/ott_wechatpay/callback');
		$call_back_url = $notify_url;

		/* No. 4*/
		$biz_type = "WECHATPAY";

		/* No. 5 */
		$action = "ACTIVEPAY";

		/* No. 6 */
		$version = "1.0";

		/* No. 7*/
		$mch_id = $this->config->get('ott_wechatpay_merchant_id');
		$merchant_id = $mch_id;
		
		/* No. 8*/		
		$sign_key = $this->config->get('ott_wechatpay_sign_key');
		$key = $sign_key;


		/* modify data */
		$wechatPaymentInfo = new WechatPaymentInfo();
		$wechatSecurity = new WechatSecurity();
		$md5hash = $wechatPaymentInfo->organizeInfo($orderId,$call_back_url,$biz_type,$amount); 
		$aesMd5String = md5($md5hash.$key);
		$aesStringRaw = $wechatPaymentInfo->my_substr_function($aesMd5String,8,24);  
		$aesKey = mb_strtoupper($aesStringRaw, "UTF-8");
		//----------------------------
		$dataBlockJson = '{'
			.'"amount":"'.$amount.'",'
			.'"biz_type":"'.$biz_type.'",'
			.'"order_id":"'.$orderId.'",'
			.'"call_back_url":"'.$call_back_url.'"'
			.'}';
 
		$aesClass = new OTTWechatAES($dataBlockJson, $aesKey,128,'ECB');
		$value = $aesClass->encrypt();
 
		$dataInput = $value;
		$md5 = $md5hash;
		$preSend = '{"data":"'.$dataInput.'","action":"'.$action.'","merchant_id":"'.$merchant_id.'","version":"'.$version.'","md5":"'.$md5.'"}';


		$isSendHttp = true;
		$qrcode_url='';
 //check session created or not
		if (!isset($this->session->data['ott_wechatpay_orderId'])){
			$this->session->data['ott_wechatpay_orderId'] = "";
		  }
		if($this->session->data['ott_wechatpay_orderId'] != $orderId){
			//set session order id
			$this->session->data['ott_wechatpay_orderId'] = $orderId;
			if($isSendHttp){ 
				$ottResponseJson = $wechatSecurity->toSendOttRequest($preSend);
				$resObj = json_decode($ottResponseJson);
	
				$resMd5hash = $resObj->md5;
				$resAesMd5String = md5($resMd5hash.$key);
				$resAesStringRaw = $wechatPaymentInfo->my_substr_function($resAesMd5String,8,24);
				$resAesKey = mb_strtoupper($resAesStringRaw, "UTF-8");
	
				$aesClass = new OTTWechatAES($resObj->data, $resAesKey,128,'ECB');
				$resOutput = $aesClass->decrypt();
	
				$cleanOutput = $wechatPaymentInfo->escape_sequence_decode($resOutput);
	
				$cleanOutputObj = json_decode($cleanOutput);
	
				$qrcode_url = $cleanOutputObj->code_url;
				// $_SESSION['currentOttALIPAYQrcode'] = $qrcode_url;
			}else {
				// $qrcode_url = $_SESSION['currentOttALIPAYQrcode'];
			}
			$data['code_url'] = $qrcode_url;

			
			$this->session->data['ott_wechatpay_code_url'] = $data['code_url'];
		}else {
			//same order id
			//set code_url from session
			$data['code_url'] = $this->session->data['ott_wechatpay_code_url'];
		}

		if ($data['code_url'] =='') {
			$data['error_warning'] = 'No OTT QR code or connection timeout, please try again!';
		} else {
			$data['error_warning'] = '';
		}
		$data['notify_url'] = $notify_url;
		$data['heading_title'] = $this->language->get('heading_title');
		//text_qrcode_description
		$data['text_qrcode_description'] = $this->language->get('text_qrcode_description');
		$data['action_success'] = $this->url->link('checkout/success');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/payment/ott_wechatpay_qrcode', $data));
	}

	public function isOrderPaid() {
		$json = array();

		$json['result'] = false;

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];

			$this->load->model('checkout/order');
			/**/
			// $resOrderId = $order_id;
			// $americo_date = date('Ymd');
			// $format_americo_date = mb_substr($americo_date,2);
			// $preOrderId = 'AME' . $format_americo_date;
			// $unformat_orderId = $resOrderId;//get orderid from session
			// $format_raw_orderId = substr($unformat_orderId, -4);
			// $format_orderId = $preOrderId . $format_raw_orderId;
			// $order_id = $format_orderId;
			/**/
			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info['order_status_id'] == $this->config->get('ott_wechatpay_completed_status_id')) {
				$json['result'] = true;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	// public function callback(){
	// 	$raw_post = file_get_contents( 'php://input' );
	// 	$decoded  = json_decode( $raw_post );
	// 	$this->load->model('checkout/order');
	// 	$resOrderId = $decoded->orderid;//$raw_post['orderid'];


	// 			//----- form americo order id start-----------


	// 			$americo_date = date('Ymd');
	// 			$format_americo_date = mb_substr($americo_date,2);
	// 			$preOrderId = 'AME' . $format_americo_date;
	// 			$unformat_orderId = $resOrderId;//get orderid from session
	// 			$format_raw_orderId = substr($unformat_orderId, -4);
	// 			$format_orderId = $preOrderId . $format_raw_orderId;
	// 			// $resOrderId = $format_orderId;
		
	// 			//----- form americo order id end-----------

	// 	$order_info = $this->model_checkout_order->getOrder($resOrderId);

	// 	$myFile = "ott_wechatpay_callback_log.txt";
	// 	$fh = fopen($myFile, 'a+') or die("can't open file");
	// 	$stringData = $raw_post;
	// 	fwrite($fh, date('YmdHis') . ' ' . $stringData."\n order_status_id:". $order_info["order_status_id"]."\nott_status_id".$this->config->get('ott_wechatpay_completed_status_id')."\n");
	// 	fclose($fh);

	// 	if ($order_info) {
	// 		// echo 'order_info<br>';
	// 		$order_status_id = $order_info["order_status_id"];
	// 		// echo 'order_status_id<br>' . $order_status_id;
	// 		if (!$order_status_id) {
	// 			// echo 'order_status_id<br>' . $order_status_id;
	// 			$this->model_checkout_order->addOrderHistory($resOrderId, $this->config->get('ott_wechatpay_completed_status_id'));
	// 		}
	// 	}
	// }

	public function callback() {
		// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
		//----------- get response from OTT -------------
		$raw_post = file_get_contents( 'php://input' );
		//----------- change json to object -----------------
		$decoded  = json_decode( $raw_post );
	
		$myFile = "ott_wechatpay_callback_log.txt";
		$fh = fopen($myFile, 'a+') or die("can't open file");
		$stringData = $raw_post;
		fwrite($fh, $stringData."\n");
		fclose($fh);
		//----------- start decryption --------------------
		if(!isset($decoded->md5)){
			exit;
		}
		// if (isset($msgArray->sciID)) {
		// 	echo "Has sciID";
		// }
		$resMd5hash = $decoded->md5;
		$resData = $decoded->data;
		$key =$this->config->get('ott_wechatpay_sign_key');
		$resAesMd5String = md5($resMd5hash.$key);
		$wechatPaymentInfo = new WechatPaymentInfo();
		$wechatSecurity = new WechatSecurity();
		$resAesStringRaw = $wechatPaymentInfo->my_substr_function($resAesMd5String,8,24);
		$resAesKey = mb_strtoupper($resAesStringRaw, "UTF-8");
		$aesClass = new OTTWechatAES($resData, $resAesKey,128,'ECB');
		$resOutput = $aesClass->decrypt();
		$cleanOutput = $wechatPaymentInfo->escape_sequence_decode($resOutput);
		$cleanOutputObj = json_decode($cleanOutput);
		// var_dump($cleanOutputObj);
		//----------- get order id after decoding ----------------------
		$resOrderId = mb_substr($cleanOutputObj->order_id,14);

		//----- form americo order id start-----------


		// $americo_date = date('Ymd');
		// $format_americo_date = mb_substr($americo_date,2);
		// $preOrderId = 'AME' . $format_americo_date;
		// $unformat_orderId = $resOrderId;//get orderid from session
		// $format_raw_orderId = substr($unformat_orderId, -4);
		// $format_orderId = $preOrderId . $format_raw_orderId;

		//----- form americo order id end-----------


		// $resOrderId = '43';
		//----------- write response to local file -----------------------
		// $myFile = "ott_wechatpay_callback_log.txt";
		// $fh = fopen($myFile, 'a+') or die("can't open file");
		// $stringData = $raw_post;
		// fwrite($fh, $stringData."\n");
		// fclose($fh);

		$mch_id = $this->config->get('ott_wechatpay_merchant_id');
		$merchant_id = $decoded->merchant_id;
		// echo 'mch_id(config value): ' . $mch_id;
		// echo 'merchant_id(response value)' . $merchant_id;
		//----------------------
		
		$rsp_code =$decoded->rsp_code;
		if($rsp_code == 'SUCCESS'){
			if($merchant_id == $mch_id){
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($resOrderId);
				if ($order_info) {
					// echo 'order_info<br>';
					$order_status_id = $order_info["order_status_id"];
					// echo 'order_status_id<br>' . $order_status_id;
					if (!$order_status_id) {
						// echo 'order_status_id<br>' . $order_status_id;
						$this->model_checkout_order->addOrderHistory($resOrderId, $this->config->get('ott_wechatpay_completed_status_id'));
					}
				}
			}
		}
		
		exit;
	}
}


class WechatPaymentInfo{
	public $myObj;
	//$object;
	public function storeObj($obj){
		$myObj->order_id = $obj->order_id;//{'order_id'};
		$myObj->call_back_url = $obj->call_back_url;//{'call_back_url'};
		$myObj->biz_type = $obj->biz_type;//{'biz_type'};
		$myObj->amount = $obj->amount;//{'amount'};
		$myObj->action = $obj->action;//{'action'};
		$myObj->version = $obj->version;//{'version'};
		$myObj->merchant_id = $obj->merchant_id;//{'merchant_id'};
		
	}
	public function getObject(){
		return $myObj;
	}
	/**
	 * check order id is available from the request 
	 * 
	 * @return true if order_id is setted, 
	 * @return false if order_id is not setted, and have to set order id by server
	 */
	public function checkOrderId($order_id){
		$holder = trim($order_id);
		if($holder !==''){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * create order id for the request 
	 * 
	 * 
	 * @return value 
	 */
	public function creatOrderId(){
		$milliseconds = round(microtime(true) * 1000);
		return "ACT".$milliseconds;
	}

	public function organizeInfo($order_Id,$call_back_url,$biz_type, $amountInput){
		$orderId = $order_Id;
		$callbackUrl = $call_back_url;
		$bizType = $biz_type;
		$amount = $amountInput;
	

		$md5Input = $amount.$bizType.$callbackUrl.$orderId;
		$md5String = md5($md5Input);
		$md5Uppercase =mb_strtoupper($md5String, "UTF-8"); //strtoupper($md5string);
		return $md5Uppercase;
	}

	public function getOrderId($orderId){
		$checkOrderId = WechatPaymentInfo::checkOrderId($orderId);
		//create order id for user
		if($checkOrderId){
			
		}else{
			$orderId= WechatPaymentInfo::creatOrderId();
		}

		return $orderId;
	}

	function my_substr_function($str, $start, $end)
	{
	  return mb_substr($str, $start, $end - $start);
	}

	function escape_sequence_decode($str) {
		
		// [U+D800 - U+DBFF][U+DC00 - U+DFFF]|[U+0000 - U+FFFF]
		$regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})
					|\\\u([\da-fA-F]{4})/sx';
	
		return preg_replace_callback($regex, function($matches) {
	
			if (isset($matches[3])) {
				$cp = hexdec($matches[3]);
			} else {
				$lead = hexdec($matches[1]);
				$trail = hexdec($matches[2]);
	
				// http://unicode.org/faq/utf_bom.html#utf16-4
				$cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
			}
	
			// https://tools.ietf.org/html/rfc3629#section-3
			// Characters between U+D800 and U+DFFF are not allowed in UTF-8
			if ($cp > 0xD7FF && 0xE000 > $cp) {
				$cp = 0xFFFD;
			}
	
			// https://github.com/php/php-src/blob/php-5.6.4/ext/standard/html.c#L471
			// php_utf32_utf8(unsigned char *buf, unsigned k)
	
			if ($cp < 0x80) {
				return chr($cp);
			} else if ($cp < 0xA0) {
				return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
			}
	
			return html_entity_decode('&#'.$cp.';');
		}, $str);
	}
}// end \WechatPaymentInfo class



class WechatSecurity {
	public static function encrypt($input, $key) {
	$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$input = WechatSecurity::pkcs5_pad($input, $size);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$dataOutput = mcrypt_generic($td, $input);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$dataOutput = base64_encode($dataOutput);
	return $dataOutput;
	}
	
	private static function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}
	
	public static function decrypt($sStr, $sKey) {
		// error_reporting(E_ALL ^ E_DEPRECATED);
		$decrypted= mcrypt_decrypt(
		MCRYPT_RIJNDAEL_128,
		$sKey,
		base64_decode($sStr),
		MCRYPT_MODE_ECB
		);
	
		$dec_s = strlen($decrypted);
		$padding = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		return $decrypted;
	} 

	public static function toSendOttRequest($preSend){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_PORT => "443",//"8081",
			CURLOPT_URL => "https://frontapi.ottpay.com:443/process",//"http://uatapi.ottpay.com:8081/process",//"https://frontapi.ottpay.com:443/process"
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			//CURLOPT_USERAGENT => $agent,
			CURLOPT_POSTFIELDS => $preSend, 
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-transfer-encoding: UTF-8",
				"content-type: application/json",
				//"postman-token: faef04ba-faaa-21fa-538d-a24d2f2997c7"
			),
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			
			if ($err) {
			return "cURL Error #:" . $err;
			} else {
				//var_dump($response);
				return $response;
				//var_dump($ottResponseJson);
			}
	}
}// end \WechatSecurity class

/*
$var = new ClassName();
$var->method();


$aesClass = new AES();
$aesClass->setData();
$aesClass->setKey();
$aesClass->setMethode(128,'ECB');
$aesClass->decrypt();

*/

/**
*Aes encryption
*/
class OTTWechatAES {
   
    protected $key;
    protected $data;
    protected $method;
    /**
     * Available OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
     *
     * @var type $options
     */
    protected $options = 0;
    /**
     * 
     * @param type $data
     * @param type $key
     * @param type $blockSize
     * @param type $mode
     */
    function __construct($data = null, $key = null, $blockSize = null, $mode = 'CBC') {
        $this->setData($data);
        $this->setKey($key);
        $this->setMethode($blockSize, $mode);
    }
    /**
     * 
     * @param type $data
     */
    public function setData($data) {
        $this->data = $data;
    }
    /**
     * 
     * @param type $key
     */
    public function setKey($key) {
        $this->key = $key;
    }
    /**
     * CBC 128 192 256 
     * CBC-HMAC-SHA1 128 256
     * CBC-HMAC-SHA256 128 256
     * CFB 128 192 256
     * CFB1 128 192 256
     * CFB8 128 192 256
     * CTR 128 192 256
     * ECB 128 192 256
     * OFB 128 192 256
     * XTS 128 256
     * @param type $blockSize
     * @param type $mode
     */
    public function setMethode($blockSize, $mode = 'CBC') {
        if($blockSize==192 && in_array('', array('CBC-HMAC-SHA1','CBC-HMAC-SHA256','XTS'))){
            $this->method=null;
             throw new Exception('Invlid block size and mode combination!');
        }
        $this->method = 'AES-' . $blockSize . '-' . $mode;
    }
    /**
     * 
     * @return boolean
     */
    public function validateParams() {
        if ($this->data != null &&
                $this->method != null ) {
            return true;
        } else {
            return FALSE;
        }
    }
//it must be the same when you encrypt and decrypt
     protected function getIV() {
		//  return '';
        // return '1234567890123456';
         //return mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
         return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
     }
    /**
     * @return type
     * @throws Exception
     */
    public function encrypt() {
        if ($this->validateParams()) { 
            return trim(openssl_encrypt($this->data, $this->method, $this->key, $this->options,$this->getIV()));
        } else {
            throw new Exception('Invlid params!');
        }
    }
    /**
     * 
     * @return type
     * @throws Exception
     */
    public function decrypt() {
        if ($this->validateParams()) {
		   $ret=openssl_decrypt($this->data, $this->method, $this->key, $this->options,$this->getIV());
          
           return   trim($ret); 
        } else {
            throw new Exception('Invlid params!');
        }
    }
}