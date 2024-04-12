<?php require_once("../../resources/config.php"); ?>

<?php 

if(isset($_GET['delete_order_id'])) {  // Poistaa ja palauttaa samalle sivulle.
    
    $query = query("DELETE FROM orders WHERE order_id = " . escape_string($_GET['delete_order_id']) . " ");
    confirm($query);
    
    set_message("Order has been DELETED.");
    
    redirect("index.php?orders");
}
else {
    
    redirect("index.php?orders");
}



?>




