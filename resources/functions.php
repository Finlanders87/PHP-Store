<?php

$upload_directory = "uploads";       // Kuvien polku. 


                                // APU FUNCTIONS




function last_id() {
    
    global $connection;
    
    return mysqli_insert_id($connection);
    
}


function set_message($msg){
    
    if(!empty($msg)){
        
        $_SESSION['message'] = $msg;
    }
    else {
        
      $msg = "";  
    }
}


function display_message(){
    
    if(isset($_SESSION['message'])) {
        
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}


// REDIRECT

function redirect($location) {
    
    header("Location: $location ");
}



function query($sql) {
    
    global $connection;
    
    return mysqli_query($connection, $sql);
}


// TARKASTUS

function confirm($result) {
    
    global $connection;
    
    if(!$result) {
        die("QUERY FAILED" . mysqli_error($connection));
    }    
}



function escape_string($string) {
    
    global $connection;
    
    return mysqli_real_escape_string($connection, $string);
}


function fetch_array($result) {
    
    return mysqli_fetch_array($result);
}



                        /********* FRONT END FUNCTIONS   *********/    


            // Hakee tietokannasta tietyn lohkon esim. 'products'.
function count_all_records($table) {
   
    // Haetaan tuotteet tietokannasta.
    $query = query(" SELECT * FROM " . $table);
    
    // Tarkistetaan että toimii.
    confirm($query);
    
    // Kuinka monta tuotetta on " rows, eli riviä tietokannassa. "
    return mysqli_num_rows($query);
    
            // Sama asia kuin ylhäällä.
    // return mysqli_num_rows(query('SELECT * FROM' . $table));
}


                // Mitä varastossa on myynnissä.

function count_all_products_in_stuck() {
    
    return mysqli_num_rows(query('SELECT * FROM products WHERE product_quantity >=1'));
}
    
    
    
                            /*********** SIVULUETTELO ********************/



function get_products_whit_pagination($perPage = "6") {
    

    $rows = count_all_products_in_stuck();
    
    if(isset($_GET['page'])) {
        
        $page = preg_replace('#[^0-9]#', '', $_GET['page']);
    }
    else {
        
        $page = 1;
    }
    
    // Kuinka monta tuotetta on per. sivu.
    
    $lastPage = ceil($rows / $perPage);
    
    
    if($page < 1) {
        $page = 1;
    }
    
    elseif($page > $lastPage){
        $page = $lastPage;
    }
    
    
    // ALA NUMEROT
    
    $middleNumbers = '';
    
    
    $sub1 = $page - 1;
    $sub2 = $page - 2;
    $add1 = $page + 1;
    $add2 = $page + 2;
    
    if($page == 1){
        
                        // Millä sivulla ollaan.
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
        
                        // Seuraavan sivun nappula.
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
        
    }
    
                                    // Edellinen sivu
    elseif($page == $lastPage) {
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'">' .$sub1. '</a></li>';
        
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
    }
    
    
                        // Keskimmäinen sivu, kun on sivuja on enemmän kuin 3.
    
    elseif ($page > 2 && $page < ($lastPage -1)) {
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub2.'">' .$sub2. '</a></li>';
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'">' .$sub1. '</a></li>';
        
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add2.'">' .$add2. '</a></li>';
    }
    
    
            // Kun on 3 sivua.
    
    elseif($page > 1 && $page < $lastPage) {
        
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'">' .$sub1. '</a></li>';
        
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';

        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
        
        //echo "<ul class='pagination'>{$middleNumbers}</ul>";
    }
    
    $limit = 'LIMIT ' . ($page-1) * $perPage . ',' . $perPage;
    
    
                    // Saadaan tuotteet joita on varastossa
    
    $query2 = query(" SELECT * FROM products WHERE product_quantity >= 1 " . $limit);
    confirm($query2);
    
    $outputPagination ="";
    
    
    
    
                /***** Back - näppäin *******/
    
    if($page != 1){
        
        $prev = $page - 1;
        
        $outputPagination .='<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$prev.'">Back</a></li>';
    }
    
    
    $outputPagination.= $middleNumbers;

    
    
                /***** Next - näppäin *******/
    
    if($page != $lastPage){
        
        $next = $page + 1;
        
        $outputPagination .='<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page= '.$next.'">Next</a></li>';    
    }
    
    
    
    
    
    // Hakee tietokannan tiedot.
    while($row = fetch_array($query2)) {
    
    $product_image = display_image($row['product_image']);


     
    $product = <<<DELIMETER
    
         <div class="col-sm-4 col-lg-4 col-md-4">
             <div class="thumbnail">
             
    <a href="item.php?id={$row['product_id']}"><img style="height: 220px;" src='../resources/{$product_image}'></a>
    
                 <div class="caption">
                 
                     <h4 class="pull-right">{$row['product_price']}&euro;</h4>
                     <h4><a href="item.php?id={$row['product_id']}">{$row['product_title']}</a>
                     </h4>
                     
                     <p>See more snippets like this online store item at <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.</p>
                     
                     <p class='text-center'>
                     <a class="btn btn-primary" target="_blank" href="../resources/cart.php?add={$row['product_id']}">Add to cart</a>
                     
                     <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                     </p>
                     
                 </div>
             
             </div>
         </div>    
DELIMETER;
        
    echo $product;
    }
    
                    // Ala selaus numerot. 
    
    echo "<div class='text-center' style='clear: both;'><ul class='pagination'>{$outputPagination}</ul></div>";    
}


                    /*************** END OF BUTTONS **********************/








function get_products_in_cat_page() {
    
    // Haetaan tuotteet tietokannasta.
    $query = query(" SELECT * FROM products WHERE product_category_id = " . escape_string($_GET['id']) . " AND product_quantity >= 1");
    
    // Tarkistetaan että toimii.
    confirm($query);
    
    // Hakee tietokannan tiedot.
    while($row = fetch_array($query)) {

    $product_image = display_image($row['product_image']);

    $product = <<<DELIMETER
    
            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <a href="item.php?id={$row['product_id']}"><img style="height: 50%;" src='../resources/{$product_image}'></a>
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        <p>
                            <a href="../recources/card.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> 
                            <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>
DELIMETER;
    echo $product;
    }
}


function get_products_in_shop_page() {
    
    // Haetaan tuotteet tietokannasta.
    $query = query(" SELECT * FROM products WHERE product_quantity >= 1");
    
    // Tarkistetaan että toimii.
    confirm($query);
    
    // Hakee tietokannan tiedot.
    while($row = fetch_array($query)) {

    $product_image = display_image($row['product_image']);

        
    $product = <<< DELIMETER
    
            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                
                    <a href="item.php?id={$row['product_id']}"><img style="height: 50%;" src='../resources/{$product_image}'></a>
                    
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        <p>
                            <a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> 
                            <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>
DELIMETER;
    echo $product;
    }
}


function get_categories(){


$query = query("SELECT * FROM categories");
confirm($query);

    while($row = fetch_array($query)) {


    $categories_links = <<<DELIMETER

    <a href='category.php?id={$row['cat_id']}' class='list-group-item'>{$row['cat_title']}</a>


DELIMETER;

    echo $categories_links;

     }
}



function login_user() {
    
    if(isset($_POST['submit'])) {
        
        $username = escape_string($_POST['username']);
        $password = escape_string($_POST['password']);
        
    $query = query("SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}'");
    confirm($query);
        
        if(mysqli_num_rows($query) == 0) {
            
            set_message("You Password or Username are wrong.");
            redirect("login.php");
        }
        else{
            $_SESSION['username'] = $username;
            redirect("admin");
        }
    }       
}

function send_message() {
    
    if(isset($_POST['submit'])) {
        
        $to         = "someEmailaddress@gmail.com"; // Mihin viesti menee.
            
        $from_name  = $_POST['name'];
        $subject    = $_POST['subject'];
        $email      = $_POST['email'];
        $message    = $_POST['message'];
        
        $headers = "From: {$from_name} {$email}";
        
        $result = mail($to, $subject, $message , $headers);
        
        if(!$result) {
            
            set_message("Sorry we could not send your message.");
            redirect("contact.php");
        }
        else {
            
            set_message("Your message haas been send.");
            redirect("contact.php");
        }
    }
}



                        /*********  BACK END FUNCTIONS  *********/  



        /****  ADMIN ORDERS  ****/


function display_orders(){
    
    $query = query("SELECT * FROM orders");
    confirm($query);
    
    while($row = fetch_array($query)) {
        
        $orders = <<< DELIMETER
        
        <tr>
            <td>{$row['order_id']}</td>
            <td>{$row['order_amount']}</td>
            <td>{$row['order_transaction']}</td>
            <td>{$row['order_currency']}</td>
            <td>{$row['order_status']}</td>
            
            <td><a class="btn btn-danger" 
            href="index.php?delete_order_id={$row['order_id']}">
            <span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>        
        
        
DELIMETER;
            
    echo $orders;
        
    }
        
}



        /****  ADMIN PRODUCTS PAGE ****/  



function display_image($picture) {
    
    global $upload_directory;
    
    return $upload_directory . DS . $picture;    
}




function get_products_in_admin() {
    
    
    // Haetaan tuotteet tietokannasta.
    $query = query(" SELECT * FROM products");
    
    // Tarkistetaan että toimii.
    confirm($query);
    
    // Hakee tietokannan tiedot.
    while($row = fetch_array($query)) {
    
    $category = show_product_category_title($row['product_category_id']);
        
    $product_image = display_image($row['product_image']);
        
    $product = <<<DELIMETER
    
        <tr>
        
            <td>{$row['product_id']}</td>
            <td>{$row['product_title']}<br>
              <a href="index.php?edit_product&id={$row['product_id']}"><img width='110' src="../../resources/{$product_image}" alt=""></a>
            </td>
            <td>{$category}</td>
            <td>{$row['product_price']}</td>
            <td>{$row['product_quantity']}</td>
            <td><a class="btn btn-danger" 
            href="index.php?delete_product_id={$row['product_id']}">
            <span class="glyphicon glyphicon-remove"></span></a></td>
            
        </tr> 
DELIMETER;
        
    echo $product;    
 
    
    }
    
}




function show_product_category_title($product_category_id) {
    
    $category_query = query("SELECT * FROM categories WHERE cat_id = '{$product_category_id}'");
    confirm($category_query);
    
    while($category_row = fetch_array($category_query)) {
        
        return $category_row['cat_title'];
    }
}





        /****  ADD PRODUCTS IN ADMIN  ****/


function add_product() {
    
    if(isset($_POST['publish'])) {
        
        $product_title         = escape_string ($_POST['product_title']);
        $product_category_id   = escape_string ($_POST['product_category_id']);
        $product_price         = escape_string ($_POST['product_price']);
        $product_description   = escape_string ($_POST['product_description']);
        $short_desc            = escape_string ($_POST['short_desc']);
        $product_quantity      = escape_string ($_POST['product_quantity']);
        

        $two_zeros_price = number_format($product_price, decimals:2, decimal_separator:'',thousands_separator:'00');


        // Miten saadaan kuvat ladattua.
        $product_image          = $_FILES['file']['name'];
        $image_temp_location    = $_FILES['file']['tmp_name'];
        
        // Siirretään ladatut kuvat uploads-kansioon.
        move_uploaded_file($image_temp_location , UPLOAD_DIRECTORY . DS . $product_image);
        
    $query = query("INSERT INTO products(product_title, product_category_id, product_price, product_description, short_desc, product_quantity, product_image) VALUES('{$product_title}', '{$product_category_id}', '{$product_price}', '{$product_description}', '{$short_desc}', '{$product_quantity}', '{$product_image}')");
    $last_id = last_id();
        
    confirm($query);

    try{
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK_KEY']);

        $product = $stripe->products->create([

            'name' => $product_title,
            'description' => $product_description,
            'metadata' => [

                'product_id' => $last_id,
                'quantity' => $product_quantity,
                'short_description' => $short_desc

            ]
        ]);

        $stripe_product_id = $product->id;
        
        $stripe_product_price = $stripe->prices->create([

            'unit_amount'=> $two_zeros_price,
            'currency'=>'eur',
            'product'=> $product->id

        ]);

        $stripe_product_price_id = $stripe_product_price->id;

        $updateProductWhitStripeId = query("UPDATE products SET stripe_product_price = '$stripe_product_price_id', stripe_product_id = '$stripe_product_id' WHERE product_id = $last_id");

        confirm($updateProductWhitStripeId);

    }catch(Exception $exception){

        echo $exception->getMessage();
    }


    set_message("New product whit id {$last_id} has been added.");
    redirect("index.php?products");
    
    }   
}





function show_categories_add_product_page() {
    
             // Haetaan tietokannasta Categoriat.
    $query = query("SELECT * FROM categories");
    confirm($query);
    
    while($row = fetch_array($query)) {

        $categories_option = <<<DELIMETER
        
        <option value="{$row['cat_id']}">{$row['cat_title']}</option>

DELIMETER;
    
    echo $categories_option;
    }
}



            /******* UPDATING PRODUCT *********/


function update_product() {
    
    if(isset($_POST['update'])) {
        
        $product_title         = escape_string ($_POST['product_title']);
        $product_category_id   = escape_string ($_POST['product_category_id']);
        $product_price         = escape_string ($_POST['product_price']);
        $product_description   = escape_string ($_POST['product_description']);
        $short_desc            = escape_string ($_POST['short_desc']);
        $product_quantity      = escape_string ($_POST['product_quantity']);
        
        // Miten saadaan kuvat ladattua.
        $product_image          = $_FILES['file']['name'];
        $image_temp_location    = $_FILES['file']['tmp_name'];
        
        
        
        // Jos ei oo uutta kuvaa.
        if(empty($product_image)) {
            
            $get_pic = query("SELECT product_image FROM products WHERE product_id =" .escape_string($_GET['id']). " ");
            
            confirm($get_pic);
            
            while($pic = fetch_array($get_pic)) {
                
                $product_image = $pic['product_image'];
            }
        }
        
        
        
        // Siirretään ladatut kuvat uploads-kansioon.
        move_uploaded_file($image_temp_location , UPLOAD_DIRECTORY . DS . $product_image);
        
    $query = "UPDATE products SET ";
    $query .= "product_title        = '{$product_title}'        , ";
    $query .= "product_category_id  = '{$product_category_id}'  , ";
    $query .= "product_price        = '{$product_price}'        , ";
    $query .= "product_description  = '{$product_description}'  , ";
    $query .= "short_desc           = '{$short_desc}'           , ";
    $query .= "product_quantity     = '{$product_quantity}'     , ";
    $query .= "product_image        = '{$product_image}'          ";
    $query .= "WHERE product_id=" . escape_string($_GET['id']) ;
        
        
        
    
    $send_update_query = query($query);       
    confirm($send_update_query);
        
    set_message("Product has been Updated.");
    redirect("index.php?products");
    
    }   
}

                /****************   CATEGORIES IN ADMIN *************/ 



function show_categories_in_admin() {
    
    $category_query = query("SELECT * FROM categories");
    confirm($category_query);
    
    while($row = fetch_array($category_query)) {
        
        $cat_id     = $row['cat_id'];
        $cat_title  = $row['cat_title'];
        
    $category = <<<DELIMETER
    
        <tr>
            <td>{$cat_id}</td>
            <td>{$cat_title}</td>
            <td><a class="btn btn-danger" 
            href="index.php?delete_category_id={$row['cat_id']}">
            <span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
        
DELIMETER;
        
    echo $category;
    } 
}



function add_category() {
    
    if(isset($_POST['add_category'])) {
        
        $cat_title = escape_string($_POST['cat_title']);
        
                // Jos kenttä on tyhjä.
        if(empty($cat_title || $cat_title == " ")) {
            
            echo "<p class='bg-danger'>This cannot be empty.</p>";
        }
        else {
            
        $insert_cat = query("INSERT INTO categories(cat_title) VALUES('{$cat_title}') ");
        confirm($insert_cat);
        
        set_message("Category has been added.");
        }
    }
}



                        /****************  ADMIN USERS *******************/


function display_users() {
    
    $users_query = query("SELECT * FROM users");
    confirm($users_query);
    
    while($row = fetch_array($users_query)) {
        
        $user_id        = $row['user_id'];
        $username       = $row['username'];
        $email          = $row['email'];
        $password       = $row['password'];
        
    $user = <<<DELIMETER
    
        <tr>
            <td>{$user_id}</td>
            <td>{$username}</td>
            <td>{$email}</td>
            <td><a class="btn btn-danger" 
            href="index.php?delete_user_id={$row['user_id']}">
            <span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
        
DELIMETER;
        
    echo $user;
    } 
}


function add_user() {
    
    if(isset($_POST['add_user'])) {
        
        $username   = escape_string($_POST['username']);
        $email      = escape_string($_POST['email']);
        $password   = escape_string($_POST['password']);
        //$user_photo = escape_string($_FILES['file']['name']);
        //$photo_temp = escape_string($_FILES['file']['tmp_name']);
        
        
    //move_uploaded_file($user_photo, UPLOAD_DIRECTORY . DS . $user_photo);
        
    $query = query("INSERT INTO users(username,email,password) VALUES('{$username}', '{$email}','{$password}' )");
    
    confirm($query);
    set_message("New User has been created.");
    redirect("index.php?users");
        
    }    
}



function get_reports() {
    
    
    // Haetaan tuotteet tietokannasta.
    $query = query(" SELECT * FROM reports");
    
    // Tarkistetaan että toimii.
    confirm($query);
    
    // Hakee tietokannan tiedot.
    while($row = fetch_array($query)) {


                    // Saadaan desimaalimerkki
        $product_price = add_2_decimals($row['product_price']);
        $product_total = add_2_decimals($row['product_total']);
   
        try {

            if(!empty($row['stripe_product_customer_id'])) {

                $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK_KEY']);

                $customer = $stripe->customers->retrieve($row['stripe_product_customer_id'], []);



    $report = <<<DELIMETER
    
        <tr>
        
            <td>{$row['report_id']}</td>
            <td>$customer->name</td>
            <td>$customer->email</td>
            <td>{$product_price}<br>
            <td>{$row['product_title']}<br>
            <td>{$row['product_quantity']}</td>
            <td>{$product_total}</td>

            <td><a id="delete-report-button" class="btn btn-danger" 
            href="index.php?delete_report_id={$row['report_id']}">
            <span class="glyphicon glyphicon-remove"></span></a></td>
            
        </tr> 
DELIMETER;
        
    echo $report;


            }
        }
        catch(Exception $exception) {

            echo $exception->getMessage();

        }

   
 
    
    }
    
}



                        /****************  GET SLIDES *******************/


function add_slides() {
    
    if(isset($_POST['add_slide'])) {
        
        $slide_title        = escape_string($_POST['slide_title']);
        $slide_image        = escape_string($_FILES['file']['name']);
        $slide_image_loc    = escape_string($_FILES['file']['tmp_name']);
        
        if(empty($slide_title) || empty($slide_image)) {
        
        echo "<p class='bg-danger'> This field cannot be empty.</p>";
        }
        else {
            
        move_uploaded_file($slide_image_loc, UPLOAD_DIRECTORY . DS . $slide_image);
        
        $query = query("INSERT INTO slides(slide_title, slide_image) VALUES('{$slide_title}', '{$slide_image}')");
        confirm($query);
        set_message("Slide has been Added.");
        redirect("index.php?slides");
        }
    }
}


function get_current_slide_in_admin() {
    
    $query = query("SELECT * FROM slides ORDER BY slide_id DESC LIMIT 1");
    confirm($query);
    
    while($row = fetch_array($query)) {
        
    $slide_image = display_image($row['slide_image']);
    
    $slide_active_admin = <<<DELIMETER
        
            <img class="img-responsive" src="../../resources/{$slide_image}" alt="">
        
        
DELIMETER;
        
    echo $slide_active_admin;
    }   
}


function get_active_slide() {
    
    // Saadaan viimeisin julkaistu kuva.
    $query = query("SELECT * FROM slides ORDER BY slide_id DESC LIMIT 1");
    confirm($query);

    while($row = fetch_array($query)) {
        
    $slide_image = display_image($row['slide_image']);
    
    $slide_active = <<<DELIMETER
        
        <div class="item active">
            <img style="height:550px" class="slide-image" src="../resources/{$slide_image}" alt="{$slide_image}">
        </div>    
        
        
DELIMETER;
        
    echo $slide_active;
    }    
}




function get_slides() {
    
    $query = query("SELECT * FROM slides");
    confirm($query);
        

    
    while($row = fetch_array($query)) {
        
    $slide_image = display_image($row['slide_image']);
    
    $slides = <<<DELIMETER
        
        <div class="item">
            <img style="height:550px" class="slide-image" src="../resources/{$slide_image}" alt="{$slide_image}">
        </div>    
        
        
DELIMETER;
        
    echo $slides;
    }
}



function get_slide_thumbnails() {
    
    $query = query("SELECT * FROM slides ORDER BY slide_id ASC ");
    confirm($query);
    
    while($row = fetch_array($query)) {
        
    $slide_image = display_image($row['slide_image']);
    
    $slide_thumb_admin = <<<DELIMETER

    <div class="col-xs-6 col-md-3 image_container">
    
        <div class="caption">
            <p>{$row['slide_title']}</p>
        </div>
        
        <a href="index.php?delete.slide_id={$row['slide_id']}">
            <img class="img-responsive slide_image" src="../../resources/{$slide_image}" alt="{slide_image}">
        </a>
        
        
        
    </div>        
              
DELIMETER;
        
    echo $slide_thumb_admin;
    }   
}    





?>