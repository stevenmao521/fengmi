<?php
/** Error reporting */
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);

header('Content-Type: text/html; charset=utf-8');
include_once('mysql.php');

$db = include_once(__DIR__.'/../../app/database.php');
$db_helper = new mysql($db['hostname'], $db['username'], $db['password'], $db['database'], $conn, 'utf8');

//$res = $db_helper->query("select * from clt_parkplace");
//$arr = $db_helper->fetch_array($res);

$receipt = $_REQUEST;
if ($receipt == null) {
    $receipt = file_get_contents("php://input");
}
if ($receipt == null) {
    $receipt = $GLOBALS['HTTP_RAW_POST_DATA'];
}

$post_data = $this->xml_to_array($receipt);
$postSign = $post_data['sign'];

#返回信息
$ordernumber = $post_data['out_trade_no'];
$total_fee = $post_data['total_fee'];  
$open_id = $post_data['openid'];  
$time = $post_data['time_end'];
$addtime = time();

#订单id
#$ordernumber = '2018062010210110';
$res = $db_helper->query("select * from clt_parkorder where order_sn='{$ordernumber}'");
$order = $db_helper->fetch_assoc($res);
$orderid = $order['id'];

if ($post_data['return_code'] == 'SUCCESS' && $postSign) {
    $columnName = "";
    $value = "";
    $db_helper->query("insert into clt_wxback (order_sn,total_fee,open_id,time,addtime) values ('{$ordernumber}',{$total_fee},'{$open_id}','{$time}','{$addtime}')");
    
    $url = "http://{$_SERVER['HTTP_HOST']}/api/Callback/paysuc";
    $postData = array("orderid"=>$orderid);
    $return = $db_helper->curlPost($url, $postData);
    echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
} else {
    // 写个日志记录  
    file_put_contents('wxpayerrorlog.txt', $post_data['return_code'] . PHP_EOL, FILE_APPEND);
    echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
}
?>