<?php

$alipay_config['sign_type']    = strtoupper('MD5');

$alipay_config['input_charset']= strtolower('utf-8');

$alipay_config['cacert']    = dirname(__FILE__) . DIRECTORY_SEPARATOR .'cacert.pem';

$alipay_config['transport']    = 'http://local.ame-new.com/';
?>