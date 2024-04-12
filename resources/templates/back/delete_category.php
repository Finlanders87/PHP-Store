<?php require_once("../../resources/config.php"); ?>

<?php 

if(isset($_GET['delete_category_id'])) {  // Poistaa ja palauttaa samalle sivulle.
    
    $query = query("DELETE FROM categories WHERE cat_id = " . escape_string($_GET['delete_category_id']) . " ");
    confirm($query);
    
    set_message("Category has been DELETED.");
    
    redirect("index.php?categories");
}
else {
    
    redirect("index.php?categories");
}



?>
