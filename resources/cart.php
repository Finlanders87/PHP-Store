<?php require_once("config.php"); ?>



<?php

if(isset($_GET['add'])) {
    
    
    $query = query("SELECT * FROM products WHERE product_id=" . escape_string($_GET['add']) . "");
    confirm($query);
    
    while($row = fetch_array($query)) {
        
        if($row['product_quantity'] != $_SESSION['product_' . $_GET['add']]) {
            
            $_SESSION['product_' . $_GET['add']] +=1;
            redirect("../public/checkout.php");
        }
        else {
            
            set_message("We only have " . $row['product_quantity'] . " " . "{$row['product_title']}" . " available");
            redirect("../public/checkout.php");
        }
        
    }
    
    //$_SESSION['product_' . $_GET['add']] +=1;
    //
    //redirect("index.php");
}


if(isset($_GET['remove'])) {        //  Vähentää tuotteen ostoskorista. 
 
    $_SESSION['product_' . $_GET['remove']]--;
    
    if($_SESSION['product_' . $_GET['remove']] < 1) {
        
        unset($_SESSION['item_total']);
        unset($_SESSION['item_quantity']);        
        redirect("../public/checkout.php");
    }
    else {
        
        redirect("../public/checkout.php");
    }    
}


if(isset($_GET['delete'])) {        //  Tyhjentää ostoskorin. 

    $_SESSION['product_' . $_GET['delete']] = '0';
    unset($_SESSION['item_total']);
    unset($_SESSION['item_quantity']);
    
    redirect("../public/checkout.php");
    
}



function cart() {           // Näyttää tuotteet.
    
    $total          = 0;
    $item_quantity  = 0;
    $item_name      = 1;
    $item_number    = 1;
    $amount         = 1;
    $quantity       = 1;
    
       
    foreach ($_SESSION as $name => $value){
        
        if($value > 0) {
            
        if(substr($name, 0, 8 ) == "product_") {  // Erottaa sessiot omiin. 8=product_
            
        $length = strlen ($name);
            
        $id = substr($name, 8 , $length);
            
        $query = query("SELECT * FROM products WHERE product_id = " . escape_string($id) . " ");
        confirm($query);
    
            
            while($row = fetch_array($query)){  
        
            $sub = $row['product_price']*$value;
            $item_quantity +=$value;
                
            $product_image = display_image($row['product_image']);
            
            $line_items[] = ['price' => $row['stripe_product_price'], 'quantity' => $value];

            $product = <<<DELIMETER
    
    <tr>
        
        <td>{$row['product_title']}<br>
        
        <a href="item.php?id={$row['product_id']}"><img style="height: 50%;" src='../resources/{$product_image}'></a>
        
        </td>
        <td>{$row['product_price']} &euro;</td>
        <td>{$value}</td>
        <td class="cd-price">{$sub} &euro;</td>
            
    <td>
            
    <a class='btn btn-warning' href="../resources/cart.php?remove={$row['product_id']}">  
    <span class='glyphicon glyphicon-minus'></span></a>
    
    <a class='btn btn-success' href="../resources/cart.php?add={$row['product_id']}">
    <span class='glyphicon glyphicon-plus'></span></a>
    
    <a class='btn btn-danger' href="../resources/cart.php?delete={$row['product_id']}">
    <span class='glyphicon glyphicon-remove'></span></a>
            
    </td>
              
    </tr>
    
    <input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}"> 
    <input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}"> 
    <input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
    <input type="hidden" name="quantity_{$quantity}" value="{$value}">
    
DELIMETER;
        
            echo $product;  // Jotta näyttää tuotteet.
            //echo number_format($sub,2,",",".")." &euro;";

    $item_name++;
    $item_number++;
    $amount++;
    $quantity++;
     
            } // END off while loop



        if(!empty($line_items)){

            try {
            
            $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK_KEY']);

            
            $session = $stripe->checkout->sessions->create([
            'success_url' => 'http://localhost/ecom/public/order-success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/ecom/public/checkout.php',
            'payment_method_types' => ['card', 'alipay'],
            'mode'=>'payment',
            'line_items' => [$line_items]

            ]);

            $_SESSION['checkout_session_id'] =  $session->id;


            }catch(Exception $exception){

            echo $exception->getMessage();
            }
        }

          
    
            
      $_SESSION['item_total'] = $total += $sub;          
      $_SESSION['item_quantity'] = $item_quantity;          
                
    
                        
        }   // <--  END OF if          
                     
        }   // <--  END OF $value
        
    }       // <--  END OF foreach
    
}          // <--  END OF function cart



function show_buy_button(){

    $buy_button = <<< DELIMETER
    
    <button id="btn" type="submit" class="btn btn-primary">Buy Now</button>

DELIMETER;
    
    return $buy_button;
}



function show_paypal() {  // Näyttää ostopainikkeen vain, jos jotain on korissa.
    
    if(isset($_SESSION['item_quantity']) && $_SESSION['item_quantity'] >=1) {
    
    $paypal_button = <<< DELIMETER
    
    <input type="image" name="upload" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online">    
    
DELIMETER;
    
    return $paypal_button;
    }
}





function process_transaction() {
    

    
    if(isset($_GET['tx'])) {
    
    $amount      = $_GET['amt']; 
    $currency    = $_GET['cc']; 
    $transaction = $_GET['tx']; 
    $status      = $_GET['st'];

    $total          = 0;
    $item_quantity  = 0;

    
       
    foreach ($_SESSION as $name => $value){
        
        if($value > 0) {
            
        if(substr($name, 0, 8 ) == "product_") {  // Erottaa sessiot omiin. 8=product_
            
        $length = strlen ($name) - 8;   
        $id = substr($name, 8 , $length);
        
        $send_order = query("INSERT INTO orders (order_amount, order_transaction, order_currency, order_status) VALUES ('{$amount}','{$transaction}','{$currency}','{$status}')");
    
        $last_id = last_id();  // last_id() = function.     
        confirm($send_order);
            
        $query = query("SELECT * FROM products WHERE product_id = " . escape_string($id) . " ");
        confirm($query);
    
            
            while($row = fetch_array($query)){  
            
            $product_price = $row['product_price'];
            $product_title = $row['product_title'];
            $sub = $row['product_price']*$value;
            $item_quantity +=$value;
                
            
                
            $insert_report = query("INSERT INTO reports (product_id, order_id, product_title, product_price, product_quantity) VALUES ('{$id}','{$last_id}', '{$product_title}','{$product_price}','{$value}')");
    
            confirm($insert_report);
     
            }
       
      $total += $sub;          
      $item_quantity;          
                
    
                        
        }            
                     
        }   // <--  END OF $value
        
    }       // <--  END OF foreach
        
    session_destroy();
        
    }       // <--  END OF if
    
    else {
    
    redirect("index.php");
    }
    
}          // <--  END OF function cart



?>