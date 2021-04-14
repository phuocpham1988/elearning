<?php
echo "<pre>";
print_r($_GET);

if ($_GET) {
	$serectkey = "UINn02456kivQMk52mQAUQKiVhZUM5a1";
	$partnerCode = $_GET['partnerCode'];
    $accessKey = $_GET['accessKey'];
    $requestId = $_GET['requestId'];
    $amount = $_GET['amount'];
    $orderId = $_GET['orderId'];
    $orderInfo = $_GET['orderInfo'];
    $orderType = $_GET['orderType'];
    $transId = $_GET['transId'];
    $message = $_GET['message'];
    $localMessage = $_GET['localMessage'];
    $responseTime = $_GET['responseTime'];
    $errorCode = $_GET['errorCode'];
    $payType = $_GET['payType'];
    $extraData = $_GET['extraData'];
	$signature = $_GET['signature'];
	
	$rawHash_result = "partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&amount=$amount&orderId=$orderId&orderInfo=$orderInfo&orderType=$orderType&transId=$transId&message=$message&localMessage=$localMessage&responseTime=$responseTime&errorCode=$errorCode&payType=$payType&extraData=$extraData";
	$signature_result = hash_hmac("sha256", $rawHash_result, $serectkey);
	
	if ($signature == $signature_result) {
		echo "<h1 style='color:blue'>Thanh toan thanh cong<h1>";
	} else {
		echo "<h2 style='color:red'>Thanh toan khong thanh cong<h2>";
	}
}

?>