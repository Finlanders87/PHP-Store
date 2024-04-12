<?php require_once("../resources/config.php"); ?>



<?php include(TEMPLATE_FRONT . DS . "header.php"); ?>


<?php

  if(isset($_GET['session_id'])) {


    foreach($_SESSION as $name => $value) {

      if(str_starts_with($name, 'product_')) {

        unset($_SESSION[$name]);
      }

      unset($_SESSION['item_total']);
      unset($_SESSION['item_quantity']);
      unset($_SESSION['checkout_session_id']);
    }

    try{

      $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK_KEY']);
      
      $order = $stripe->checkout->sessions->retrieve($_GET['session_id'], []);
    }
    catch(Exception $exception) {
      redirect('index.php');
      echo $exception->getMessage();  
    }
  }


?>

<div class="container">

  <div class="jumbotron">
  
      <?php  
        
        $customer = $stripe->customers->retrieve($order->customer, [])
      ?>


      <h1 class="text-center">Thank you.</h1>  
      <h3 class="text-center"><?= $customer->name; ?></h3>
  <p class="text-center">We will send you email here <mark><?= $customer->email; ?></mark> <?= $customer->name; ?></p>


  </div>

</div>


<?php include(TEMPLATE_FRONT . DS . "footer.php"); ?>