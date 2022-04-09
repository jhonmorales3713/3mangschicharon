<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$amount = intval(floatval($_SESSION['order_data']['total_amount'])*100);
$success_url = base_url('order_confirmation/'.$_SESSION['current_order_id']);
$failed_url = base_url('payment_failed');
$keyword = $_SESSION['payment_keyword'];

$response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
  'body' => '{"data":{"attributes":{"amount":'.$amount.',"redirect":{"success":"'.$success_url.'","failed":"'.$failed_url.'"},"type":"'.$keyword.'","currency":"PHP"}}}',
  'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Basic cGtfbGl2ZV9rOXJDeXRwQWViZWcyS0dOZTFhWnB5aHY6c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w=',
    'Content-Type' => 'application/json',
  ],
]);

$data = json_decode($response->getBody(),TRUE);
//echo $response->getBody();

//print_r($data['data']['attributes']['redirect']['checkout_url']);

//header('Location: '.$data['data']['attributes']['redirect']['checkout_url']);
//exit();

?>

<div class="container">
  <div class="row">
      <div class="col-lg-3 col-md-3 col-sm-12"></div>
      <div class="col-lg-6 col-md-6 col-sm-12">
        <center>
          <img src="<?= base_url('assets/img/gcash_icon.png'); ?>" alt="" width="100%">
          <span>You are about to pay: </span><br><br>
          <strong><?= php_money($_SESSION['order_data']['total_amount']); ?></strong><br><br>
          <a class="btn btn-primary" href="<?= $data['data']['attributes']['redirect']['checkout_url'] ?>">PROCEED</a>
          
        </center>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-12"></div>
  </div>
</div>




