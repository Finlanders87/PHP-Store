<?php require_once("../../resources/config.php"); ?>

<?php 

if(isset($_GET['delete_product_id'])) {  // Poistaa ja palauttaa samalle sivulle.
    
    $query = query("DELETE FROM products WHERE product_id = " . escape_string($_GET['delete_product_id']) . " ");
    confirm($query);
    
    set_message("Product has been DELETED.");
    
    redirect("index.php?products");
}
else {
    
    redirect("index.php?products");
}



?>
