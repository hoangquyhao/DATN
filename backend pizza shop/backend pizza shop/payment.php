<?php

include 'config.php';

$config = [
    "appid" => 553,
    "key1" => "9phuAOYhan4urywHTh0ndEXiV3pKHr5Q",
    "key2" => "Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3",
    "endpoint" => "https://sandbox.zalopay.com.vn/v001/tpe/createorder"
];

$embeddata = [
    "merchantinfo" => "embeddata123"
];
$items = [
    ["itemid" => "knb", "itemname" => "kim nguyen bao", "itemprice" => 198400, "itemquantity" => 1]
];
$order = [
    "appid" => $config["appid"],
    "apptime" => round(microtime(true) * 1000), // miliseconds
    "apptransid" => date("ymd") . "_" . uniqid(), // mã giao dich có định dạng yyMMdd_xxxx
    "appuser" => "demo",
    "item" => json_encode($items, JSON_UNESCAPED_UNICODE),
    "embeddata" => json_encode($embeddata, JSON_UNESCAPED_UNICODE),
    "amount" => 5000,
    "description" => "ZalsoPay Intergration Demo",
    "bankcode" => "zalopayapp"
];

// appid|apptransid|appuser|amount|apptime|embeddata|item
$data = $order["appid"] . "|" . $order["apptransid"] . "|" . $order["appuser"] . "|" . $order["amount"]
    . "|" . $order["apptime"] . "|" . $order["embeddata"] . "|" . $order["item"];
$order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

$context = stream_context_create([
    "http" => [
        "header" => "Content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($order)
    ]
]);

$resp = file_get_contents($config["endpoint"], false, $context);
$result = json_decode($resp, true);

foreach ($result as $key => $value) {
    echo "$key: $value<br>";
}
////////////
$response = [
    'returncode' => 1,
    'returnmessage' => '',
    'zptranstoken' => 'ACZqCq8EbISYWICvQiDC-pYw',
    'orderurl' => 'https://qcgateway.zalopay.vn/openinapp?order=eyJ6cHRyYW5zdG9rZW4iOiJBQ1pxQ3E4RWJJU1lXSUN2UWlEQy1wWXciLCJhcHBpZCI6NTUzfQ==',
    'qrcode' => '00020101021226520010vn.zalopay0106180005020300103178469866709256937538620010A00000072701320006970454011899ZP24189O004232140208QRIBFTTA5204739953037045405500005802VN630411D4'
];

if ($response['returncode'] == 1) {
    echo "<h1>Thanh toán thành công!</h1>";
    echo "<p>Mã giao dịch: " . $response['zptranstoken'] . "</p>";
    echo "<p>URL thanh toán: <a href='" . $response['orderurl'] . "' target='_blank'>Nhấn vào đây để thanh toán</a></p>";
    
    // Hiển thị mã QR để khách hàng quét và thanh toán
    echo "<p>Hoặc quét mã QR sau để thanh toán:</p>";
    echo "<img src='https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($response['qrcode']) . "&size=200x200' alt='QR Code' />";
} else {
    echo "<h1>Thanh toán thất bại!</h1>";
    echo "<p>Thông báo lỗi: " . $response['returnmessage'] . "</p>";

}

?>