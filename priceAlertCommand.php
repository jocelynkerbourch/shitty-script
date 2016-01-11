#!/usr/bin/php
<?php

define('TWILIO_ACCOUNT_SID','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('TWILIO_AUTH_TOKEN','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('FROM','+336xxxxxxxx');

define('TO','+336xxxxxxxx');

$headers = array('User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');

$urljson = file_get_contents(__DIR__."/priceAlertCommand.json");
$json = json_decode($urljson, true);

foreach ($json as $key=>$product){
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL, $product["url"]);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	$html = curl_exec($curl);
	curl_close($curl);

	preg_match_all($product["regex"], $html, $prices);

	$send=false;
	if (array_key_exists(1, $prices)){
		$price = floatval(str_replace(",", ".", trim($prices[1][0])));
		if ($price<$product["alert"]){
			$message = sprintf('Price change for %s : %01.2f',$product["label"],$price);
			$send = true;
		}else{
			$message = sprintf("Price not change for %s",$product["label"]);
		}
	}else{
		$message = sprintf("No price in %s",$product["url"]);
	}

	if ($send){
		$data = array (
    			'From' => FROM,
    			'To' => TO,
    			'Body' => $message,
		);
		$post = http_build_query($data);
		$curlSms = curl_init(sprintf("https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json",TWILIO_ACCOUNT_SID));
		curl_setopt($curlSms, CURLOPT_POST, true);
		curl_setopt($curlSms, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlSms, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlSms, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curlSms, CURLOPT_USERPWD, sprintf("%s:%s",TWILIO_ACCOUNT_SID,TWILIO_AUTH_TOKEN));
		curl_setopt($curlSms, CURLOPT_POSTFIELDS, $post);
		$sms = curl_exec($curlSms);
		curl_close($curlSms);
	}

	echo sprintf("%s \n",$message);
}
