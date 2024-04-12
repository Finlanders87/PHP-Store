<?php 



// webhook.php
//
// Use this sample code to handle webhook events in your integration.
//
// 1) Paste this code into a new file (webhook.php)
//
// 2) Install dependencies
//   composer require stripe/stripe-php
//
// 3) Run the server on http://localhost:4242
//   php -S localhost:4242



if($_SERVER['REQUEST_METHOD'] === 'POST'){

  require_once("../resources/config.php");

  
}



// The library needs to be configured with your account's secret key.
// Ensure the key is kept out of any version control system you might be using.
$stripe = new \Stripe\StripeClient('sk_test_...');

// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = 'whsec_fadb963e853f080ec3567aabaa5d572ae63868083b632a04f648b8ad4ddfd707';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}


// Handle the event
switch ($event->type) {
  case 'payment_intent.succeeded':
  //  $paymentIntent = $event->data->object;
  // ... handle other event types
  break;




  case 'charge.succeeded':

    $order_amount       = $event->data->object->amount;
    $order_currency     = $event->data->object->currency;
    $order_transaction  = $event->data->object->balance_transaction;
    $order_status       = $event->data->object->status;
    $stripe_session_id  = $event->id;

    $order_query = query("INSERT INTO 
    orders(stripe_session_id, order_amount, order_transaction, order_status, order_currency)
    VALUES('$stripe_session_id', $order_amount, '$order_transaction', '$order_status' ,'$order_currency')
    ");

    confirm($order_query);

    break;




    // ob_flush();
    // ob_start();
    // var_dump($event->data);
    // file_put_contents("event.txt", ob_get_flush());




  case 'checkout.session.completed':

    $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK_KEY']);


    $session_items = $stripe->checkout->sessions->allLineItems($event->data->object->id, ['limit'=> 25]);

    foreach($session_items as $product_ordered){

      $product = $stripe->products->retrieve($product_ordered->price->product);

      $product_title              = $product->name;
      $stripe_product_customer_id = $event->data->object->customer;
      $stripe_product_id          = $product_ordered->price->product;
      $stripe_price_id            = $product_ordered->price->id;
      $product_price              = $product_ordered->price->unit_amount;
      $product_quantity           = $product_ordered->quantity;
      $product_total              = $product_ordered->amount_total;

      $reports_query_result = query("INSERT INTO reports(
          stripe_product_customer_id, 
          stripe_product_id, 
          stripe_price_id, 
          product_price,
          product_title, 
          product_quantity , 
          product_total)
          VALUES(
            '$stripe_product_customer_id', 
            '$stripe_product_id', 
            '$stripe_price_id', 
            $product_price,
            '$product_title', 
            $product_quantity, 
            $product_total)");

      confirm($reports_query_result);
    }

  break;




  default:
    echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);






?>