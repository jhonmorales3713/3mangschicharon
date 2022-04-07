<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$amount = intval(floatval($_SESSION['order_data']['total_amount'])*100);
$success_url = base_url('order_confirmation/'.$_SESSION['current_order_id']);

$response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
  'body' => '{"data":{"attributes":{"amount":'.$amount.',"redirect":{"success":"'.$success_url.'","failed":"http://3mangs.com/capture"},"type":"gcash","currency":"PHP"}}}',
  'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Basic cGtfdGVzdF9LeUFaV3p2UTJzanNOemRhNGdTWlV3cDI6c2tfdGVzdF9NWmVoc0tHRVZWMXBOajdUVlFBczZhU0c=',
    'Content-Type' => 'application/json',
  ],
]);

$data = json_decode($response->getBody(),TRUE);
//echo $response->getBody();

//print_r($data['data']['attributes']['redirect']['checkout_url']);

header('Location: '.$data['data']['attributes']['redirect']['checkout_url']);
exit();

?>





