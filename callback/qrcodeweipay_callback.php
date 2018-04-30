<?php
/**
 * @copyright        2016 opencart.cn - All Rights Reserved
 * @link             http://www.guangdawangluo.com
 * @author           TL <mengwb@opencart.cn>
 * @created          2016-12-12 16:04:00
 * @modified         2016-12-12 16:39:36
 */

// Configuration
if (is_file('../config.php')) {
	require_once('../config.php');
}

$url = HTTP_SERVER . "index.php?route=extension/payment/qrcodeweipay/notifycallback";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('weipay_param'=>$GLOBALS['HTTP_RAW_POST_DATA']));

$output = curl_exec($ch);
curl_close($ch);

echo($output);
